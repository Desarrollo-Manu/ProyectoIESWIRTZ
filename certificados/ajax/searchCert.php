<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdFamilia      = obterPost('Familia',false,false,true,false,false);
$Nivel          = obterPost('Nivel',false,false,true,false,false);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'listCertificados'  =>  searchCerts($IdFamilia,$Nivel)
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