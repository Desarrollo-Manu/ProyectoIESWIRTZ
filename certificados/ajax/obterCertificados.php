<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$listCertificados=obterCertificados();

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'resultado'         =>  !empty($listCertificados),
        'listCertificados'  =>  $listCertificados
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