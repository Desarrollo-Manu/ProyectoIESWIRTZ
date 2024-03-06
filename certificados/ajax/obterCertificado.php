<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$Id= obterPost('Id',true,false,false,false,false);

if(comprobarCert($Id)){
    $infoCertificado    =   obterCertificado($Id);
    $resultado          =   !empty($infoCertificado);
}else{
    $resultado=false;
    $infoCertificado=null;
}
header('Content-Type: application/json');
$json = json_encode(
    Array(
        'certificado'   =>  obterCertificado($Id),
        'resultado'     =>  $resultado
    )
);
if ($json === false){
    $json = json_encode(["jsonError" => json_last_error_msg()]);
    if ($json === false) {
        $json = '{"jsonError":"unknown"}';
    }
    http_response_code(500);
}
echo $json;
exit();