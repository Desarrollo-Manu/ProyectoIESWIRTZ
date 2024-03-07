<?php
$root       =   realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
$relative   =   $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER ["SERVER_NAME"].'/ProyectoIESWIRTZ/';
require_once $root.'login/lib.php';
require_once $root.'config/fmaestras.php';

require_once $root.'header/header.php';

