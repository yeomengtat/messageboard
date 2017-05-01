<?php
namespace Xodebox;

interface GenericDatabase{
    public function query($query);
    public function success();
    public function results();
}

interface DB_RootMethods{
    public function createDatabase($dbname);
    public function deleteDatabase($dbname);
    public function createTable($tableName, Array $fields);
    public function deleteTable($tableName);
    public function alterTable($tableName, Array $fields, $operation);
}

interface DB_AdminMethods{
    public function tableExists($tableName);
    public function addRecords($table, Array $fields);
    public function updateRecords($table, Array $field, Array $cond);
    public function deleteRecords($table, $cond=null);
}

interface DB_UserMethods{
    public function findRecords($table, $cond = null);
    public function joinTables($table1, $table2, $filter);
}

class DataField{
    private $dataType;
    private $flags;  //[PK, FK]
    private $data;
};


class Database{
    private $db = null;
    private $layer1  = null;
    public function __construct(Array $config =  null){
        if($config == null)
            $config = Config::$dbConfig;        
        $this->db = new db\PDO_Database([
            'host'  => $config['host'],
            'user'  => $config['user'],
            'pass'  => $config['pass'],
            'db'    => $config['name']]);
        $this->attachSQLLayer();
    }
    
    public function attachSQLLayer(){    
        $this->layer1 = new db\MySQLDatabaseLayer($this->db);
    }

    public function getDatabase(){
        return $this->db;
    }

    public function getDatabaseLayer(){
        return $this->layer1;
    }

    public static function createDB(Array $config = null){
        if($config == null)
            $config = Config::$dbConfig;
        return new db\PDO_Database([
            'host'  => $config['host'],
            'user'  => $config['user'],
            'pass'  => $config['pass'],
            'db'    => $config['name']]);
    }
}


class DatabaseFactory{
    public static function createDB(Array $config = null){
        return new db\PDO_Database([
            'host'  => $config['host'],
            'user'  => $config['user'],
            'pass'  => $config['pass'],
            'db'    => $config['name']]);
    }
}

?>