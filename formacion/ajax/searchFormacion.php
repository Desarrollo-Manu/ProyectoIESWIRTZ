<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$IdFormacion   = obterPost('IdFormacion',false,false,true,false,true);
$Tipo        = obterPost('Tipo',false,false,true,false,true);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'listPlanes'  =>  searchFormacion($IdFormacion,$Tipo)
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