<?php
if(estarLogeado1()){
    date_default_timezone_set('Europe/Madrid');
    $root       =   realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
    require_once($root.'config/bd/bdMariaBD.php');
    require_once($_SESSION['root'].'config/bd/bdMariaBD.php');
    require_once($_SESSION['root'].'config/getVariables.php');
    require_once($_SESSION['root'].'config/patrones.php');
    require_once($_SESSION['root'].'certificados/lib.php');
    require_once($_SESSION['root'].'formacion/lib.php');
    require_once($_SESSION['root'].'usuarios/lib.php');
    require_once($_SESSION['root'].'departamentos/lib.php');
}else{
    header('Location: http://localhost/ProyectoIESWIRTZ/index.php');
}
function estarLogeado1(){
    if (isset($_COOKIE['PHPSESSID'])) {
        if($_COOKIE['PHPSESSID']===session_id()){
            return true;
        }else{
            session_start();
            return true;
        }
    } else {
        header('Location: http://localhost/ProyectoIESWIRTZ/index.php');
        return false;
    }
}