<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$Id= obterGet('Id',true,false,false,false,false);

//CARGAMOS AS PLANTILLAS MUSTACHE PARA OS DATOS
$cardCertificados   = file_get_contents("templates/cardCertificado.mustache");
$cardEstrucCert     = file_get_contents("templates/cardEstructCert.mustache");
$cardOcupaciones    = file_get_contents("templates/cardOcupaciones.mustache");
$modalAddOcupaciones= file_get_contents("templates/modalNewOcupacion.mustache");
$modalModOcupaciones= file_get_contents("templates/modalModOcupacion.mustache");
$view               = file_get_contents("templates/view.mustache");
$modalAddMFs        = file_get_contents("templates/modalAddMF.mustache");
$modalAddUFs        = file_get_contents("templates/modalAddUF.mustache");

//var_dump(obterMFsUFsCert(intval($Id)));
echo $m->render( $view,Array(
    'root'              =>  $relative,
    'IdCert'            =>  $Id,
    'cardDatos'         =>  $m->render( $cardCertificados,Array(
        'root'                  =>  $relative,
        'IdCert'                =>  $Id,
        'datosCertificado'      =>  obterCertificado($Id),
    )),
    'cardEstruc'            =>  $m->render( $cardEstrucCert,Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id,
        'listMFsUFs'        =>  obterMFsUFsCert($Id),
    )),
    'modalAddMFs'           =>  $m->render( $modalAddMFs,Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    )),
    'modalAddUFs'           =>  $m->render( $modalAddUFs,Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    )),
   'cardOcupaciones'       =>  $m->render( $cardOcupaciones,Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id,
        'listOcupaCert'     =>  obterOcupacionesCert($Id)
    )),
    'modalAddOcupaciones' =>  $m->render( $modalAddOcupaciones,Array(
        'root'              =>  $relative,
        'IdCert'            =>  $Id
    )),
    'modalModOcupaciones' =>  $m->render( $modalModOcupaciones,Array(
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