<?php
/**
 * Xodebox Web Framework Bootstrapper.
 */
require_once "library/autoload.php";
use Xodebox\Router;
use Xodebox\ExceptionHandler;

$exceptionHandler = new ExceptionHandler();
                  
Xodebox\Config::$home_dir = dirname($_SERVER['SCRIPT_NAME']);
Xodebox\Config::$asset_dir = dirname($home_dir) . $asset_dir; 
//print Xodebox\Config::$home_dir;

//$home_dir = get_dir();
function reportingMode(){

}

$router = new Router(Xodebox\Config::$routerConfig);
//Give router URL and ask for controller name
$result = $router->route();

//if(!$result)
//    print "Router: Controller not found. Make sure router is properly configured </br>";


?>
