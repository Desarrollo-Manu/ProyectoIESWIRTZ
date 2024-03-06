<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);

$IdCertificado  = obterPost('IdCert',true,false,false,false,false);
$IdModulo       = obterPost('IdModulo',true,false,true,false,false);
$Orden          = obterPost('Orden',true,false,false,false,false);

if(comprobarMF($IdModulo)&&(comprobarCert($IdCertificado)&&(!comprobarMFCert($IdCertificado,$IdModulo)))){
    $resultado = is_int(saveCertAddMFCert($IdCertificado,$IdModulo,$Orden));
}else{
    $resultado  =   true;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>      $resultado,
    'IdModulo'      =>      $IdModulo,
    'IdCert'        =>      $IdCertificado,
    'listMFsUFs'    =>      obterMFsUFsCert($IdCertificado)
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