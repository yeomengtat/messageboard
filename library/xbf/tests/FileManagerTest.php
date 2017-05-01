<?php
require("../../autoload.php");
use PHPUnit\Framework\TestCase;

use Xodebox\FileManager;

final class FileManagerTest extends TestCase{

    
    public function testCreateDirectory(){
        var_dump(Xodebox\Config::$upload_dir);
        /*
        FileManager::changeDir("../../../../uploads");        
        FileManager::makeDir('test');
        FileManager::makeDir('test2/a');*/
            //public
    }
    
    //public function testFileCreate(){
    //}

}


?>