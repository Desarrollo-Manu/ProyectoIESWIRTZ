<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$IdUSuario  = obterPost('IdUsuario',true,false,false,false,true);
$Tipo       = obterPost('Tipo',true,false,false,false,true);
$Valor      = obterPost('Valor',true,false,false,false,false);
$Id         = obterPost('Id',false,false,false,false,true);

if(comprobarUsuario($IdUSuario,null)&&!comprobarContacto($IdUSuario,$Tipo,$Valor)){
    $Id=saveContacto($IdUSuario,$Tipo,$Valor);
}else{
    $Id=null;
}


header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>  is_int($Id),
    'Id'            =>  $Id,
    'listContactos'=>  obterContactos($IdUSuario)
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