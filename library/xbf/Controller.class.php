<?php
namespace Xodebox;

class Controller{
    protected $model  = null;
    protected $action = null;
    protected $view   = null;

    private $functions = [];

    private $pre_methods = [];
    private $session_var = null;
    
    private $sa = "as";
    /** Callback for pre-execution code **/
    //public function _before_(){
        //foreach($this->before as $func){
            //$this->{$func}();
        //}
    //    return;
    //}

    protected function start_session(){
        session_start();
    }

    protected function clear_session(){
        session_stop();
    }

    public function __construct($action=null){
        $this->pre_methods = static::before_methods();
        
        $this->setView(new View());  //Note use dependancy injector for this in the future
    }

    public function setView($v){
        $this->view = $v;
    }
    
    /**
     * Constructor uses late static binding to call the child before_method, otherwise this method will be called.
     **/
    protected static function before_methods(){
        return [];
    }

    /**
     * Controller calls the child before, otherwise this method will be called.
     * NOTE: This method must return true, otherwise controller will not execute the called method.
     **/
    protected function before(){
    }

    /**
     * Used by the router to call prehook
     **/
    public function isPrehooked($method_name){
        //print "method: $method_name\n";
        //var_dump($this->pre_methods);
        if(in_array($method_name, $this->pre_methods))
            return true;
        return false;
    }

    public function call($func, $args=null){
        $ret = true;
        //var_dump($args);
        if(isset($args['SESSION']))
            var_dump($args['SESSION']);
        if($this->isPrehooked($func))
            $ret = $this->before();
        if($ret == true)
            $this->{$func}($args);

    }

    /*
    public function __get($name){
        if($name == "call"){
            //var_dump($this);
            return new CallHandler($this);
        }
        else
            throw new \Exception("Undefined property $name");
            }*/
    /*
    public function Route($controller, $action, $params){
        Router
        }*/

    public function view(){
        return $this->view();
    }

    public function redirectTo($page){
        $home = Config::$home_dir;
        $root = "http://$_SERVER[HTTP_HOST]$home/$page";
        //header_remove();
        ?>
        <script type="text/javascript">
        window.location.href = '<?=$root?>';
        </script>
<?php
        //   exit(header("Location: $root"));
    }

    function json_decode($s) {
        /*$s = str_replace(
            ['"',  "'"],
            ['\"', '"'],
            $s
            );*/
        //print $s;
        $s = preg_replace('/(\w+):/i', '"\1":', $s);
        return json_decode($s);
    }
}

?>