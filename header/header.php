<?php
$root       =   realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
$relative   =   'http://localhost/ProyectoIESWIRTZ/';

require_once $root.'config/frameworks/mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
$m = new Mustache_Engine;

echo $m->render( file_get_contents($root.'header/templates/header.mustache'),Array(
    'root'              => $relative,
    'logged'            => estarLogeado(),
));
