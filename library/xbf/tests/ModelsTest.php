<?php
require("../../autoload.php");
use PHPUnit\Framework\TestCase;

use Xodebox\db\RepositoryLayer as RepositoryLayer;
use Xodebox\db;

class TestUserModel extends Xodebox\Model{
    protected $id;
    public $name;
    
    public function __construct(){
        //map name to table name by default.
        //$this->setTableName("testUsers");
    }

    public function getDefaultTable(){
        return parent::getTableName();
    }

    /**
     * This information will be used by the automatic table creator
     **/
    public static function creator(){
        return [
             'id'   => ['type' => 'int',
                         'pk'   => true,
                         'nn'   => true,
                         'ai'   => true],
             'name' => 'string'
        ];
        //$this->addProperty('id', 'int', 'pk');
        //$this->addProperty('name', 'string');
        //$this->setPrimaryKey('id');        
    }
}

class TestAuthorModel extends Xodebox\Model{
    public $id, $name, $books;
    
    public static function creator(){
        return ['id'   => 'pk',
                'name' => 'string'
        ];
    }

    public function addRelations(){
        return ['books'  => 'TestBookModel.author.many'];
    }
}

class TestBookModel extends Xodebox\Model{
    public $id, $name, $author;
    public static function creator(){
        return ['id'     => 'pk',
                'name'   => 'string',
                'author' => ['type'        => 'int',
                             'references'  => 'user_table.id',
                             'fk'          => true
                ]      
        ];
    }

    public function addRelations(){
        return ['author' => 'TestAuthorModel.id']; //one to one
    }
}

class TestEvent extends Xodebox\Model{
    public $id, $name, $description, $date, $create, $updated;
    public static function creator(){
        return [
            'id'           => 'pk',
            'name'         => 'string',
            'description'  => 'string',
            'date'         => 'timestamp',
            'created'       => 'createtime',
            'updated'      => 'updatetime'
        ];
    }
}

class TestClient extends Xodebox\Model{
    public $id, $name, $agent;
    public static function creator(){
        return [
            'id'     => 'pk',
            'name'   => 'string',
                        'agent' => ['type'        => 'int',
                         'references'  => 'test_agent_table.id',
                         'fk'          => true
                         ]      
        ];

    }
    public function addRelations(){
        //return [];
        return ['agent'  => 'TestAgent.id.both'];
    }

}

class TestAgent extends Xodebox\Model{
    public $id, $name, $client;
    public static function creator(){
        return [
            'id'     => 'pk',
            'name'   => 'string',
            'client' => ['type'        => 'int',
                        'references'  => 'test_client_table.id',
                        'fk'          => true
            ]      
        ];
    }
    public function addRelations(){
        return ['client'  => 'TestClient.id'];
    }
}
    
final class ModelsTest extends TestCase{
    
    public function testModels(){
        print "\nTesting Models \n";
        
        $db = new Xodebox\Database();
        $n = new RepositoryLayer($db);

        //print "Creating DBO for test user Model.\n";
        //TestUserModel::createDBO($db);

        
        $user = new TestUserModel();
        $user->setRepository($n);
        //print $user->getDefaultTable();
        $user->name = "Johnny B. Goode";

        $user->save();
        $user->remove();

        //TestUserModel::setGlobalRepository($n);        

        $user2 = new TestUserModel();//TestUserModel::find('6');        
        $user2->setRepository($n);
        $m = $user2->find('4');

        //print_r ($m) ."\n";
        //print $user2;

        //var_dump($user2->getFieldMap());
        //print $user->var;
        
        //$user2->remove();
        //print $user;
        //var_dump( $user2);
        //$user2->remove()

        print_r($author->books);
        
        //$a = $user->getClassAttribs();        
        //var_dump($a);
        //$user->save();
        //var_dump($ret);
    }

    public function testRelation(){
        print "Testing relations";
        $db = new Xodebox\Database();
        $n = new RepositoryLayer($db);

        //print "Creating DBO for test user Model.\n";
        TestAuthorModel::createDBO($db);
        TestBookModel::createDBO($db);
        
        $author = new TestAuthorModel();
        $book   = new TestBookModel();

        $author->find(1);
        //$author->name = "HP Lovecraft";
        //$author->save();

        //$book->name = "Mountain of Madness";
        //$book->author = $author;
        //$book->save();
        //$book->n

        $book->find(3);

        //print $book;
        print $book;

        print $author;

        $books = $author->books->all();
        $outsider = $author->books->find(['name' => 'Outsider']);

        $all = $book->findAllRecords();
        print $book->findAll()[0]->name;
        //var_dump($books);
        //var_dump($outsider);
        //var_dump($all);
        //$x;
        //print_r($author->books);
        //print $book->author;
    }

    
    public function testTimeStamp(){
        print "\n Testing timeStamp\n";
        $db = new Xodebox\Database();
        $r = new RepositoryLayer($db);

        //print "Creating DBO for test user Model.\n";
        //TestAuthorModel::createDBO($db);
        TestEvent::createDBO($db);

        
        $event = new TestEvent();
        $event->name         = "wakeup";
        $event->description  = "Wake up everymorning";
        //$event->date         = "12-02-2016";
        $event->date = "2013-08-05 18:19:03";
        $event->save();
    }

    public function testOneToOne(){
        print "\nTesting one to one relation\n";
        $db = new Xodebox\Database();
        $n = new RepositoryLayer($db);

        /*
        print "Creating Client table\n";
        TestClient::createDBO($db);
        print "Creating Agent table\n";
        TestAgent::createDBO($db);
        */
        
        //$c = new TestClient();
        //$c->name = "Joe";
        //$c->save();
        $c = TestClient::fetch(1);
        print $c;

        print "\n";
        print $c->agent->id;

        print "\n";
        /*
        $a = new TestAgent();
        $a->name = "Jane";
        $a->save();
        */
        $a = TestAgent::fetch(1);
        print $a;

        //$a->client = $c;
        //$a->save();

        //$c->agent = $a;
        //$c->save();
        
    }
}


?>