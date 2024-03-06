<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdModulo   = obterPost('IdModulo',true,false,false,false,false);
$IdUnidad   = obterPost('IdUnidad',true,false,false,false,false);


$unidadFormativa=obterUFdelMF($IdModulo,$IdUnidad);
header('Content-Type: application/json');
$json = json_encode(
    Array(
        'resultado' =>  !empty($unidadFormativa),
        'Unidad'    =>  $unidadFormativa
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