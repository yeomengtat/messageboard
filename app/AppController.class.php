<?php
use Xodebox\Controller;

class AppController extends Controller{

    public function index(){
        $view = View:loadView("user/index.html");
        //print "Hi. There is nothing here yet.";
        $view->display();
    }
}
    
?>