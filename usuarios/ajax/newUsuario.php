<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$DNI            = obterPost('DNI',true,false,false,false,false);
$Nombre         = obterPost('Nombre',true,true,false,false,false);
$Apellidos      = obterPost('Apellidos',true,true,false,false,false);
$NSS            = obterPost('NSS',false,false,false,false,true);
$FecNacimiento  = obterPost('FecNacimiento',false,false,false,false,false);
$Sexo           = obterPost('Sexo',true,false,false,false,true);

if(validarDNI($DNI)){
    $Id=newUsuario($DNI,$Nombre,$Apellidos,$NSS,$FecNacimiento,$Sexo);
}else{
    $Id=null;
}


header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>  is_int($Id),
    'Id'            =>  $Id
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