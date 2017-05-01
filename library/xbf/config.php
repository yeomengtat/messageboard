<?php
/**
 * Loads the configuration file if it exists	
 *
 * Config php file should be defined as follows:
 * <code>
 *   namespace BinaryTreeVisualizer;
 *   Config::  $db_config = array(
 *   'host' => "localws",
 *   'pass' => "123",
 *   'name' => 'mlm_database',
 *   'user' => 'root'
 *    );
 * </code>
 **/
namespace Xodebox;

class Config{
    public static $dbConfig = [
        'host' => 'localhost',
        'pass' => '123',
        'user' => 'root',
        'name' => 'invoice_management'
    ];
    public static $app_dir  = "";
    public static $routerConfig = [];
    public static $view_dir  = "";
    public static $asset_dir = "../assets";
    public static $home_dir  = "";	//Keep the contents away from the front end view
    public static $image_dir = "";
    public static $upload_dir = "";
    public static $debug_mode = false;
}

$path = dirname(__FILE__).'/';

if ( file_exists($path.'../../config/config.php') )
    require_once ($path. '../../config/config.php');
if ( file_exists($path.'../../config/db/config.php') )
    require_once ($path. '../../config/db/config.php');

if ( file_exists($path.'../../config/router.php') )
    require_once ($path. '../../config/router.php');


?>