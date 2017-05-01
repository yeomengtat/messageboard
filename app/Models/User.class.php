<?php
use Xodebox\Model;

class User extends Model{
    private $id, $name, $hash;

    public static function creator(){
        return [
            'id'    => 'pk',
            'name'  => 'string',
            'hash'  => 'string'
        ];
    }

    /*
    public function addRelations(){
        return [
            
        ];
        }*/
}


?>