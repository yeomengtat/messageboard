<?php
namespace Xodebox;

use Xodebox\db\RepositoryLayer as RepositoryLayer;

interface ModelCreator
{
    public static function creator();
}

class ModelFetcher{
    private $model_class;
    private $model_condition;

    public function __construct($class, Array $cond){
        $this->model_class = $class;
        $this->model_condition = $cond;
    }

    public function getModels(){
        $model = new $this->model_class();
        return $model->findWhere($this->model_condition);
    }

    //TODO: Deprecate getModels and use this
    public function getModels_fixed(){
        $models = [];

        $model = new $this->model_class();
        $model_class = $this->model_class;
        $arr= $model->findWhere($this->model_condition);
        if($arr == null){
            $arr = [];
            return [];
        }
        foreach($arr as $item){            
            $pk_name = $model->getPrimaryKey()['name'];
            $pk = $item[$pk_name];
            $models [] = $model_class::fetch($pk); 
        }
        return $models;
    }

    public function find($cond){
        $model = new $this->model_class();
        $con = array_merge($this->model_condition, $cond);
        return $model->findWhere($cond);
    }

    public function findObject($cond){
        $model_class = $this->model_class;
        //$model_class = "Product";
        $model = new $model_class();
        //print $model_class;
        $c = $this->find($cond);
        if($c){
            $pk_name = $model->getPrimaryKey()['name'];
            $pk = $c[0][$pk_name];
            return $model_class::fetch($pk);
        }
        return null;
    }

    public function all($models = false){
        if($models == true)
            return $this->getModels_fixed();
        return $this->getModels();
    }

    public static function __callStatic($name, $arg){
        print "Not implemented yet.";
    }

    public function __invoke(){
        $arr = $this->getModels_fixed();
        if(isset($arr[0]))
            return $arr[0];
        //print "Not implemented yet.";
    }

    public function __get($arg){
        //return $this->getModels()[0];
        if($this->__invoke())
            return $this->__invoke()->{$arg};
        return "Not implemented yet.";
    }

    public function __toString(){
        return "[ModelFetcher for {$this->model_class}]";
    }
}

/**
 *  Bugs:
 *   - Does not allow the derived classes to declare private variables.
 *
 * TODO: Remove direct object retrieval from find() method in Model.
 *    Instead return ModelFetcher for all Models.
 **/
class Model implements ModelCreator{
    private $fieldMap = [];
    private $pk = null;
    private $repo = null;
    
    private $tableName = null;
    private $relations = [];
    //private static $static_repo = null;

    private $callbacks = [];
    
    public function __construct(){
        $this->tableName = $this->defaultTable();
        $this->getChildAttribs();
        $this->setPrimaryKey("id");
        
        if($this->repo == null){  //TODO: REMOVE THIS AND USE A DEPENDANCY MANAGER
            $db   = new \Xodebox\Database();
            $this->repo = new RepositoryLayer($db);
            $this->setRepository($this->repo);
            $this->relations = $this->addRelations();
        }
    }

    
    public function getFieldMap(){
        return $this->getChildAttribs();
    }   
    

    public function setRepository($r){
        $r->setTable($this->getTableName());
        $this->getChildAttribs();
        foreach($this->fieldMap as $col){
            $r->attachColumn($col);
        }
        //print_r($this->fieldMap);
        $this->repo = $r;
    }

    /**
     * Creates a database object for this model.
     **/
    public function getChildAttribs(){
        $attribs = [];
        $ref = new \ReflectionClass(get_class($this));

        foreach ($ref->getProperties() as $property)
        {
            $attribs[$property->name] = $this->{$property->name};
            $this->fieldMap[$property->name] = $property->name;
        }
        /*
        if(!$this->dbl->tableExists($this->getTableName())){
            $this->db->createTable($this0>getTableName())
            }*/
        return $attribs;
    }

    public static function defaultTable(){
        //$className = get_class($this);
        $className = static::class;
        $name = preg_replace("/Model/", "", $className);
        $name = preg_replace_callback("/([A-Z])/", 'self::convert', $name);
        $name = ltrim($name, '_');
        $name .= "_table";
        return $name;
    }

    private static function convert($match){
        $word = strtolower($match[0]);
        return "_{$word}";
    }

    

    protected function setTableName($name){
        $this->tableName = $name;
    }

    public function setPrimaryKey($key){
        $this->pk['name'] = $key;
    }

    public function getPrimaryKey(){
        return $this->pk;
    }

    public function save(){
        if($this->repo == null)
            return false;
        $insertId = $this->repo->save($this);
        
        if($insertId){
            $this->find($insertId);
            return true;
        }
        return false;
    }

    /**
     *  TODO: Find should be static
     *  TODO: REFACTOR ASAP. 
     **/
    public function find($id){        
        if($this->repo == null )
            return false;
        if($this->pk == null)
            $this->pk['name'] = "id";
        
        $this->repo->setPrimaryKey($this->pk['name'], $id);
        $vars = $this->repo->find($id);
        
        if($vars == null)
            return null;
        
        //Add the attributes missing from the table
        $missed = array_diff_key($this->getFieldMap(), $vars);
        $vars   = array_merge($vars, $missed);

        foreach($vars as $vname => $var){
            //if there is a relation load the model using the key
            //            var_dump($this->relations);
            //var_dump($vname);
            if($this->relations && in_array($vname, array_keys($this->relations)) ){
                $relType = "single";
                $relation = $this->relations[$vname];
                $name = explode(".", $relation);
                $class =  $name[0];
                $model = new $class();
                if(!isset($name[1]))
                    $name[1] = "id";
                $key = $name[1];

                if(isset($name[2]))
                    $relType = $name[2];

                
                if($relType == "single")  //fetch a object
                {                    
                    $ret = $model->find($var);
                    if($ret)
                        $this->{$vname} = $model;
                }

                //Use model fetcher for one to one relationship. The object must be invoked to instantiate its model
                if($relType == 'both'){
                    $myKey = $vars[$this->getPrimaryKey()['name']];
                    $model = new ModelFetcher($class, ["$key" => "$myKey"]);
                    if($model)
                        $this->{$vname} = $model;
                }

                if($relType == 'many')  //fetch many objects
                {
                    //Register a fetch function to be called when requested.
                    //print "Fetching many objects for $vname..\n";
                    $myKey = $vars[$this->getPrimaryKey()['name']];
                    //$models = $model->findWhere(["$key" => "$myKey"]);
                    $models = new ModelFetcher($class, ["$key" => "$myKey"]);
                    //var_dump($models);
                    //$fetch = $this->findWhere([]);
                    $this->{$vname} = $models;
                }
                
            }else            
                $this->{$vname} = $var;
        }
        $this->pk['val'] = $id;
        return $vars;
    }

    /**
     * Fetch and return all in Model format
     **/
    public function findAll($param = null, $limit=0, $order = [], $sort=null){
        $fields = [$this->getPrimaryKey()['name']];

        //Process the fields argument into a string
        if($param != null){
            if(is_string($param))
                $fields[]= $param;
            if(is_array($param))
                $fields = array_merge($fields, $param);
        }
        
        $f = implode(', ', $fields);
        $keys = $this->findAllRecords($f, $limit, $order, $sort);
        
        if($keys == null || $keys == false)//if record is not found, there will be no key
            return [];
		

        $vars = [];
        $mclass = get_class($this);
        //var_dump($mclass);
        foreach($keys as $key){
            //$var = new $mclass();
            //$var->find($key);
            $var = $mclass::fetch($key);
            $vars[] = $var;            
        }
        return $vars;
    }


    /**
     * Fetch and returns in  array format
     **/
    public function findAllRecords($param = null, $limit=0, $order=[], $sort=null){
        if($this->repo == null )
            return false;
        $keys = $this->repo->findAll($param, $limit, $order, $sort);
        return $keys;
    }

    /**
     * findWhere(['col_name' => 'value']);
     * @return Array record
     **/
    public function findWhere(Array $conds, $limit=0, $order=[], $sort=null){
        if($this->repo == null )
            return false;
        $vars = $this->repo->where($conds, $limit=0, $order=[], $sort=null);        
        return $vars;
    }

    /**
     * findWhere(['col_name' => 'value']);
     * @return Array record
     **/
    public function findLike(Array $conds){
        if($this->repo == null )
            return false;
        $vars = $this->repo->like($conds);        
        return $vars;
    }

    public function remove(){
        if($this->repo == null || $this->pk == null)
            return false;
        if(!isset($this->pk['val']))
            return false;
        return $this->repo->remove($this);
    }

    public function getTableName(){
        return $this->tableName;
    }

    /**
     * For debug purpose only
     **/
    public function __toString(){
        $ret = "";
        $ref = new \ReflectionClass(get_class($this));
        $ret = "Model ".get_class($this)." {\n";
        $attribs = $ref->getProperties();
        
        if(count($attribs) <= 0)
            return "Empty model";
        foreach ($attribs as $property)
        {
            $val = $this->{$property->name};
            if($val instanceof Model)
                $val = "[Object ".get_class($val). "]";
            $ret .= "\t";
            $ret .= "{$property->name} = {$val}";
            $ret .= "\n";
        }
        $ret .= "}";
        
        return $ret;
    }

    /**
     * Uses late static Binding to call child's creator method
     **/
    public static function createDBO(\Xodebox\Database $db){
        $param = static::creator();
        if($param != null && $db != null){
            $dbl = $db->getDatabaseLayer();
            //$this->db->
            $table = self::defaultTable();
            
            if($dbl->tableExists($table)){
                print "Table exists for this Model.\n";
                $fields = $dbl->getAllFields($table);
                $in_table = [];
                foreach($fields as $field){
                    $fname = $field['Field'];
                    $in_table [$fname]= $field['Type'];
                }

                //remove arrays in param value 
                foreach($param as $key => $type){
                    if(is_array($type)){
                        $param[$key] = $type['type'];
                    }
                    $param[$key] = $dbl->convertTypeString($param[$key]);
                }

                $new = array_diff_key($param, $in_table);
                $remove = array_diff_key($in_table, $param);

                //print_r($param);
                //print_r($in_table);
                $change = false;
                //print "New = \n";
                //print_r($new);
                if(count($new)>0){
                    $dbl->alterTable($table, $new, 'add');
                    $change = true;
                    //$dbl->executeQueries();
                    //var_dump($dbl->success());
                    
                }
                
                //print "Old = \n";
                //print_r($remove);
                if(count($new)>0){
                    $dbl->alterTable($table, $remove, 'drop');
                    $change = true;
                }
                $dbl->executeQueries();
                //Table exists for this Model
                return $change;
            }else{
                print "Table does not exist for this Model.\n";
                //foreach($param as $field){
                    //}
                $dbl->createTable($table, $param);
                //print $dbl->dumpQueries()[0]['statement'];
                $dbl->executeQueries();
                return true;
            }
        }
    }

    
    public function __set($name, $val){
        $this->{$name} = $val;
    }
    
    public static function creator(){
        return null;
    }

    public function addRelations(){
    }

    /**
     * Use this as a user defined constructor
     **/
    protected function init(){

    }

    
    /**
     * Static version of findAll method
     **/
    public static function all($param = null, $limit = 0, $cond=[], $sort=null){
        $m = new static();        
        return $m->findAll($param, $limit, $cond);
		
		
    }

    /**
     * Static version of find method
     **/
    public static function fetch($id){
        $m = new static();
 
        $res = $m->find($id);
        if(!$res)
            return null;
        return $m;
    }

    /**
     * Static version of findAllRecords method
     **/
    public static function allRecords($param = null){
        $m = new static();
        return $m->findAllRecords($param);
    }

    /**
     * Static version of findWhere method
     * @return Array Models
     **/
    public static function where($param = null, $limit = 0, $order = [], $sort = null){
        $m = new static();
        $arr = [];
        $mclass = static::class;
        $results = $m->findWhere($param, $limit, $order, $sort);
        foreach($results as $row){
            $obj = new $mclass();
            $obj->find($row['id']);  //FIXME: Use getPrimary key to automatically find the primary key name later
            $arr []= $obj;
        }
        return $arr;
    }

    /**
     * Static version of findLike method
     * @return Array Models
     **/
   public static function like($param = null){
        $m = new static();
        $arr = [];
        $mclass = static::class;
        $results = $m->findLike($param);
        foreach($results as $row){
            $obj = new $mclass();
            $obj->find($row['id']);  //FIXME: Use getPrimary key to automatically find the primary key name later
            $arr []= $obj;
        }
        return $arr;
    }

    /**
     * Find by JSON attribute
     **/
    public function jsonField($col, Array $keys){
        $results = $this->repo->jsonField($col, $keys);
        $arr = [];
        $mclass = static::class;
        foreach($results as $row){
            $obj = new $mclass();
            $obj->find($row['id']);  //FIXME: Use getPrimary key to automatically find the primary key name later
            $arr []= $obj;
        }
        return $arr;
    }

    /**
     * Find by JSON Attribute static
     **/
    public function findJSON($col, Array $keys){
        $m = new static();
        $arr = [];
        $mclass = static::class;
        $results = $m->jsonField($col, $keys);
        
        return $results;
    }
    
    public function makeForeignKey($className){
        $obj = new $className;
        $pk_name = $obj->getPrimaryKey()['name'];
        $table_name = $obj->getTableName();
        return ['type' => 'int',
                'references' => "{$table_name}.{$pk_name}",
                'fk'     => true];
    }
}

?>