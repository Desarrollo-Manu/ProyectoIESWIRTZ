<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$tiposFormacion=obterTiposFormacion(null);
echo $m->render( file_get_contents("templates/index.mustache"),Array(
    'root'                  =>  $relative,
    'cardBuscador'          =>  $m->render( file_get_contents("templates/cardBuscador.mustache"),Array(
        'listTiposFormacions'   =>  $tiposFormacion,
        'listPlanes'      =>  obterPlanes(null)
    )),
    'cardBuscarAlumnos'=> $m->render( file_get_contents("templates/cardBuscarAlumnos.mustache"),Array(
        'root'              =>  $relative,
        'listPlanes'        =>  obterPlanes(null),
        'listCertificados'  =>  obterCertificados(),
        'listRoles'         =>  obterRoles(null)
    )),
));

echo $m->render( file_get_contents("templates/modalNewPlanEstudios.mustache"),Array(
    'listTiposFormacions'   => $tiposFormacion
));

include_once('templates/cardBuscarAlumnos.html');
include_once('templates/cardBuscador.html');
include_once('templates/modalNewPlanEstudios.html');
