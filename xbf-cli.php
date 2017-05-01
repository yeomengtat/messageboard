<?php
require("library/autoload.php");

use Xodebox\Database;
new Xodebox\ExceptionHandler();
$os =php_uname('s');
print "Xodebox Webframework Command-line. \n";
print "You should not be able to see this on a web browser\n";
print "Running on a $os System.\n";
\AutoLoader::addpath("../app/Controllers");
\AutoLoader::addpath("../app/Models");


if (!preg_match('/Linux/', $os)){
    print "  NOTE: This tool was designed to run on a Linux system. Some features may not work.\n";
}

if(isset($argv[1]))
    $module =  $argv[1];
if(isset($argv[2]))
    $action =  $argv[2];

if(!isset($module) ){
    print "  Usage: \n\t xbf-cli [module] [action]\n";
    print "   Available modules: \n";
    print "    db    -- Database Module. \n";
//    print "    gen -- Generator Module. \n";
    exit;
}


if($module == "db"){
    if (!isset($action)){
        print "Available actions for db module: \n\t create - Create a new database \n\t createDBO [ModelName] - Create/Update a Model \n\t regen - Create/Update all Models \n";
        exit;
    }
    
    if ($action == "create"){
        $db = (object) Xodebox\Config::$dbConfig;
        print "Creating database '{$db->name}'\n";
        
        $queries = ["CREATE DATABASE IF NOT EXISTS {$db->name};",
                    "GRANT USAGE ON *.* TO {$db->user}@{$db->host} IDENTIFIED BY '{$db->pass}';",
                    "GRANT ALL PRIVILEGES ON {$db->name}.* TO {$db->user}@{$db->host};",
                    "FLUSH PRIVILEGES;"
        ];

        $admin = $db->user;
        $pass  = $db->user;
        if(isset($argv[3]))
            $admin = $argv[3];
        if(isset($argv[4]))
            $pass = $argv[4];
        
        $ret = shell_exec("mysql -u{$admin} -p{$pass} -e \"".implode($queries)."\"");
        if($ret){
            print "\n Database creation Failed. \n";
            print "Try executing the following query manually, then run xbf-cli db regen. \n";
            print "\n User:$admin Pass: $pass \n";
        }else{
            print "Successfully created. Now run: \n\t xbf-cli db regen \n";
        }
                                               
        exit;
        
    }
    
    $db  = new Database();
    $dbl = $db->getDatabaseLayer();
    
    //Create/Update Database Objects
    if($action == "createDBO" && isset($argv[3]))
    {
        $className = $argv[3];
        print "Class name: $className \n";
        $className = "$className";
        //new $className;
        
        if(method_exists ( $className , 'createDBO' )){
            $ret = $className::createDBO($db);
            if($ret)
                print "  Table changed \n";
            else
                print "  No changes detected.\n";
        }        
    }

    //Create/Update all database objects
    if($action == "regen")
    {
        print "Regenerating database models. \n";
        $dir = "app/Models/";
        $files = scandir($dir);
        foreach($files as $file){
            $className = null;
            if($file == "." || $file  == "..")
               continue;
            preg_match('/(.*).class.php/', $file, $match);
            if(count($match)>0)
                $className = $match[1];
            if($className && method_exists ( $className , 'createDBO' ) ){
                
                print "Processing Model $className \n";
                $ret =  $className::createDBO($db);
                if($ret)
                    print "\t Table changed \n";
                else
                    print "\t No changes detected.\n";
            }

        }
    }else{
        print "Database module \n";
    }
    
}

?>