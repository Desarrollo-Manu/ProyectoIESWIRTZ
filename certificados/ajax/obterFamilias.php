<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$listFamilias=obterFamilias(null);
header('Content-Type: application/json');
$json = json_encode(
    Array(
        'resultado'     =>empty($listFamilias),
        'listFamilias'  =>  obterFamilias(null)
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