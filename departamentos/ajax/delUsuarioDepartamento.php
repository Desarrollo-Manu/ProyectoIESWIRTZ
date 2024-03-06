<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$IdUsuario      = obterPost('IdUsuario',true,false,false,false,false);
$IdDepartamento = obterPost('IdDepartamento',true,false,false,false,false);
$IdDepUsu       = obterPost('IdDepUsu',true,false,false,false,false);

if(comprobarUsuario($IdUsuario,null)&&comprobarDepartamento($IdDepartamento)&&comprobarUsuarioDepartamento($IdDepartamento,$IdUsuario)){
    $resultado=delUsuarioDepartamento($IdDepUsu);
}else{
    $resultado=true;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'         =>   $resultado
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