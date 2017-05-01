<?php
declare(strict_types =1);
require("../../autoload.php");

use PHPUnit\Framework\TestCase;
use Xodebox\Database as Database;
//use Xodebox\DatabaseFactory as DatabaseFactory;

use Xodebox\db\DBO;
//use Xodebox\db\FieldAttributes as Attribute;

final class DatabaseTest extends TestCase{

    public function testCanConnectDatabase(){
        $db = null;
        
        try{            
            $db = Database::createDB(Xodebox\Config::$dbConfig);            
            //$db->test();
        }catch(\Exception $ex){
            print "\Database Error:  ".$ex->getMessage() ;
        }
        //$this->assertInstanceOf(Database::class, $db);
    }

    public function testCanCreateDBO(){
        try{
            //$tableTest = new DBO();
            //$tableTest = DBO::CreateTable([]);
            $field1 = DBO::createField('age',1,7);
            $field2 = DBO::createField(['name' => "name",
                                        'type' => 'string',
                                        'uq'   => true,
                                        'pk'   => true,
                                        'fk'   => true
            ]);
            //print $field1;
            //print "\n";
            //print $field2;
            $table  = new Xodebox\db\Table('user', [$field1, $field2]);
            $table2 = DBO::createTable('users', [$field1, $field2,
                                        'email' => "string",
                                        'contact_id' => ['fk'   => true,
                                                         'uq'   => true,
                                                         'type' => 'int']
            ]);
                  
            $this->assertNotNull($table);
            $this->assertNotNull($table2);
            $this->assertTrue($field1->checkAttribute(Xodebox\db\FieldAttribute::Unique));
            //var_dump($table2->getFields());
        }catch(\Exception $ex){
            print "\nException:  ".$ex->getMessage() ;
        }

    }

    public function testMySQLDBLayer(){
        print "\nTesting MySQL Layer \n";
        try{
            $db = Database::createDB(Xodebox\Config::$dbConfig);            
            
            $dbLayer = new Xodebox\db\MySQLDatabaseLayer($db);
            
            $table1 = $dbLayer->createTable('Company',
                                            ['Name' => 'string',
                                             'type' => 'int'
                                            ]);            
            
            $dbLayer->executeQueries();            
            $this->assertTrue($dbLayer->tableExists('Company'));
            $table1 = $dbLayer->deleteTable('Company');
            $dbLayer->executeQueries();
            $this->assertFalse($dbLayer->tableExists('Company'));

            /*
            $table2 = $dbLayer->createTable('users',
                                            ['name' => 'string',
                                             'type' => 'string']);
            */

            $dbLayer->addRecords("users", ["name" => "Ibrahim",
                                          "type" => "Employee"]);
            $dbLayer->executeQueries();
            
            $findRes = $dbLayer->findRecords('users', ['name' => 'Ibrahim']);
            $this->assertNotEmpty($findRes);
            $dbLayer->deleteRecords('users', ['name' => 'Ibrahim']);
            $dbLayer->executeQueries();
                        
            $findRes = $dbLayer->findRecords('users', ['name' => 'Ibrahim']);
            $this->assertEmpty($findRes);
            $dbLayer->executeQueries();

            
        }catch(Exception $ex){
            print "Exception {$ex->getMessage()}\n";
        }

        //var_dump(Xodebox\Config::$dbConfig);
    }

    public function testCanCreateDatabase(){

    }

    public function testCanAccessDatabase(){
    }
}


?>