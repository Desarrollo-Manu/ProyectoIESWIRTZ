<?php
$root       =   realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
$relative   =   'http://localhost/ProyectoIESWIRTZ/';
$fileSystem =   'http://localhost/ProyectoIESWIRTZDATA/';
require_once $root.'config/frameworks/mustache/src/Mustache/Autoloader.php';
require_once $root.'login/lib.php';
Mustache_Autoloader::register();
$m = new Mustache_Engine;

if(estarLogeado()){
    session_start();
}

/*CARGAMOS A CABECEIRA*/
$header   = file_get_contents($root.'header/templates/header.mustache');

if(estarLogeado()){
    require_once($root.'config/bd/bd.php');
    /*LIBRERIAS DE CARGA PARA ENTIDADES, ETC...*/
    require_once($root.'config/fmaestras.php');
    /*PLANTILLAS HTML PARA INTERFACES*/
    //require_once($root.'config/interfaces.php');
}
echo $m->render( $header,Array(
    'root'              =>  $relative,
    /*'site_admin'        => (estarLogeado()) ?  site_admin($_SESSION['IdEntidad']) : false ,*/
    'logged'            => estarLogeado(),
));

