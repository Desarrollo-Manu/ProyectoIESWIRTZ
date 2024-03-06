<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCertificado',true,false,false,false,false);
$IdModulo       = obterPost('IdModulo',true,false,false,false,false);

if(comprobarMF($IdModulo)&&comprobarCert($IdCertificado)&&comprobarMFCert($IdCertificado,$IdModulo)){
    $resultado =   delCertMFCert(obterRelCertMF($IdCertificado,$IdModulo));
}else{
    $resultado =   false;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>  $resultado,
    'IdModulo'      =>  $IdModulo,
    'IdCertificado' =>  $IdCertificado,
    'listMFsUFs'    =>  obterMFsUFsCert($IdCertificado)
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