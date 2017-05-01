<?php
//require("../Router.class.php");
require("../../autoload.php");
use PHPUnit\Framework\TestCase;

use Xodebox\Router as Router;
use Xodebox\MappedItem as MappedItem;
final class RouterTest extends TestCase{
    
    public function testRouter(){
        $routerMap = [
            [
                'url'        => 'posts',
                'controller' => 'Post',
                'action'     => 'index'
            ],

            [
                'url'         => 'posts/:param',
                'controller'  => 'Post',
                'action'      => 'show'
            ],
            [
                'url' => 'hello/:id1/:id2',
                'controller' => 'Post',
                'action' => 'show'
            ]
        ];
        
        //$r = new Router();
        try{
            //$_GET['url'] = 'hello/12/32';
            // $_GET['url'] = 'posts/2';
            $_GET['url'] = 'posts';
            $url = $_GET['url'];
            print "\n$url \n";
            //Router::route();
            $r = new Router();
            print "\n";
            //$r->route();

            //$route = Xodebox\RouterFactory::createRoute($routerMap);

            $r->addRouteMap($routerMap);
            $r->route();
            
            //print "\n". $ctrl['index'];
            //print $r->getRegExp("posts/:param1");
            //var_dump($r->getRouteMap());
            //$m = RouteMapItem::create($routerMap[0]);
            //var_dump($m);
        }catch(\Exeption $ex){
            print $ex->getMessage();
        }
    }
    
}

?>