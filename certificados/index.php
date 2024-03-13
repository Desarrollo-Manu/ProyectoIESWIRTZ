<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$cardSearchCerts    = file_get_contents("templates/cardBuscador.mustache");
$index              = file_get_contents("templates/index.mustache");
$listFamilias       = obterFamilias(null);
echo $m->render( $index,Array(
    'root'              =>  $relative,
    'cardSearchCerts'   =>  $m->render( $cardSearchCerts,Array(
        'listFamilias'  =>  $listFamilias,
    )),
));

$newCertificado         = file_get_contents("templates/modalNewCert.mustache");
echo $m->render( $newCertificado,Array());

include_once('templates/cardCertificado.html');
include_once('templates/cardBuscador.html');
include_once('templates/modalNewCert.html');
