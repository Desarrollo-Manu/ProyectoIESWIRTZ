<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$IdCert        = obterPost('IdCert',true,false,false,false,false);
$Codigo        = obterPost('Codigo',true,true,false,false,false);
$IdCert        = obterPost('IdCert',true,false,false,false,false);
$IdTipo        = obterPost('Tipo',true,false,false,false,false);
$FecIni        = obterPost('FecIni',true,false,false,false,false);
$FecFin        = obterPost('FecFin',true,false,false,false,false);

if(comprobarCert($IdCert)&&!comprobarCodigoFormacion($Codigo)&&comprobarTipoFormacion($IdTipo)){
    $IdPlan = newPlan($Codigo,$IdCert,$IdTipo,$FecIni,$FecFin);
    $resultado=is_int($IdPlan);
}else if(comprobarCert($IdCert)&&comprobarIdFormacion($Codigo)&&comprobarTipoFormacion($IdTipo)){
    $resultado = actualizarPlan($Codigo,$IdTipo,$FecIni,$FecFin);
}else{
    $resultado=null;
}
header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>   $resultado,
    'id'            =>   $IdPlan
));
if ($json === false) {
    // Avoid echo of empty string (which is invalid JSON), and
    // JSONify the error message instead:
    $json = json_encode(["jsonError" => json_last_error_msg()]);
    if ($json === false) {
        // This should not happen, but we go all the way now:
        $json = '{"jsonError":"unknown"}';
    }
    // Set HTTP response status code to: 500 - Internal Server Error
    http_response_code(500);
}
echo $json;
exit();