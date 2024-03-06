<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');

echo $m->render( file_get_contents("templates/index.mustache"),Array(
    'root'              =>  $relative,
    'misDatos'          =>  obterUsuario($_SESSION["Id"]),
    'listContactos'     =>  obterContactos($_SESSION["Id"]),
    'listFormaciones'   =>  obterFormacionUsuario($_SESSION["Id"])
));
echo $m->render( file_get_contents("../usuarios/templates/modalUsuario.mustache"),Array(
    'Id'    =>$_SESSION["Id"]
));


include_once('../usuarios/templates/modalUsuario.html');