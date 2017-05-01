<?php

require("../../autoload.php");
use PHPUnit\Framework\TestCase;

use Xodebox\db\RepositoryLayer as RepositoryLayer;
use Xodebox\db;

class TestUserModel extends Xodebox\Model{
    public $name;

    public function construct(){
        //map name to table name by default.
    }
}

final class RepositoryLayerTest extends TestCase{
    public function testRepositoryLayer(){
        print "\nTesting repository Layer\n";
        $name = 'testUsers';
        
        $db = new Xodebox\Database();
        $n = new RepositoryLayer($db);
        $user = new TestUserModel();
        $user->name = 'Doe J.';
        
        $this->initTable($db->getDatabaseLayer());
        
        $n->setTable($name);
        $n->setPrimaryKey('id', 1);
        $n->attachColumn('name');
        
        $n->save($user);
    }

    private function initTable($db){
        //$db->deleteTable('testUsers');
        //$db->executeQueries();
        if(!$db->tableExists('testUsers')){
            print "Test table does not exist yet. \n";
            $db->createTable("testUsers",
                             ['name' => 'string',
                              'id'   => [ 'pk' => true,
                                         'type' => 'int',
                                         'ai' => true
                              ]
                             ]
            );
            print $db->dumpQueries()[0]['statement'];
            $db->executeQueries();
            print "Test Table created. \n";
        }else{
            print "Test table found. \n";
        }
    }
            
}

?>