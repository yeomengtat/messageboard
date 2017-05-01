<?php
$pwd  = dirname(__FILE__);
$inc_path = $pwd ;
set_include_path($inc_path);

class ExceptionFileNotFound extends Exception{
    
}

class MissingFile extends Exception{
    public function __toString(){
        return "File not found";
    }
}

/*
function autoload($className){
    $dirMap = ["Xodebox" => 'xbf'];
    
    $fileName= "{$className}.class.php";

    //Map namepsaces to alternative directories.
    $fileName = str_replace("\\", "/", $fileName);
    foreach($dirMap as $key => $new_dir){
        $test = "";
        if(strlen($fileName) >= strlen($key)){
            $test = substr($fileName, 0, strlen($key));
        }

        if($test == $key){  //Match found
            $fileName = $new_dir . substr($fileName, strlen($key));
            break;
        }
    }

    //return $fileName;
    print "Loading class $fileName..\n";
    require($fileName);
    //throw
}
*/

class Autoloader{
    public static $paths = [".", "xbf", "../..", "./library", "./library/xbf",
                            "library/xbf"
                            
    ];
    
    private static $dirMap = ["Xodebox" => "xbf"];
    public static $verbose_mode = false;
    
    public static function load($class){
        $pwd =  dirname(__FILE__)."/";
        $classPath = AutoLoader::getFileName($class);
        //print "Namespace = ". self::getNamespace($class) ."\n";
        $classPath = self::replacePath($classPath);
        foreach (self::$paths as $path) {
            $path = $pwd . $path;
            //if(gettype($alias) == 'string')
            if(self::$verbose_mode)
                print "\n Searching path {$path}/{$classPath}\n";
            if (is_file($path ."/" . $classPath)) {
                if(self::$verbose_mode)
                    print "Found {$path}/{$classPath}\n";
                require_once $path . "/" . $classPath;
                return;
            }
        }
    }

    private static function replacePath($path){
        $retpath = $path;
        //print "$path\n";
        //print "PATH = $retpath ".  str_replace('Xodebox', 'xbf', $path) . "\n";
        foreach(self::$dirMap as $alias => $dir){
            $retpath = str_replace($alias, $dir, $retpath);
        }
        return $retpath;
    }

    private static function getNamespace($className){
        $match = "";
        //preg_match("((?:\\{1,2}\w+|\w+\\{1,2})(?:\w+\\{0,2})+)", $className, $match);
        preg_match("/.*[\\\]/", $className, $match);
        if(isset($match[0]))
            return $match[0];
        return null;
    }

    private static function getFileName($className){
        $fileName= "{$className}.class.php";
        //Map namepsaces to alternative directories.
        $fileName = str_replace("\\", "/", $fileName);
        
        //print "\n$fileName\n";
        return $fileName;
    }

    public static function addPath($path) {
        //$path = realpath($path);
        if ($path) {
            self::$paths[] = $path;
        }
    }
}

require_once('xbf/config.php');
//spl_autoload_register('autoload');
spl_autoload_register(['Autoloader', 'load']);

//print "AOK";
//$database = new Xodebox\Database();
//$database->test();
?>