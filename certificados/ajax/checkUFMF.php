<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdModulo   = obterPost('IdModulo',true,false,false,false,false);
$IdUnidad   = obterPost('IdUnidad',true,false,false,false,false);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'resultado'  =>  comprobarUFMF($IdModulo,$IdUnidad)
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