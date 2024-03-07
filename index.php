<?php
$root=realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
require_once $root.'config/frameworks/mustache/src/Mustache/Autoloader.php';
require_once $root.'login/lib.php';
Mustache_Autoloader::register();
$m = new Mustache_Engine;

$logIncorrecto=false;
error_reporting(E_ALL); //activar los errores (en modo depuración)
if (isset($_POST['user'])&&isset($_POST['pass'])&&(!isset($_COOKIE['PHPSESSID']))){
    include_once($root.'config/bd/bdMariaBD.php');
    $Id=comprobarUserPass(strval($_POST['user']),strval(md5($_POST['pass'])));
    if(is_int($Id)){
        session_start();
        $_COOKIE['PHPSESSID']       =   session_id();
        $_SESSION['name']           =   $_POST['user'];
        $_SESSION['Id']             =   $Id;
        $_SESSION['Sesion']         =   session_id();
        $_SESSION['SameSite']       =   'Lax';
        $_SESSION['root']           =   $root;
        $_SESSION['url']            =   $relative   =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/';
        $mainPage  =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/index.php';
        header('Location: '.$mainPage);
    }else{
        $logIncorrecto=true;
    }
}else if(isset($_COOKIE['PHPSESSID'])) {
    if($_COOKIE['PHPSESSID']!==session_id()){
        session_start();
    }
}

require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/header/header.php');
if(estarLogeado()){
    include_once($root.'config/bd/bdMariaBD.php');
    echo $m->render( file_get_contents($_SESSION['root']."login/templates/login.mustache") ,Array(
        'root'          =>  $relative   =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/',
        'listDepartamentos' =>obterDepartamentosUsuario($_SESSION['Id'])
    ));
}else{
    echo $m->render( file_get_contents($root.'login/templates/noLogin.mustache') ,Array(
        'root'          =>  $relative   =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/',
        'errorLog'          =>  $logIncorrecto,
        'accessLog'         =>  isset($_POST['access'])
    ));
}
