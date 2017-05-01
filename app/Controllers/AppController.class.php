<?php
use Xodebox\Controller;
use Xodebox\View;

class AppController extends Controller{
    private $loginSession = null;
    private $current_user = null;
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
    public function register($param){
        extract($this->getParams($param));
        
        $view = View::loadView("user/register.php");
        
        if(isset($name) && isset($password)){
            $user = new User();
            $user->name = $name;
            $user->setPassword($password);
            $user->save();
        }
        $view->display();
    }

    /**
     * Main screen
     **/
    public function main(){
         $view = View::loadView("user/main.html");
         $view->addData(['user' => $this->getCurrentUser()]);
		 $view->display();
    }

    /**
     * Login
     **/
    public function login($p){
        extract($this->getParams($p));
        
        $view = View::loadView("user/index.php");
        if(isset($name) && isset($password)){
            $user = User::where(['name' => $name]);
            if(!empty($user)){
                $user = $user[0];
                if($user->checkPassword($password) ){
                    $this->createNewSession($user->id);
                    $this->redirectTo('index');   //@TODO Give appropriate error messages
                }else{
                    $this->redirectTo('index'); //@TODO Give appropriate error messages
                }
            }else{
                $this->redirectTo('index');
            }
            return;            
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
    private function createNewSession($user_id){
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user_id;//
    }

    /**
     * Unauthorize the user (Log out)
     **/
    private function clearSession(){
        unset($_SESSION['logged_in']);   
    }

    public function getCurrentUser(){
        if(!$this->isLoggedIn())
            return false;
        return User::fetch($_SESSION['user_id']);
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