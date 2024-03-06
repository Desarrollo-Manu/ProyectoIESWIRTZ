<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCertificado',true,false,false,false,false);
$IdUnidad       = obterPost('IdUnidad',true,false,true,false,false);
$IdModulo       = obterPost('IdModulo',true,false,false,false,false);
$Orden          = obterPost('Orden',true,false,false,false,false);

if(comprobarMF($IdModulo)&&(comprobarUF($IdUnidad))&&(!comprobarUFMF($IdModulo,$IdUnidad))){
    $resultado  =   saveCertUFMF($IdModulo,$IdUnidad,$Orden);
}else{
    $resultado  =   false;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>   $resultado,
    'IdUnidad'      =>   $IdUnidad,
    'IdCertificado' =>   $IdCertificado,
    'listMFsUFs'    =>   obterMFsUFsCert($IdCertificado),
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