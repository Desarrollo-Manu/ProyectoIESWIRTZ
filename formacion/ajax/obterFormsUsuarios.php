<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$Id         = obterPost('Id',true,false,false,false,false);
$IdCert     = obterPost('IdCert',true,false,false,false,false);
if(comprobarIdPlan($Id)){
    $listModulos    =obterMFsCert($IdCert);
    $listUsuarios   =obterUsuarios();
    $listRoles      =obterRoles(null);
}else{
    $resultado=null;
}
header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>   $listModulos && $listUsuarios && $listRoles,
    'listModulos'   =>   $listModulos,
    'listUsuarios'  =>   $listUsuarios,
    'listRoles'     =>   $listRoles,
    'Id'            =>   $Id
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