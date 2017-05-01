<?php

namespace Xodebox;

/**
 * Handles non-caught Exceptions.
 **/
class ExceptionHandler{
    private $debug_mode = false;
    public function __construct(){
        @set_exception_handler([$this, 'exception_handler']);
        if(isset(Config::$debug_mode) && Config::$debug_mode)
            $this->debug_mode = true;
                                       
    }
    public function exception_handler($exception){

        if($exception instanceof \PDOException){  
            print "Cannot connect to the database. Make sure the user name and password is correct. ";
        }else{
            print "Error occured: ". $exception->getMessage() . "\n";
            if($this->debug_mode){
                print "  File: {$exception->getFile()} Line". $exception->getLine();
            }
        }
    }
}
?>