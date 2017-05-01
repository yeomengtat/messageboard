<?php
namespace Xodebox\db;
use Xodebox\Database as Database;
use Xodebox\Model;
use Xodebox\ModelFetcher;

interface RepositoryInterface{
    public function find($id);
    public function save(Model $m);
    public function remove(Model $m);    
}

class RepositoryLayer implements RepositoryInterface{
    protected $dbo = null;
    protected $dbl = null;
    protected $attachedTable = null;
    protected $attachedColumns = null;
    protected $pkey = null;
    
    public function __construct(Database $db){
        $this->dbo = $db->getDatabase();
        $this->dbl = $db->getDatabaseLayer();
    }

    public function setTable($name){
        $this->attachedTable = $name;
        return $this;
    }

    public function setPrimaryKey($col_name, $data){
        $this->pkey = ['key' => $col_name, 'value' => $data];
        return $this;
    }

    public function attachColumn($name){
        $this->attachedColumns []= $name;
        return $this;
    }

    /**
     * Check if the table exists.
     * For now, we are not going to validate the columns
     **/
    public function verifyTable(){
        if($this->attachedTable == null)
            return false;
        return $this->dbl->tableExists($this->attachedTable);
    }

    public function find($id){
        if (!$this->verifyTable())
            return null;
        $result = $this->dbl->findRecords($this->attachedTable,
                                [$this->pkey['key'] => $this->pkey['value']]
        );
        if(count($result) == 0)
            return null;
        return $result[0];
    }

    public function where(Array $conds, $limit = 0, $order = [], $sort=null){
        if (!$this->verifyTable())
            return null;
        $result = $this->dbl->findRecords($this->attachedTable,
                                          $conds,
                                          null,
                                          true,
                                          $limit,
                                          $order,
                                          $sort
        );
        return $result;
    }

    public function like(Array $conds){
        if (!$this->verifyTable())
            return null;
        $ncols ['like']= $conds;
        $result = $this->dbl->findRecords($this->attachedTable,
                                          $ncols
        );
        return $result;
    }

    public function jsonField($col, Array $conds){
        if (!$this->verifyTable())
            return null;
        $keyArr = [];
        foreach($conds as $key => $value){
            $keyArr [$col]= "'{$key}:\"{$value}\"'";            
        }
        
        $ncols ['RLIKE'] = $keyArr;
        $result = $this->dbl->findRecords($this->attachedTable,
                                          $ncols,
                                          '*',
                                          false
        );
        //var_dump($this->dbl->dumpQueries());;

        return $result;
    }


    public function findAll($cols = null, $limit = 0, $order = []){
        if($cols == null)
            $cols = "*";
        if (!$this->verifyTable())
            return null;

        $result = $this->dbl->findRecords($this->attachedTable,
                                          null,
                                          $cols ,       
                                          true,
                                          $limit,
                                          $order
        );
        
        if($cols != null && $result && count($result[0])==1 ){
            $result = $this->filterByValue($result, $cols);
        }
        return $result;
    }

    /**
     * Used by find all
     **/
    private function filterByValue($result, $cols){
        $ret = [];
        foreach($result as $item){
            $ret []= $item[$cols];
        }
        return $ret;
    }


    /**
     *
     **/
    public function save(Model $m){
        $this->dbl->clearQueries();
        $vars = get_object_vars($m);
        
        /**This is not necessary but we are doing it to give user control over which data to save **/
        $data = [];
        if(count($this->attachedColumns) == 0)   //Need to attach columns first
            return null;

        //print "Attached ::<br/>";
        //var_dump($m);
        //var_dump($this->attachedColumns);
        foreach($this->attachedColumns as $col){
            //print "<br />";
            $item = null;
            
            if(isset($vars[$col]))  //Fixme: UPDATE ONLY ATTRIBUTES WHICH HAS BEEN CHANGED.
                $item = $vars[$col];

            
            if($item && $item instanceof Model){
                $fkey = $item->getFieldMap()[$item->getPrimaryKey()['name']];
                if($fkey == null){  //FIXME
                    $fkey = $item->save();
                }
                $data [$col] = $fkey;
            }elseif($item && $item instanceof Xodebox\ModelFetcher){
                //$fkey = $item->getFieldMap()[$item->getPrimaryKey()['name']];
                $models = $item->getModels();
                if(isset($models[0]))
                    $fkey = $models[0]->getFieldMap()[$models[0]->getPrimaryKey()['name']];
                else
                    $fkey = null;
                
                $data [$col] = $fkey;
            }
            elseif($item)
                $data [$col]= $item;
            
        } //end of for loop
        //var_dump($data);

        //Resolve models and modelfetchers for unattached columns
        //Unattached models are not saved on insert or update
        foreach($data as $key => $item){
            if($item instanceof ModelFetcher){
                /*
                $models = $item->getModels_fixed();
                //var_dump($models);
                var_dump(count($models));
                
                if(isset($models[0]))
                    $fkey = $models[0]->getFieldMap()[$models[0]->getPrimaryKey()['name']];
                else
                    $fkey = null;
                    $data [$col] = $fkey;*/
                unset($data[$key]);
            }

            if($item instanceof Model){
                $fkey = $item->getFieldMap()[$item->getPrimaryKey()['name']];
                $data [$col] = $fkey;
                
            }
            
        }
        
        //If primary key does not exists then create.
        if($this->pkey == null){            
            $result = $this->dbl->addRecords($this->attachedTable, $data);
            
            $id = $this->dbl->getLastInsertId();
            
            //var_dump($id);
            //print $this->dbl->dumpQueries()[0]['statement'];
        }else{
            $this->dbl->updateRecords($this->attachedTable, $data, [$this->pkey['key'] => $this->pkey['value']]
            );
            //print $this->dbl->dumpQueries()[0]['statement'];
            //var_dump($data);
            //print "<br > <br>";
            //print $this->dbl->dumpQueries()[0]['statement'];
            //print "<br />";
            //print_r( $this->dbl->dumpQueries()[0]['bind']);

        }
        //print "<br > <br>";
        //print $this->dbl->dumpQueries()[0]['statement'];
        //print "<br />";
        //print "\n";
        //print_r( $this->dbl->dumpQueries()[0]['bind']);

        $this->dbl->executeQueries();
        return $this->dbl->getLastInsertId();
        //return true;
    }

    public function remove(Model $m){
        $key = $m->getPrimaryKey();
        if($key == null && isset($key['val']) && isset($key['name']) ){  //The primary key must be set before removal.
            return false;
        }
        
        $this->dbl->deleteRecords($m->getTableName(),
                                  [$key['name'] => $key['val']]
        );        
        $this->dbl->executeQueries();        
    }
}

?>