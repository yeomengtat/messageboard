<?php
namespace Xodebox\db;
use Xodebox\DB_RootMethods  as DB_RootMethods;
use Xodebox\DB_AdminMethods as DB_AdminMethods;
use Xodebox\DB_UserMethods  as DB_UserMethods;
use Xodebox\db\DBO as DBO;
use Xodebox\db\FieldType as Datatype;
use Xodebox\GenericDatabase;
use Xodebox\db\FieldAttribute as Attribute;

class MySQLDatabaseLayer implements DB_RootMethods, DB_AdminMethods, DB_UserMethods{
    private $db = null;
    private $query = [];
    private $results = [];

    public function __construct(GenericDatabase $db){
        $this->db = $db;
    }

    public function createDatabase($dbname){
        $q = "CREATE DATABASE {$dbname};";
        $this->registerQuery($q);
    }

    public function deleteDatabase($dbname){
        $q = "DROP  DATABASE {$dbname};";
        $this->registerQuery($q);
    }

    public function useDatabase($dbName){
        $q = "USE DATABASE {$dbname};";
        $this->registerQuery($q);
    }

    /**
     * Usage example:<br>
     * createTable('users', ['name' => 'String', 'age' => [type => 'int', 'default' => '21'] , 'id' => 'pk', 'contact_id' => ['type' => 'int', 'references' => 'contact_table'] ] );
     **/
    public function createTable($tableName, Array $fields){
        if(!$this->isDictionary($fields))
            return false;
        //$dbo = new Xodebox\db\DBO([]);
        $data = "";
        $table = DBO::createTable($tableName, $fields);

        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
		$this->db->results();

        $width = $table->getWidth();
        $foreign_keys = "";
        $unique = [];
        foreach($table->getFields() as $key => $field){
            if($key < $width-1)
                $comma = ",";
            else
                $comma = " ";
            $type = $field->getType();
            if($type == Datatype::Integer)
                $type = "INT";
            elseif($type == Datatype::String)
                $type = "VARCHAR(255)";
            elseif($type == Datatype::Text)
                $type = "TEXT";
            elseif($type == Datatype::Double)
                $type = "FLOAT";
            elseif($type == Datatype::TimeStamp)
                $type = "TIMESTAMP DEFAULT 0";
            elseif($type == Datatype::CreateTimeStamp)
                $type = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ";
            elseif($type == Datatype::UpdateTimeStamp)
                $type = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";                         
            else
                $type = "VARCHAR(128)";

            //Add attributes -- TODO Refactor the following to something better
            $attr = "";
            //if($field->checkAttribute(Attribute::Unique))  //Removed because this not compatible with MYSQL Syntax
            //    $attr .= "UNIQUE ";
            if($field->checkAttribute(Attribute::PrimaryKey))
                $attr .= "PRIMARY KEY ";
            if($field->checkAttribute(Attribute::NotNull))
                $attr .= "NOT NULL ";
            if($field->checkAttribute(Attribute::AutoIncrement))
                $attr .= "AUTO_INCREMENT ";
            if($field->checkAttribute(Attribute::CurrentTime))
                $attr .= "DEFAULT CURRENT_TIMESTAMP ";


            //Add foreign key
            //var_dump($field->getReference());
            if($field->getReference() != null && $field->checkAttribute(Attribute::ForeignKey)){
                $ref = explode(".", $field->getReference());
                if(!isset($ref[1]))
                    $ref1 = 'id';    //Change this to default primary key name
                $foreign_keys .= ", FOREIGN KEY ({$field->getName()}) REFERENCES {$ref[0]}({$ref[1]})";
            }

            if($field->checkAttribute(Attribute::Unique)){
                $unique []= $field->getName();
            }
            
            //if($field->checkAttribute(Attribute::NotNull) 
            
            $data .= "{$field->getName()} {$type} {$attr} {$comma} ";
            //print $field . "\n";
        }
        $uq = "";
        if($unique){
            $uq = ", UNIQUE (". implode(", ", $unique).")";
        }
        
        $q = "CREATE TABLE {$table->getName()} ({$data}{$foreign_keys}{$uq}); ";

        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
		$this->db->results();

        
        //print $q."\n";
        $this->query []= ['statement' => $q];
    }

    /**
     * Not implemented yet.
     **/
    public function alterTable($tablename, Array $fields, $op){
        if($op == "add")
        array_walk($fields,
                   create_function('&$i,$k', '$i=" $k $i" ;') ); //Create function is just a backward compatible lambda function. Check out the php docs for more information.
        
        if($op == "add"){
            $op = "ADD";
            $fieldString = implode($fields, ", ADD COLUMN");
        }
        elseif($op = "drop"){
            $op = "DROP COLUMN";
            $fieldString = implode(array_keys($fields), ", DROP ");
        }
        else
            return false; //Invalid operation
        
        $query = "ALTER TABLE $tablename {$op} {$fieldString};\n";
        $this->query []= ['statement' => $query];
    }

    
    public function deleteTable($tableName){
        $query = "DROP TABLE {$tableName};";
        $this->query []= ['statement' => $query ];
    }

    

    private function isDictionary(Array $arg){
        if( count(array_keys($arg)) == count(array_values($arg)) )
            return true;
        return false;
    }

    public function executeQueries(){
        if($this->db == null){
            throw new \Exception("Database not set.\n");
            return false;
        }
        //$this->db->query("SET FOREIGN_KEY_CHECKS=0;");
		//$this->db->results();
        while(!empty($this->query)){
            $q = array_shift($this->query);
            $this->db->query($q['statement']);
            if(isset($q['bind'])){
                $this->results []= $this->db->results($q['bind']);
            }else{
                $this->results []= $this->db->results();
            }
            $this->db->success();               
        }
		//$this->db->query("SET FOREIGN_KEY_CHECKS=1;");
		//$this->db->results();
        return true;
    }

    public function executeQuery(){
        $q = array_shift($this->query);
        //$this->db->query("SET FOREIGN_KEY_CHECKS=0;");
		//$this->db->results();
        
        $this->db->query($q['statement']);        
        if(isset($q['bind'])){
            $this->results []= $this->db->results($q['bind']);
        }else{
            $this->results []= $this->db->results();
        }        
        return $this->db->success();                       
    }
    

    public function clearResults(){
        $this->results = [];
    }

    public function clearQueries(){
        $this->queries = [];
    }

    public function getResults(){
        return $this->results;
    }

    public function dumpQueries(){
        return $this->query;
    }

    public function updateRecords($tableName, Array $data, Array $conds){
        if (array_keys($data) === range(0, count($data) -1))
            throw new \Exception(get_class($this). ": Argument \$data must be an associative array.");
        if (array_keys($conds) === range(0, count($conds) -1))
            throw new \Exception(get_class($this). ": Argument \$conds must be an associative array.");

        $rowString = "";
        $i = 0;
        $len = count($data);
        $valueString = "";
        $condString  = "";
        $bind = [];

        $bindCount = 0;
        
        $keyLen = count($conds);
        foreach($conds as $val1 => $val2){
            $condString .= "`". $val1 . "`";
            $condString .= " = ";
            $condString .= ":val{$bindCount}";
            $bind[":val{$bindCount}"] = $val2;
            $bindCount++;
            if($i != $keyLen - 1)
                $condString .= " AND ";
            $i++;
        }

        $i=0;
        foreach ($data as $key => $item){
            $valueString .= "". $key . "";
            $valueString .= " = "; // '{$item}'";
            $valueString .= ":val{$bindCount}";
            if($item == 'null' || $item == "NULL")  //QUICKFIX. TODO: Use PHP null keyword to unassign.
                $item = null;
            $bind[":val{$bindCount}"] = $item;
            $bindCount++;
            if($i != $len - 1){
                //$rowString .= ", ";
                $valueString .= ", ";
            }
            $i++;
        }

        $query = "UPDATE {$tableName} SET {$valueString} WHERE {$condString};";
        $this->query []= ['statement' => $query,
                          'bind'      => $bind
        ];
    }

    public function addRecords($tableName, Array $data){
        if (array_keys($data) === range(0, count($data) -1))
            throw new \Exception(get_class($this). ": Argument \$items must be an associative array.");

        $fieldString = "";
        $valueString = "";
        $i=0;
        $len = count($data);

        $itemArr = [];
        foreach ($data as  $key => $item){
            $fieldString .= "`{$key}`";
            $valueString .= ":item{$i}";
            $itemArr[":item{$i}"] = $item;
            if($i != $len - 1)
            {
                $fieldString .= ", ";
                $valueString .= ", ";
            }
            $i++;
        }
        $query = "INSERT INTO {$tableName} ({$fieldString}) VALUES ($valueString);";
        $this->query []= ['statement' => $query,
                          'bind'      => $itemArr];
        //var_dump($this->query);
        $this->executeQueries();
        //$this->db->query($query);

        
        //foreach($itemArr as $key => $item)
        //    $this->db->bind($key, (string) $item);
        //print $query . "\n";
        // print_r($itemArr);
        //$results = $this->db->results();
        return true;
    }

    public function deleteRecords($table, $cond = null){
        if(array_keys($cond) === range(0, count($cond) -1) )
            throw new \Exception(get_class($this). ": Argument \$conds must be an associative array. \n");

        $condStr = "";
        $i = 0;
        $len = count($cond);

        foreach($cond as $key => $value){
            $condStr .= " `{$key}` = '{$value}'";
            if($i != $len -1)
                $condStr .= " AND";
            $i++;
        }

        if($cond == null)
            $query = "DELETE FROM {$table};";
        else
            $query = "DELETE FROM {$table} WHERE {$condStr};";

        $this->query []= ['statement' => $query];
    }

    /**
     * TODO: Refactor and clean this code
     * $cond can be ['col' => 'value'], ['col' => ['op' => 'val]]
     * pass '{bind_mode} => false to turn of the bind mode
     **/
    public function findRecords($table, $cond = null, $item = "*", $bind_mode = true, $limit = 0, $orderCol = [], $order="ASC"){
        if($order == null)
            $order = "ASC";
        if($item == null)
            $item = "*";

        //Quick fix to let bind mode be removed by the caller. 
        if(isset($cond['{bind_mode}'])){
            $bind_mode = $cond['{bind_mode}'];
            unset($cond['{bind_mode}']);
        }
        
        $bool = "AND";
        $condStr = "";
        $i = 0;
        $len = count($cond);
        $ci = 0;
        $bind = [];
        $limitStr = "";
        $orderStr = "";

        if($limit > 0)
            $limitStr = "LIMIT {$limit}";


        //var_dump(count($orderCol) >= 1);
        if(count($orderCol) >= 1){
            $orderParam = implode(', ',$orderCol); 
            $orderStr    = "ORDER BY {$orderParam} {$order}";
        }
        
        if($cond != null){
            foreach($cond as $key => $value){
                $op = "=";
                if(is_array($value)){
                    $op = $key;
                    foreach($value as $key => $val){
                        $condStr  .= " {$key} {$op} :val{$i}";
                        $bind[":val{$i}"] = $val;
                        if($i != $len -1)
                            $condStr .= " $bool";
                        $i++;
                    }
                }else{                                     
                    $condStr .= " {$key} {$op} :val{$i}";
                    $bind[":val{$i}"] = $value;
                    if($i != $len -1)
                        $condStr .= " $bool";
                    $i++;
                }
            }
            
            $query = "SELECT {$item} FROM {$table} WHERE {$condStr}  {$orderStr} {$limitStr};";

            //$this->query []= ['statement' => $query,
            //                  'bind'      => $bind];
            
            if($bind_mode == false){
                foreach($bind as $bkey => $bval){
                    $query = str_replace($bkey, $bval, $query);
                }
                $bind = null;
                $this->db->query($query);
                return $this->db->results();

            }
            
            //--DEBUG_START----
            //print $query;
            //print "<br>";
            //print_r($bind);
            //print "<br>";
            //--DEBUG_END----
            
            $this->db->query($query);
            return $this->db->results($bind);
                
        }else{ //retrieve all
            $query = "SELECT {$item} FROM {$table}  {$orderStr} {$limitStr};";
                    
            $this->db->query($query);
            return $this->db->results();
        }
    }


    /**
     * Feature not implemented yet.
     **/
    public function joinTables($table1, $table2, $filter){
        /**
           Do nothing
        **/
        return false;
    }    

    public function tableExists($tableName){
        $q = "SHOW TABLES LIKE '$tableName';";
        $this->db->query($q);
        $res = $this->db->results();
        if(count($res)>0)
            return true;
        return false;
    }

    private function registerQuery($query, $bind=null){
        $q = ['statement' => $query];
        if($bind != null)
            $q ['bind'] = $bind;
        $this->query []= $q;                          
    }

    public function getLastInsertId(){
        //$this->db->query("SELECT LAST_INSERT_ID()");
        return $this->db->getLastInsertId();
    }

    public function getAllFields($tableName){
        $this->db->query("SHOW COLUMNS FROM `$tableName`;");
        return $this->db->results();
    }
    


    /** 
     * Convert string data type to MySQL datatype
     **/
    public function convertTypeString($type){
        $dboType = DBO::getType($type);
        if($dboType != null){
            return self::convertType($dboType);
        }
        return null;
    }

    /**
     * Convert type from DBO to MySQL 
     **/
    public static function convertType($type){
        if($type == Datatype::Integer)
            $type = "INT";
        elseif($type == Datatype::String)
            $type = "VARCHAR(255)";
        elseif($type == Datatype::Text)
            $type = "TEXT";
        elseif($type == Datatype::Double)
            $type = "FLOAT";
        elseif($type == Datatype::TimeStamp)
            $type = "TIMESTAMP";
        elseif($type == Datatype::CreateTimeStamp)
            $type = "TIMESTAMP";
        elseif($type == Datatype::UpdateTimeStamp)
            $type = "TIMESTAMP";
        else
            $type = "VARCHAR(128)";        
        return $type;
    }
	
	
}
?>