<?php
namespace Xodebox\db;

use PDO;
use \Exception;
use Xodebox\GenericDatabase as GenericDatabase;

class PDO_Database implements GenericDatabase{
    private $host = null;
    private $user = null;
    private $pass = null;
    private $pdo  = null;
    private $dbname = null;
    private $last_result = false;
    private $fetch_method = PDO::FETCH_ASSOC;
    private $statement = null;
    private $query_count = 0;
                     
    private $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_PERSISTENT => true
    ];


    /**
     *  Construct a pdo database
     * Expects the following $config parameters
     *   - host
     *   - user
     *   - pass
     *   - db
     **/
    public function __construct(Array $config){
        try{
            $this->host = $config['host'];
            $this->user = $config['user'];
            $this->pass = $config['pass'];
            $this->dbname = $config['db'];
            $this->pdo = $this->makePDO();
        }catch(PDOException $ex){
            echo $ex->getMessage();
            exit();
        }        
    }

    /**
     * Makes the PDO Object
     *
     * @return null on failure
     */
    private function makePDO(){
        if($this->host == null){
            throw new \Exception("Host not set.");
            return null;
        }

        if($this->user == null){
            throw new \Exception("User not set.");
            return null;
        }

        if($this->pass == null){
            throw new \Exception("Password not set.");
            return null;
        }
        
        $user    = $this->user;
        $pass    = $this->pass;
        $host    = "mysql:host={$this->host}";
        $dbname  = "dbname={$this->dbname}";
        $options = $this->options;
        $pdo = new PDO("$host;$dbname", $user, $pass, $options);
        
        return $pdo;
    }

    public function query($query){
        $this->statement = $this->pdo->prepare($query);
    }

    /**
     * TODO: This method needs to be refactored.
     **/
    public function bind($pos, $value, $type=null){
        if(is_null($type)){
             switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($pos, $value, $type);
    }

    public function success(){
        return $this->last_result;
    }

    public function results($param = null){
        if($this->statement == null)
            return null;
        $this->last_result = $this->execute($param);
        return $this->statement->fetchAll($this->fetch_method);
    }

    private function execute($param = null){
        $this->query_count++;
        if($param)
            return $this->statement->execute($param);
        else
            return $this->statement->execute();
    }

    public function getLastInsertId(){
        return $this->pdo->lastInsertId();
    }

    
}

?>