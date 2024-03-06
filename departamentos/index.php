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

include_once('templates/modalAddUsuDep.html');
include_once('templates/cardSearch.html');