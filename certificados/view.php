<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$Id= obterGet('Id',true,false,false,false,false);

echo $m->render( file_get_contents("templates/view.mustache"),Array(
    'root'              =>  $relative,
    'IdCert'            =>  $Id,
    'cardDatos'         =>  $m->render( file_get_contents("templates/cardCertificado.mustache"),Array(
        'root'                  =>  $relative,
        'IdCert'                =>  $Id,
        'datosCertificado'      =>  obterCertificado($Id),
    )),
    'cardEstruc'            =>  $m->render( file_get_contents("templates/cardEstructCert.mustache"),Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id,
        'listMFsUFs'        =>  obterMFsUFsCert($Id),
    )),
    'modalAddMFs'           =>  $m->render( file_get_contents("templates/modalAddMF.mustache"),Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    )),
    'modalAddUFs'           =>  $m->render( file_get_contents("templates/modalAddUF.mustache"),Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    )),
   'cardOcupaciones'       =>  $m->render( file_get_contents("templates/cardOcupaciones.mustache"),Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id,
        'listOcupaCert'     =>  obterOcupacionesCert($Id)
    )),
    'modalAddOcupaciones' =>  $m->render( file_get_contents("templates/modalNewOcupacion.mustache"),Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    )),
    'modalModOcupaciones' =>  $m->render( file_get_contents("templates/modalModOcupacion.mustache"),Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    ))

));

/*PLANTILLAS HTML*/
include_once('templates/cardCertificado.html');
include_once('templates/modalNewOcupacion.html');
include_once('templates/modalModOcupacion.html');
include_once('templates/modalAddMF.html');
include_once('templates/modalAddUF.html');