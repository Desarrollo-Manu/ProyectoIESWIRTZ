<?php
$root=realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
$relative='http://localhost/ProyectoIESWIRTZ/';


//session_start();
unset($_SESSION);
unset($_COOKIE);
session_destroy();
setcookie ("PHPSESSID", "", time() - 93600, '/');
//require_once($root.'header/header.php');
header('Location: http://localhost/ProyectoIESWIRTZ/index.php');