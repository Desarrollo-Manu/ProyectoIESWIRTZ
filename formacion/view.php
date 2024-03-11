<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);

$Id     = obterGet('id',true,false,false,false,false);

$InfoPlan         = obterPlan($Id);

echo $m->render( file_get_contents("templates/view.mustache"),Array(
    'root'          =>  $relative,
    'Id'            =>  $Id,
    'cardDatosPlan' => $m->render( file_get_contents("templates/cardResumenPlan.mustache"),Array(
        'Id'                =>  $Id,
        'Plan'              =>  $InfoPlan,
    )),
    'cardformacion'=> $m->render( file_get_contents("templates/cardPlanEst.mustache"),Array(
        'root'              =>  $relative,
        'IdPlan'            =>  $Id,
        'listformacion'   =>  obterformacionPlan($Id),
    )),

));

echo $m->render( file_get_contents("templates/modalAddAl.mustache"),Array(
    'Id'  =>  $Id,
));
echo $m->render( file_get_contents("templates/modalAddformaciones.mustache"),Array(
    'Plan'  =>  $InfoPlan,
));
echo $m->render( file_get_contents("../usuarios/templates/modalNewUsuario.mustache"),Array(
    'Plan'  =>  $InfoPlan,
));
echo $m->render( file_get_contents("../usuarios/templates/modalUsuario.mustache"),Array(
    'Plan'  =>  $InfoPlan,
));

include_once('templates/cardPlanEstTem.html');
include_once('templates/modalAddformaciones.html');
include_once('templates/modalAddAl.html');
include_once('../usuarios/templates/modalNewUsuario.html');
include_once('../usuarios/templates/modalUsuario.html');
