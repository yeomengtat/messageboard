<?php
use Xodebox\Controller;
use Xodebox\View;

class AppController extends Controller{
    private $loginSession = null;
    public function __construct(){
        session_start();        
    }

    /**
     * The index page
     **/
    public function index(){
        if($this->isLoggedIn()){
            //main screen
            $this->main();
        }else{
            //Login Screen
            $this->login();
        }
    }

    /**
     * Register screen
     **/
    public function register(){
        print "register screen";
    }

    /**
     * Main screen
     **/
    public function main(){
        print "You are logged in";
    }

    /**
     * Login
     **/
    public function login(){
        $view = View::loadView("user/index.php");
        $view->display();
    }



    /**
     * Returns true when user is logged in
     **/
    private function isLoggedIn(){
        if(isset($_SESSION['logged_in'])){
            $this->loginSession = true;
        }else{
            $this->loginSession = false;
        }
    }
}
    
?>