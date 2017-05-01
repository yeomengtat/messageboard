<?php
namespace Xodebox;
use Xodebox\Template;

class View{
    private $name     = null;
    private $app_dir  = null;
    private $view_dir = null;
    private $file = null;
    private $data = [];

    private $template = null;
    private static $shared_vars = [];

    /**
     * Todo: use dependancy manager and remove template engine constructor from here.
     **/
    public function __construct($config = null, $templateEngine = null){
        if(!isset($name))
            $name = "view";
        $this->name    = $name;
        if($config == null){
            $this->app_dir  = \Xodebox\Config::$app_dir;
            $this->view_dir = \Xodebox\Config::$view_dir;            
        }else{
            if(isset($config['view_dir']))
               $this->view_dir = $config['view_dir'];
            if(isset($config['app_dir']))
               $this->app_dir  = $config['app_dir'];
            if(isset($config['file']))
                $this->file   = $config['file'];               
        }
        
        
        //Construct template if not provided. 
        $cwf = "{$this->app_dir}{$this->view_dir}/{$this->file}";
        $this->template = new Template($cwf);
        $this->template->set('srv_base', \Xodebox\Config::$home_dir);  // This is a global variable
	$this->template->set('ass_dir', \Xoddebox\Config::$asset_dir);
        foreach(self::$shared_vars as $var => $val){
            $this->template->set($var, $val);
        }
    }

    public function set($var, $value){
        if($this->template == null)
            return null;
        $this->template->set($var, $value);
        return true;
    }

    public function addData(Array $val){
        $this->data = array_merge($this->data, $val);
    }

    public function addVar(){

    }
    
    public function render(){
        //extract($this->data);
        $this->template->setData($this->data);
        //ob_start();
        //include($cwf);
        $content =  $this->template->output();
        //$content = ob_get_contents();
        //ob_end_clean()
        //print $content;
        return $content;
        //print "TODO: render {$this->app_dir}/{$this->view_dir}/{$this->file}";
    }

    public function display(){
        print $this->render();
    }

    public static function loadView($path){
        $config = ['app_dir'  => \Xodebox\Config::$app_dir,
                   'view_dir' => \Xodebox\Config::$view_dir,
                   'home_dir' => \Xodebox\Config::$home_dir
        ];
        if (!is_array($path))
            $config ['file']= $path;
        else
            $config = $path;
        
        return new self($config);
    }

    /**
     * Shared variables can be used amongst all active views
     **/
    public static function setShared($name, $value){
        self::$shared_vars[$name] = $value;
    }
}

?>
