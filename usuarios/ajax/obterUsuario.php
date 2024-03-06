<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$Id     = obterPost('Id',true,false,false,false,true);

if(comprobarUsuario($Id,null)){
    $usuario=obterUsuario($Id);
    $listContactos=obterContactos($Id);
}else{
    $usuario=null;
}


header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>      !empty($usuario),
    'usuario'       =>      $usuario,
    'listContactos' =>      $listContactos,
    'Id'            =>      $Id
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