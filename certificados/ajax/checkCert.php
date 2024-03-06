<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/aixa/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCertificado',true,false,false,false,false);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'resultado'  =>  comprobarCert($IdCertificado)
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