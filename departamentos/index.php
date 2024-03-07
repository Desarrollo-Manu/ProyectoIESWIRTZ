<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');

echo $m->render( file_get_contents("templates/index.mustache"),Array(
    'root'          =>  $relative,
    'FormUsuarios'  =>  $m->render( file_get_contents("templates/cardSearch.mustache"),Array(
        'listDepartamentos' =>  obterDepartamentos()
    )),
));
echo $m->render( file_get_contents("templates/modalAddUsuDep.mustache"),Array(
    'listDepartamentos' =>  obterDepartamentos()
));
echo $m->render( file_get_contents("templates/modalAddUsuDep.mustache"),Array(
    'listDepartamentos' =>  obterDepartamentos()
));
echo $m->render( file_get_contents("../usuarios/templates/modalNewUsuario.mustache"),Array(

));
include_once('../usuarios/templates/modalNewUsuario.html');
include_once('templates/cardSearch.html');
include_once('templates/modalAddUsuDep.html');