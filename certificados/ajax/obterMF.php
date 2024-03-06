<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdModulo       = obterPost('IdModulo',true,false,false,false,false);
$IdCertificado  = obterPost('IdCertificado',true,false,false,false,false);

$Modulo=obterMF($IdCertificado,$IdModulo);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'IdCertificado'     =>  $IdCertificado,
        'Modulo'            =>  $Modulo,
        'resultado'         =>  !empty($Modulo)
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