<?php
namespace Xodebox;

Config::$app_dir   = dirname(__FILE__)."/../app/";
Config::$view_dir  = "Views";

Config::$image_dir = "images";
Config::$upload_dir = "uploads";
Config::$asset_dir = "assets";
//Config::$home_dir  = "";   //Write the project directory path relative to web server root if the project path is not the server.
Config::$debug_mode = true;

error_reporting(E_ERROR);
?>
