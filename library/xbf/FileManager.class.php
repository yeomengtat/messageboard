<?php

namespace Xodebox;

/*class Image{
    private $data = null;
    private $filename = null;

    public function readFile($name){
        
    }    
    }*/


/**
 * 
 * Set file_uploads = on [php.ini]
 *
 * TODO: Filter types of files that can be uploaded by the user.
 **/
class FileManager{
    private static $target_dir = null;
    
    public function __construct(){
        //$this->target_dir = Config::$upload_dir;
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

    public static function deleteFile($fileName, $use_dir = true){
        //$file = self::getTargetFile($fileName);
        $dir=self::getDir();
        if($use_dir == false)
            $dir = "";
        $file = $use_dir . $fileName;
        if(file_exists($file)){
            unlink($file);
        }
    }
    
    public static function checkFile($fileData){
        // Check if image file is a actual image or fake image
        //$check = getimagesize($fileData['tmp_name']);
        return true;  // Let user upload all types of file, for now
        /*
        if($check !== false) 
            return true;
         else 
         return false;*/
    }

    /**
     * Creates a new directory and sets as the current target path.
     **/
    public static function makeDir($inPath){
        $path = self::getdir() . $inPath ."/";
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    

    /**
     * The root directory to upload must be set in config.php file:
     * Example:
     *  $upload_dir = "";
     **/
    public static function getdir(){
        if(self::$target_dir == null)
            self::$target_dir = Config::$upload_dir;

        return self::$target_dir."/";
    }

    public static function changeDir($dir){
        self::$target_dir = Config::$upload_dir."/".$dir;
    }
}

?>
