<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdModulo   = obterPost('IdModulo',true,false,false,false,false);

if(comprobarMF($IdModulo)){
    $listUFs=obterUFsNoMF(intval($IdModulo));
}else{
    $listUFs=null;
}

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'listUFs'  =>  $listUFs,
        'resultado'=>   !empty($listUFs)
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