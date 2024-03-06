<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdOcupacion    = obterPost('IdOcupacion',true,false,false,false,false);
$IdCert         = obterPost('IdCert',true,false,false,false,false);

$infoOcupacion=obterOcupacion($IdOcupacion);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'resultado'     =>  !empty($infoOcupacion),
        'Ocupacion'     =>  $infoOcupacion,
        'IdCert'        =>  $IdCert
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