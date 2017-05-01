<?php
use Xodebox\Controller;
use Xodebox\View;

class AppController extends Controller{

    public function index(){
       $view = View::loadView("user/index.php");
	  $view->display(); 
    }
}
    
?>