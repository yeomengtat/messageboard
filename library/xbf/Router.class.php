<?php
namespace Xodebox;

class RouteMapItem{
    private $url        = null;
    private $controller = null;
    private $action = null;
    private $param  = [];
    private $regexp = null;
    private $variables = null;
    private $var_pos = null;

    public function __toString(){
        return "RouterItem[URL: {$this->url}, Controller: {$this->controller}, Action: {$this->action}]";
    }
    
    public function set(Array $arg = []){
        foreach($arg as $key => $item){
            if($key == 'url'){
                $this->url    = $item;
                $this->createRegexp();
            }
            if($key == 'action')
                $this->action = $item;
            if($key == 'param')
                $this->param  = $item;
            if($key == 'controller')
                $this->controller  = $item;
        }
    }

    public function get(){
        return [ 'url'    => $this->url,
                 'action' => $this->action,
                 'param'  => $this->param,
                 'controller' => $this->controller
        ];
    }

    public function getControllerName(){
        return $this->controller;
    }

    public function getPattern(){
        return $this->regexp;
    }

    public function getVariableName($index){
        if($index < 0 || $index > count($this->variables)-1)
            return  null;
        return $this->variables[$index];
    }

    public function getVariablePosition($name){
        if(isset($this->var_pos[$name]))
            return $this->var_pos[$name];
        return null;
    }

    /**
     * Static constructor
     **/
    public static function create($arg = []){
        $m = new self();
        $m->set($arg);
        return $m;
    }

    public function createRegexp(){
        if($this->url != null){
            $regexp = Router::getRegExp($this->url);
            $this->regexp    = $regexp['pattern'];
            $this->variables = $regexp['variables'];
            $this->var_pos   = $regexp['pos'];            
            return true;
        }
        return false;
    }
}

class RouterFactory{
    public static function createRoute($param){        
        if(is_array(array_values($param)[0]))     //NOTE: Works in PHP5.4+
        {
            $ret = [];
            foreach($param as $arg){
                $ret []= RouteMapItem::create($arg);
            }
            return $ret;
        }
        elseif (is_array($param))
            return RouteMapItem::create($param);
        
    }
}

class Router{
    private $routeMap = [];
    private $fallback = [];
    private $appdir = ".";
    private $assetdir = "";
    private $controller_dir = "Controller";
    private $view_dir;
    private $controller_class_pattern = "{\$cname}Controller";
    private $homedir = "";

    /**
     * TODO LOAD CONFIG FILE FROM config/router.php
     **/
    public function __construct($routerConfig = null){
        $this->appdir = dirname(__FILE__)."/../../app";
        \AutoLoader::addpath("../app/Controllers");
        \AutoLoader::addpath("../app/Models");
        
        if(isset($routerConfig['map']))
            $this->addRouteMap($routerConfig['map']);
        if(isset($routerConfig['fallback']))
            $this->fallback = $routerConfig['fallback'];
        
        $this->assetdir = \Xodebox\Config::$asset_dir;
        $this->homedir  = \Xodebox\Config::$home_dir;
        $this->view_dir  = \Xodebox\Config::$view_dir;
        $this->app_dir  = \Xodebox\Config::$app_dir;
        
    }

    public function addRouteMap($item){
        if( is_array($item[0])){
            $prepend = RouterFactory::createRoute($item);
            $this->routeMap = array_merge($this->routeMap, $prepend);
            return true;
        }
        if($item instanceof RouteMapItem){
            $this->routeMap []= $item;
            return true;
        }
        return false;            
    }

    public function getRouteMap(){
        return $this->routeMap;
    }

    /**
     * Performs the routing method
     **/
    public function route(){
        if(!isset($_GET['url'])){
            //throw new \Exception("Router failed: Could not catch the URL.");
            //print "Router failed: Could not catch the URL \n";
            //TODO redirect to 404 page.
            //$this->getFallback('404');
            //return false;
            $_GET['url'] = 'index';
        }        
        $url = $_GET['url'];
        $url = rtrim($url, "/");
        //print $url[0];
        
        $routerResult = $this->findRoute($url);

        //Route found
        if($routerResult){
            $route      = $this->routeMap[$routerResult['index']];
            $params     = $routerResult['params'];
            //$post       = $routerResult
            if(isset($_POST))
                $params['POST'] = $_POST;
            if(isset($_SESSION))
                $params['SESSION'] = $_SESSION;
            $controller = $this->getController($route);
            $action     = $route->get()['action'];
            $this->callController($controller, $action, $params);
            return true;
        }
        print $this->getFallback('404');            

        //TODO:Try the asset resolver if it fails initially
        
        return false;
        //$controller = $r->getController
    }

    /**
     * Finds a matching route map from the given url parameter
     * Returns an array consisting of route map index and parameters mapped
     * @return Array [$index, $params]
     **/
    public function findRoute($url){
        $words = explode("/", $url);
        //print "\n";

        $found = false;
        $retIndex = 0;
        foreach($this->routeMap as $index => $route){
            //$pattern = $this->getRegExp($route->get()['url']);
            $pattern = $route->getPattern();
            preg_match($pattern, $url, $matches);
            if(count($matches)>0){
                //print $pattern;
                //print($this->routeMap[$index]);
                $found = true;
                $retIndex = $index;
                array_shift($matches);
                break;
            }
            //print "$url ?= $pattern \n";
        }

        $params = [];

        if(!isset($matches))
            return null;

        $c = 0;
        foreach($matches as $index => $match){
            $var_name = $route->getVariableName($c);
            $var_pos  = $route->getVariablePosition($var_name);
            if($index == $var_pos){
                $params[$route->getVariableName($c)] = $match;
                $c++;
            }
        }
        
        if($found)
            return ['index' => $retIndex, 'params' => $params];
        return null;                                     
    }

    public static function getRegExp($pattern){
        $sep  = "\/";
        $xsep = "[^\/]";
        $var = "(.*)";
        $words = explode("/", $pattern);

        $rexp = "";
        $len = count($words);
        $vars = [];
        $var_pos = [];
        foreach($words as $i => $word){
            if($word[0] == ':'){
                //$rexp .= "(.*)";
                $rexp .= "([0-9]+)";
                $var_name = trim($word, ":");
                $vars[] = $var_name;
                $var_pos[$var_name] = $i;
            }
            elseif($word[0] == '$'){
                $rexp .= "([\w]+)";
                $var_name = trim($word, '$');
                $vars[] = $var_name;
                $var_pos[$var_name] = $i;
            }
            else
                $rexp .= "($word)";
            if($i < $len-1)
                $rexp .= $sep;
        }
        
        $rexp = "/^{$rexp}$/";
        return ['pattern' => $rexp, 'variables' => $vars, 'pos' => $var_pos];
    }
    
    /**
     * Identifies and returns proper Controller Name and Action using the route item parameter. 
     **/
    public function getController(RouteMapItem $item){
        $cname = $item->getControllerName();
        $class_pattern = $this->controller_class_pattern;
        $controllerName = str_replace('{$cname}', $cname, $class_pattern);
        $action = $item->get()['action'];
        //print "$controllerName : $action \n";
     
        $controller = new $controllerName();
        return $controller;
    }

    /**
     * Calls the controller method
     *
     **/
    public static function callController( $c, $action, $params=null){
        if(method_exists($c, $action)){
            $c->call($action, $params);
            /*
            if($c->isPrehooked($action))
                $c->before();
                $c->{$action}($params);*/
        }
        else{
            $class_name = get_class($c);
            print "Method $action does not exists in the controller $class_name.";
        }
    }

    public function getFallback($str){
        if(isset($this->fallback[$str]) && file_exists("{$this->app_dir}/{$this->view_dir}/".$this->fallback[$str]) )            
        {
            require "{$this->app_dir}/{$this->view_dir}/".$this->fallback[$str];
        //print "app/View/".$this->fallback[$str];
        }else{
            return "Nothing to see here. Please report the following error: <br>Router error: Misconfigured fallback content. ";
        }
    }

    public static function redirectTo($url, $statusCode = 303){
        header("Location: {$url}", true, $statusCode); 
        exit();
    }
}

?>
