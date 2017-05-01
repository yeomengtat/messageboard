<?php
use Xodebox\Model;

class User extends Model{
    public $id, $name, $hash;

    public static function creator(){
        return [
            'id'    => 'pk',
            'name'  => 'string',
            'hash'  => 'string'
        ];
    }

    public function setPassword($password){
        $this->hash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function checkPassword($password){
        return password_verify($password, $this->hash);
    }

    /*
    public function addRelations(){
        return [
            
        ];
        }*/
}


?>