<?php
$root=realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
$relative   =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/';

//session_start();
unset($_SESSION);
unset($_COOKIE);
session_destroy();
setcookie ("PHPSESSID", "", time() - 93600, '/');
$mainPage  =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/index.php';
header('Location: '.$mainPage);