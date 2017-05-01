<?php
namespace Xodebox;

class Template{
    protected $file;
    protected $values = [];
    private $data = [];

    public function __construct($file){
        $this->file = $file;
    }

    public function set($key, $value){
        $this->values[$key] = $value;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function output(){
        if(!file_exists($this->file)){
            return "Template Engine: Template {$this->file} not found.";
        }
        //$content = file_get_contents($this->file);
        //var_dump($this->data);
        extract($this->data);
        
        ob_start();
        //include "data://text/plain;base64,".base64_encode($content);
        //include($this->file);
        //eval($content);
        //print $content;
        //print $content;
        readfile($this->file);
        $output = ob_get_contents();        
        ob_end_clean();

        //var_dump($this->values);
        ob_start();
        eval('?>'. $output . '<');        
        //print "A";         
        $output = ob_get_contents();
        $output = rtrim($output, "<"); //FIx < bug
        foreach($this->values as $key => $value){
            $replace = '{'.$key.'}';
            $output   = str_replace($replace, $value, $output);
        }        
        ob_end_clean();
            
        return $output;
    }
}
?>