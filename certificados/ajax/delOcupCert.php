<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCert',true,false,false,false,false);
$IdOcupacion    = obterPost('IdOCupa',true,false,false,false,false);
$Id             = obterPost('Id',true,false,false,false,false);

if(comprobarIdOcupacion($IdOcupacion)&&(comprobarCert($IdCertificado))&&(comprobarOcupaCert($IdCertificado,$IdOcupacion))){
    $resultado =   delCertOcupacion($Id);
}else{
    $resultado =   false;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'listOcupaCert'  =>  obterOcupacionesCert($IdCertificado),
    'resultado'      => $resultado
));
if ($json === false){
    $json = json_encode(["jsonError" => json_last_error_msg()]);
    if ($json === false) {
        $json = '{"jsonError":"unknown"}';
    }
    http_response_code(500);
}
echo $json;
exit();