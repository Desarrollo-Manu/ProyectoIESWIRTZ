<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCertificado',true,false,false,false,false);
$IdUnidad       = obterPost('IdUnidad',true,false,false,false,false);
$IdModulo       = obterPost('IdModulo',true,false,false,false,false);

if(comprobarUF($IdUnidad)&&comprobarMF($IdModulo)&&comprobarUFMF($IdModulo,$IdUnidad)){
    $resultado =   delCerUFMF(obterRelMFUF($IdModulo,$IdUnidad));
}else{
    $resultado =   null;
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