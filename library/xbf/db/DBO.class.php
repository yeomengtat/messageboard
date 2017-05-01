<?php
namespace Xodebox\db;

abstract class FieldType{
    const Undefined = 0;
    const Integer = 1;
    const Double  = 2;
    const String  = 3;
    const TimeStamp = 4;
    const CreateTimeStamp = 5;
    const UpdateTimeStamp = 6;
    const Text = 7;
    const strMap  = [
        0=> 'Undefined',
        1=> 'Integer',
        2=> 'Double',
        3=> 'String',
        4=> 'TimeStamp',
        5=> 'CreateTimeStamp',
        6=> 'UpdateTimeStamp',
        7=> 'Text'        
    ];
}

abstract class FieldAttribute{
    const None          = 0;
    const Unique        = 1;
    const PrimaryKey    = 2;
    const ForeignKey    = 4;
    const NotNull       = 8;
    const AutoIncrement = 16;
    const CurrentTime   = 32;
    const strMap = [
        0=>'',
        1=>'Unique',
        2=>'Primary Key',
        4=>'Foreign Key'       
    ];
}

class Field{
    private $dataType  = FieldType::Integer;
    private $attribute = FieldAttribute::None;
    private $name = null;
    private $reference = null; 
    
    public function __construct($name,  $type = FieldType::Integer,  $attr = FieldAttribute::None){
        $this->dataType  = $type;
        $this->attribute = $attr;
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function getType(){
        return $this->dataType;
    }

    public function getAttributes(){
        return $this->attribute;
    }

    public function checkAttribute($attr){
        if($attr < 0)
            return false;
        if($this->attribute & $attr)
            return true;
        return false;
    }

    public function setReference($table){
        $this->reference = $table;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function __toString(){
        $attrs = "";
        $keys = array_keys(FieldAttribute::strMap);
        //$strMap = FieldAttribute::strMap;
        for($i=0; $i< count(FieldAttribute::strMap); $i++){
            $j = array_pop($keys);
            if($this->attribute & $j)
                $attrs .= " ". FieldAttribute::$strMap[$j] ." ";
        }
            //$attrs = $this->attribute;
        $type  = FieldType::$strMap[$this->dataType];
        return "<{$type}> {$this->name} : [$attrs]";
    }
}

/**
 * Field factory creates fields using given parameters
 **/
class FieldFactory{
    private $field = null;

    /**
     * Example use:
     * createField(['name' => 'name', 'type' => 'string', 'pk' => true]);
     **/
    public static function createField(Array $params){
        //Valid keys --> ['name', 'type', 'pk', 'fk', 'uq', 'ref']
        $name = null;
        $type = null;
        $attr = FieldAttribute::None;
        foreach($params as $key => $val){
            if($key == 'name')
                $name = $val;
            if($key == 'type'){
                if($val == 'int')
                    $type = FieldType::Integer;
                if($val == 'float')
                    $type = FieldType::Double;
                if($val == 'string')
                    $type = FieldType::String;
                if($val == 'text')
                    $type = FieldType::Text;
                if($val == 'timestamp')
                    $type = FieldType::TimeStamp;
                if($val == 'createtime')
                    $type = FieldType::CreateTimeStamp;
                if($val == 'updatetime')
                    $type = FieldType::UpdateTimeStamp;                
            }
            
            if($key == 'fk' && $val)
                $attr += FieldAttribute::ForeignKey;
            if($key == 'pk' && $val)
                $attr += FieldAttribute::PrimaryKey;
            if($key == 'uq' && $val)
                $attr += FieldAttribute::Unique;
            if($key == "nn" && $val)
                $attr += FieldAttribute::NotNull;
            if($key == "ai" && $val)
                $attr += FieldAttribute::AutoIncrement;
            if($key == "current_time" && $val){
                $attr =  FieldAttribute::CurrentTime; //The Assignment operator is not a mistake. It is intentional.
            }

            if($key == "reference" || $key == "references"){
                                print "REF SET OK";
                $reference = $val;
            }
        }
        
        if($name != null && $type != null){
            $field = DBO::createField($name, $type, $attr);
            if(isset($reference))
                $field->setReference($reference);
            return $field;
        }else{
            if($type == null)
                throw new \Exception("DBO: No type given");
            if($name == null)
                throw new \Exception("DBO: No name given");
        }
        return null;
    }

}



class Table{
    private $cursor_pos = 0;
    private $m_fields  = [];
    private $name;
    
    public function __construct($name, Array $fields = []){
        if(!$this->validFields($fields)){
            throw new \Exception("Table initialization failed: Wrong type of parameter given. \n");
            return null;
        }
        if(gettype($name) != 'string')
            throw new \Exception("Table initialization failed: Name must be a string.");
        $this->name = $name;
        $this->m_fields = $fields;        
    }

    public function getName(){
        return $this->name;
    }

    /** 
     * Check whether all fields are of correct type
     **/ 
    public function validFields($fields){
        foreach($fields as $field){
            if(!($field instanceof Field)){
                return false;
            }
        }
        return true;
    }


    /**
     * Counts the number of Fields in the table
     **/
    public function getWidth(){
        return count($this->m_fields);
    }

    /**
     * Returns the fields as an array
     **/
    public function getFields(){
        return $this->m_fields;
    }
}

/**
 * DBO also acts as a static factory 
 **/
class DBO extends Table{
    /**
     * Default constructor. Creates a table when constructed.
     **/
    public function __construct(Array $fields=[]){
        parent::__construct($fields);
    }

    /**
     * Field Creator
     **/
    public static function createField($name, $type=null, $attr=null){
        if (is_array($name))
            return FieldFactory::createField($name);
        else{            
            return new Field($name, $type, $attr);
        }
    }

    /**
     * Table Constructor. Use this instead of dynamically creating a DBO object.
     **/
    public function createTable($tableName, Array $fields = []){
        $param = [];
        foreach($fields as $key => $field){
            if($field instanceof Field)
                $param []= $field;            
            if(isset($key) && gettype($key) == 'string'){
                if(gettype($field) == "string" && $field == "pk") 
                    $param []= DBO::createField(['name' => $key,
                                                 'type' => 'int',
                                                 'pk'   => true,
                                                 'nn'   => true,
                                                 'ai'   => true]);
                elseif(gettype($field) == "string")
                    $param []= DBO::createField(['name' =>$key,
                                                 'type' =>$field]);
                if(is_array($field)){
                    $fparam = $field;
                    $fparam ['name'] = $key;
                    $param []= DBO::createField($fparam);
                }
            }            
        }
        return new Table($tableName, $param);
    }

    public static function getType($val){
        $type = null;
        if($val == 'int')
            $type = FieldType::Integer;
        if($val == 'float')
            $type = FieldType::Double;
        if($val == 'string')
            $type = FieldType::String;
        if($val == 'text')
            $type = FieldType::Text;
        if($val == 'timestamp')
            $type = FieldType::TimeStamp;
        if($val == 'createtime')
            $type = FieldType::CreateTimeStamp;
        if($val == 'updatetime')
            $type = FieldType::UpdateTimeStamp;
        //$val;
        return $type;
    }

    public static function getAttribute($key){
        $attr = null;
        if($key == 'fk' )
            $attr += FieldAttribute::ForeignKey;
        if($key == 'pk' )
            $attr += FieldAttribute::PrimaryKey;
        if($key == 'uq' )
            $attr += FieldAttribute::Unique;
        if($key == "nn" )
            $attr += FieldAttribute::NotNull;
        if($key == "ai")
            $attr += FieldAttribute::AutoIncrement;
        if($key == "current_time"){
            $attr =  FieldAttribute::CurrentTime; //The Assignment operator is not a mistake. It is intentional.
        }
        return $attr;
    }

}

?>