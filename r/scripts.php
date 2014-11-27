<?php

error_reporting(E_ERROR);

require "phpmailer/PHPMailerAutoload.php";

function rgb($hex){
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));
    return array(
        "r" => $r,
        "g" => $g,
        "b" => $b
    );
}

class dataset{
    public $array;
    public $maxCol = 1;
    public $rows;

    function toColumns($cap,$butt,$doWhat){
        foreach($this->array as $x => $line){
            $line = $doWhat($line);
            foreach($line as $y=> $val){
                $columns[$y][] = $val;
            }
        }
        foreach($columns as $y => $column){
            $output[] = $cap;
            $output = array_merge($output,$column);
            $output[] = $butt;
        }
        return implode(PHP_EOL,$output);
    }
    
    
}

class csv extends dataset{

    function __construct($input){
        $array = explode(PHP_EOL,$input);
        foreach($array as $line){
            if(empty($line)) continue;
            $this->array[] = explode(",",$line);
        }
    }

}

class json extends dataset{
    
    function __construct($json){
        $this->array = json_decode($json,true);
    }
    

    function toRows($array){
        static $rowNum = 1;
        static $colNum = 1;
        
        if(is_null($array)) $array = $this->array;
        $rows = &$this->rows;
        
        foreach($array as $key => $val){
            if(!is_numeric($key)){
                $rows[$rowNum][$colNum] = $key;
                $colNum++;
                if($colNum > $this->maxCol) $this->maxCol = $colNum;
                if(is_array($val)){
                    $this->toRows($val);
                }else{
                    if(!empty($val)) $rows[$rowNum][$colNum] = $val;
                    $rowNum++;
                }
                $colNum--;
                continue;
            }
            if(!empty($val)) $rows[$rowNum][$colNum] = $val;
            $rowNum++;
        }
    }
    
    function fillRows($filler){
        for($rowNum = 1; $rowNum <= count($this->rows); $rowNum++){
            $row = &$this->rows[$rowNum];
            end($row);
            $toPad = abs($this->maxCol - key($row));
            while($toPad > 0){
                $row[] = $filler;
                $toPad--;
            }
        }
    }

    function toTable(){
        static $output;
        $rows = &$this->rows;
        $output[] = "<table>";
        foreach($rows as $rowNum => $row){
            $output[] = "<tr>";
            foreach($row as $colNum => $val){
                $rowspan = 1;
                $rowSearch = $rowNum + 1;
                while($rowSearch <= count($rows)){
                    if(!isset($rows[$rowSearch][$colNum])) $rowspan++;
                    else break;
                    $rowSearch++;
                }
                if($rowspan > 1) $output[] = "<td rowspan=\"$rowspan\">$val</td>";
                else $output[] = "<td>$val</td>";                    
            }
            $output[] = "</tr>";
        }
        $output[] = "</table>";
        return implode(PHP_EOL,$output);
    }
    
    function toLists($array = null){
        $output = &$this->output;
        if(is_null($array)) $array = $this->array;
        $output .= "<ul>";
        foreach($array as $key => $val){
            $output .= "<li>";
            if(is_array($val)){
                $output .= $key;
                if(!empty($val)) $this->toLists($val);
            }else{
                $output .= $val;
            }
            $output .= "</li>";
        }
        $output .= "</ul>";
    }

}

class html extends dataset{
    public $html;
    public $tags;
    public $text;
    public $chars;

    function getTags(){
        preg_match_all("/\<([^\>]+)\>/",$this->html,$tags);
        $tags = $tags[0];
        for($x=0; $x<count($tags); $x++){
            $tagsOut[htmlentities($tags[$x])]++;
        }
        $this->tags = $tagsOut;
        return $tagsOut;
    }
    
    function getText(){
        $string = preg_replace("/\<([^\>]+)\>/","<>",$this->html);
        $string = preg_replace("/\&([^\;]+);/","<>",$string);
        preg_match_all("/\>([^\<]+)\</",$string,$text);
        $text = $text[1];
        for($x=0; $x<count($text); $x++){
            $line = trim($text[$x]);
            if(!empty($line)) $textOut[] = $line;
        }
        $this->text = $textOut;
        return $textOut;
    }
    
    function getSpecialChars(){
        preg_match_all("/\&([^\;]+);/",$this->html,$chars);
        $chars = $chars[1];
        for($x=0; $x<count($chars); $x++){
            $line = trim($chars[$x]);
            if(!empty($chars)) $charsOut[] = "&amp;$line;";
        }
        $this->chars = $charsOut;
        return $charsOut;
    }
/*    
    function blanks(){
        $string = $this->html;
        
        $close = "*CLOSE*";
        $open = "*OPEN*";
        $right = "*RIGHT*";
        $amp = "*AMP*";
        $semi = "*SEMI*";
        
        $string = str_replace("</",$close,$string);
        $string = str_replace("<",$open,$string);
        $string = str_replace(">",$right,$string);
        $string = str_replace("&",$amp,$string);
        $string = str_replace(";",$semi,$string);

        $string = str_replace($open,"&lt;<span title=\"",$string);
        $string = str_replace($close,"&lt;/<span title=\"",$string);
        $string = str_replace($right,"\"></span>&gt;",$string);
        $string = str_replace($amp,"&amp;<span title=\"",$string);
        $string = str_replace($semi,"\"></span>;",$string);
        return $string;
    }
*/

    function blanks(){
        $string = $this->html;
        $string = preg_replace(array(
                "/\<([^\/\>]+)\>/",
                "/\<([^\>]+)\>/",
                "/\&([^\;]+);/",
            ),array(
                "*CLOSE*",
                "*OPEN*",
                "*AMP*"
            ),$this->html);
        $string = str_replace(array(
                "*CLOSE*",
                "*OPEN*",
                "*AMP*"
            ),array(
                "<span class=\"open\"></span>",
                "<span class=\"closed\"></span>",
                "<span class=\"amp\"></span>"
            ),$string);
        return $string;
    }

}


function sanitize($what){
	if(empty($what)) return false;
    return substr(preg_replace("/[^a-zA-Z0-9 \@\.\_\/\-\?\!\:\;\(\)\"\']/"," ", strip_tags($what)),0,500);
}

class error extends Exception{
}

class success extends Exception{
}

function dirTree($dir){
    $handle = opendir($dir);
    chdir($dir);
    while(false !== ($index = readdir($handle))){
        if(substr($index,0,1) == ".") continue;
        $tree[$index] = is_dir($index) ? dirTree($index) : "0";
    }
    closedir($handle);
    chdir("..");
    return($tree);
}

function chapters($input,$out){
    $output .= "<$out>";
    foreach($input as $key => $val){
        $output .= "<li>".ucfirst(str_replace(array("_",".html"),array(" ",""),$key));
        if(is_array($val)) $output .= chapters($val,$out);
        $output .= "</li>";
    }
    $output .= "</$out>";
    return $output;
}

?>