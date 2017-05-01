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
        $view = View::loadView("user/register.php");
        //if($isset
        $view->display();
    }

    /**
     * Main screen
     **/
    public function main(){
         $view = View::loadView("user/main.html");
		 $view->display();
    }

    /**
     * Login
     **/
    public function login($p){
        extract($this->getParams($p));
        
        $view = View::loadView("user/index.php");
        if(isset($name) && isset($password)){
            if($name == 'admin' && $password == '123'){
                $this->createNewSession();
                $this->redirectTo("");
                return;
            }
        }
        $view->display();
    }

    /**
     * Logout action
     **/
    public function logout(){
        $this->clearSession();
		$this->redirectTo('index');
    }
    

    /**
     * Authorize the user
     **/
    private function createNewSession(){
        $_SESSION['logged_in'] = true;
        //$_SESSION['user_id'] = ;//
    }

    /**
     * Unauthorize the user (Log out)
     **/
    private function clearSession(){
        unset($_SESSION['logged_in']);   
    }



    /**
     * Returns true when user is logged in
     **/
    private function isLoggedIn(){
        if(isset($_SESSION['logged_in'])){
            $this->loginSession = true;
            return true;
        }else{
            $this->loginSession = false;
            return false;
        }
    }
}
    
?>