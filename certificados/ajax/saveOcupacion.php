<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$Id                     = intval(obterPost('Id',true,false,false,false,false));
$Codigo                 = obterPost('Codigo',true,false,false,false,false);
$Denominacion           = obterPost('Denominacion',true,true,false,false,false);
$IdCertificado          = obterPost('IdCert',true,false,false,false,false);

if(comprobarIdOcupacion($Id)){
    $resultado=updateOcupacion($Id,$Codigo,$Denominacion);
}else{
    $resultado=false;
}
header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>    $resultado,
    'listOcupaCert' =>   obterOcupacionesCert($IdCertificado)
));
if ($json === false) {
    // Avoid echo of empty string (which is invalid JSON), and
    // JSONify the error message instead:
    $json = json_encode(["jsonError" => json_last_error_msg()]);
    if ($json === false) {
        // This should not happen, but we go all the way now:
        $json = '{"jsonError":"unknown"}';
    }
    // Set HTTP response status code to: 500 - Internal Server Error
    http_response_code(500);
}
echo $json;
exit();