<?php
namespace Xodebox;
/**
public class HTMLTables extends HTMLElement{
    //private cssClass =
    public function
    }**/
class HTMLElement{
    private $attributes = [];
    private $tag_name;
    private $content;
    private $singleTag = false;
    private $indent = 0;

    public function __construct($tag_name, $attr = null, $data = null, $endTag = false){
        $this->tag_name = $tag_name;
        $this->singleTag = $endTag;
        $this->attributes = $attr;
        $this->content  = $data;
    }

    public function getAttrString(){
        $ret = "";
        if(is_array($this->attributes)){
            foreach($this->attributes as $key => $value){
                $ret .= " {$key}=\"{$value}\" ";
            }
        }
        return $ret;
    }

    private function getIndent(){
        $tab = "";
        for($i=0; $i<$this->indent; $i++){
            $tab .= "x";
        }
        return $tab;
    }

    public function addIndent($val=0){
        if($val=0)
            $this->indent++;
        elseif($val > 0)
            $this->indent+=$val;
    }
    
    public function getHTML(){
        $name = $this->tag_name;
        $tag1 = "\n<$name {$this->getAttrString()}>";
        $tag2 = "</$name>";
        $tab = $this->getIndent();
        $content = $this->getContent();
        if($this->singleTag)
            return $tab.$tag1;
        else
            return "$tab{$tag1}\t{$content}\n{$tag2}";
    }

    public function __toString(){
        return $this->getHTML();
    }

    public function getTagName(){
        return $this->tag_name;
    }

   
    public function getContent(){
        $content = $this->content;
        if($content instanceof HTMLElement)
            return $content->getHTML();
        elseif(is_array($content)){
            $res= "";
            foreach($content as $el){
                //$el->addIndent($this->indent + 1);
                if($el instanceof HTMLElement)
                    $res .= $el->getHTML();
                else
                    $res .= $el;
            }
            return $res;
        }
        else
            return "\n\t".$content;
    }

    public function getRawContent(){
        return $this->content;
    }

    public function setContent($data){
        $this->content = $data;
    }

    /**
     * Appends content to the current content data
     **/
    public function addContent($item){
        $content = $this->content;
        if(!is_array($content))
        {
            $this->content = [];
            $this->content[] = $content;
        }
        
        //TODO: If item is an array, extract it.
        $out = [];
        if(is_array($item)){
            foreach($item as $i){
                $this->addContent($i);
            }
        }else        
            $this->content []= $item;
        return $this;
    }

    public static function createArray($name, Array $contentArr, $attr=null, $end=false){
        $arr = [];
        foreach($contentArr as $content){
            $arr[] = new HTMLElement($name, $attr, $content, $end);
        }
        return $arr;
    }

}



class HTMLTable extends HTMLElement{
    private $rowElements = [];

    //Generate rows for normal table
    private function  generateRows($data){

        $col_names = [];
        $rowElements = [];

        //var_dump($data[0]);
        if($data){
            $tr = new HTMLElement("tr");            
            $first = $data[0];
            $col_names = array_keys($first);
            $tds = HTMLElement::createArray('th', $col_names);
            $tr->setContent($tds);
            $rowElements[] = $tr;
        }
        
        
        foreach($data as $row){
            $tr = new HTMLElement("tr");
            $cols = [];
            for($i=0; $i< count($col_names); $i++){
                $val = $row[$col_names[$i]];
                if(!is_array($val)){
                    $td = new HTMLElement("td", null, $val);
                    $cols []= $td;
                }else{
                    $td = new HTMLElement("td", null, "");
                    $cols []= $td;
                }
            }
            $tr->setContent($cols);            
            $rowElements[] = $tr;
        }
        return $rowElements;
    }
    
    public function __construct(Array $data){
        parent::__construct("table", ['border' => '1px', 'class' => 'table table-bordered table-striped']);
        $this->setContent($this->generateRows($data));        
    }    
}

class HTML5Template{
    private $metahead = "";
    private $root = null;
    private $body, $head;
    public function  __construct(){
        $this->root = new HTMLElement("html");
        $this->body = new HTMLElement("body");
        $this->head = new HTMLElement("head");
        $this->root->setContent([$this->head, $this->body]);
    }

    public function getBody(){
        return $this->body;
    }

    public function getHead(){
        return $this->head;
    }

    public function _print(){
        print("<!doctype html>\n\t");
        print($this->root->getHTML());
    }
}

class HTMLDropdown extends HTMLElement{
    private function setData(Array $data, $attr = null){
        $content = [];
        if($attr == null)
            $attr = [];
        foreach($data as $key => $value){
            $attr['value'] = $key;
            $content[] = new HTMLElement("option", $attr, $value);
        }
        $this->setContent($content);
    }
    
    public function __construct(Array $data, $attr = null){
        parent::__construct("select", $attr);
        $this->setData($data);
    }
    
}



class HTMLContextMenu extends HTMLElement{
    private $cbuttons = null;
    private $attr = null;
    private $name = null;
    private $preData = null;
    
    public function __construct($name, $attr=null, $data, $preData = null){
        $mAttr = null;
        if(isset($attr['id']))
            $mAttr['id'] = ($attr['id']);
        if(!isset($attr['class']))
           $mAttr['class'] = 'btree-context-menu';
        parent::__construct('div', $mAttr);
        $this->cbuttons = $data;
        $this->attr = $attr;
        $this->name = $name;
        $this->preData = $preData;
        $this->createElements();
    }

    private function createButtonList(){
        $cb = [];
        foreach($this->cbuttons as $key => $btn){
            $bt = new HTMLElement('button', ['id'=>$key, 'class' => 'btn btn-default'], $btn);
            $li = new HTMLElement('li');
            $li->setContent($bt);
            $cb []= $li;
        }
        $ul  = new HTMLElement("ul", null);
        $ul->setContent($cb);
        return $ul;
    }

    private function createElements(){
        $attr = $this->attr;
        if($attr == null)
            $attr = [];
        if(isset($attr['id']))
            $attr['id'] = 'cm_'.$attr['id'];
        $attr['class'] = 'panel panel-default';
        $cHeading    = new HTMLElement('div', ['class' => 'panel-heading'], $this->name);

        $btnList     = $this->createButtonList();

        
        if($this->preData !=  null)
        {
            $pd = new HTMLElement('div', [],$this->preData);
            $btnList = [$pd, $btnList];
        }                        

        $cArea       = new HTMLElement('div', ['class' => 'panel-body'], $btnList);
        $contextMenu = new HTMLElement('div', $attr, [$cHeading, $cArea]);
        $this->setContent($contextMenu);
    }
}

class HTMLHelpers{
    public static function arrayFromObjects(Array $array, $method, $object=null, $uKey = null){
        $ret = [];
        foreach($array as $key => $val){
            foreach($val as $col){
                if($uKey == null)
                    $pkey = $key;
                else
                    $pkey = $val->{$uKey}();
                if($object  == null)                    
                    $ret[$pkey] = $val->{$method}();
                else
                    $re[$pkey]  = $val->{$object}->{$method}();
            }
        }
        return $ret;
    }

    public static function createRadioButton($name, $value, $label, $attr=[]){
        $attr['type']  = 'radio';
        $attr['name']  =  $name;
        $attr['value'] =  $value;
        return new HTMLElement('input',
                               $attr,
                               $label);
    }

    public static function createLabelOutput($label, $outId, $placeholder = null){
        return new HTMLElement("p", null,
                    ["<b>$label: </b>", new HTMLElement('span',
                                                 ['id' => $outId])
                    ]);
    }

    public static function createTextBox($label, $name, $type="text"){
        return new HTMLElement('div', ['class' => 'form-group'], 
                               [
                                   new HTMLElement('label', ['for' => 'input_'.$name ], $label . ":"),
                                   new HTMLElement("input", ['type' => $type,
                                                             'name' => $name,
                                                             'id'   => 'input_'.$name,
                                                             'class' => 'form-control'
                                   ])
                                                   
                               ]
        );
    }
    
    public function displayTable(Array $object){
        $table = new HTMLTable($object);
        return $table->getHTML();
    }

    public function displayDropdown(Array $data, $attr = null){
        $dropDown = new HTMLDropdown($data, $attr);
        return $dropDown->getHTML();
    }

    public function getBasicHTML(){
        $meta = "<!doctype html>\n";
        $head = new HTMLElement("head");
        
        $body = new HTMLElement("body");
       
        $html = new HTMLElement("html");
        $html->setContent([$head, $body]);
        return $meta.$html;
    }
}


 class HTMLImport{
    public static function css($filename){
        return new HTMLElement('link', ['type' => 'text/css',
                                        'rel'  => 'stylesheet',
                                        'href'  => $filename], null, true);
    }

    public static function js($filename){
        return new HTMLElement('script', ['type' => 'text/javascript',
                                          'src'  => $filename]);
        
    }
}
?>