<?php

namespace Xodebox;

class Image{
    private $data = null;
    private $filename = null;

    public function readFile($name){
        
    }    
}


/**
 * 
 * Set file_uploads = on [php.ini]
 *
 **/
class ImageManager{
    
    public function __construct(){
        
    }

    public static function getTargetFile($fileData){
        if(isset($fileData['name']))
            return self::getdir() . basename($fileData['name']);
        else
            return null;
               
    }

    //public static function retrieveImageURL($filename){
        //return {
    // }

    public static function saveFile($fileData, $target = null){
        if($target == null)
            $target = self::getTargetFile($fileData);
        return move_uploaded_file($fileData['tmp_name'],  $target);
    }
    
    public static function checkFile($fileData){
        // Check if image file is a actual image or fake image
        $check = getimagesize($fileData['tmp_name']);
        if($check !== false) 
            return true;
         else 
            return false;
    }

    public static function getdir(){
        return Config::$image_dir."/";
    }
}

?>
