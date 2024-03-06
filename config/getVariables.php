<?php
function obterPost($variable,$die,$mayusculas,$recuperarId,$checkbox,$intval){
    if(isset($_POST[$variable])&&(!empty($_POST[$variable])||($_POST[$variable]=='0'))){
        if($checkbox){
            return 1;
        }else{
            $r= trim($_POST[$variable]);
            $r= ($mayusculas) ? cambiarMinusToMayusTexto($r) : $r;
            $r= ($recuperarId) ? explode(' - ',$r)[0] : $r;
            $r= ($intval) ? intval($r) : $r;
            return $r;
        }
    }else{
        if($checkbox){
            return 0;
        }else{
            return ($die) ? die() :  null;
        }
    }
}
function obterGet($variable,$die,$mayusculas,$recuperarId,$checkbox,$intval){
    if(isset($_GET[$variable])&&(!empty($_GET[$variable])||($_GET[$variable]=='0'))){
        if($checkbox){
            return 1;
        }else{
            $r= trim($_GET[$variable]);
            $r= ($mayusculas) ? cambiarMinusToMayusTexto($r) : $r;
            $r= ($recuperarId) ? explode(' - ',$r)[0] : $r;
            $r= ($intval) ? intval($r) : $r;
            return $r;
        }
    }else{
        if($checkbox){
            return 0;
        }else{
            return ($die) ? die() :  null;
        }
    }
}
function obterArray($variable,$die,$mayusculas,$recuperarId,$intval){
    if(isset($_POST[$variable])&&!empty($_POST[$variable])){
        $array=Array();
        foreach($_POST[$variable] as $v){
            $r= (is_numeric($v)) ? $v : trim($v);
            $r= ($mayusculas) ? ((is_numeric($r)) ? $r : cambiarMinusToMayusTexto($r)) : $r;
            $r= ($recuperarId) ? ((is_numeric($r)) ? $r : explode(' - ',$r)[0]) : $r;
            $r= ($intval) ? intval($r) : $r;
            array_push($array,$r);
        }
        return $array;
    }else{
        return ($die) ? die() :  null;
    }
}
function obterObjeto($variable,$die){
    if(isset($_POST[$variable])&&!empty($_POST[$variable])){
        $array=Array();
        for($x=0;$x<count(array_keys($_POST[$variable]));$x++){
            foreach($_POST[$variable][$x] as $clave => $p){
                $r= (is_numeric($p)) ? $p : trim($p);
                $_POST[$variable][$x][$clave]=$r;
            }
            $array[]=$_POST[$variable][$x];
        }
        return $array;
    }else{
        return ($die) ? die() :  null;
    }
}