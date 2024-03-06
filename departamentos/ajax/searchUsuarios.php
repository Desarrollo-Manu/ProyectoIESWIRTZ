<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$Nombre         = obterPost('Nombre',false,false,false,false,false);
$Apellidos      = obterPost('Apellidos',false,false,false,false,false);
$DNI            = obterPost('DNI',false,false,false,false,false);
$IdDepartamento = obterPost('IdDepartamento',false,false,false,false,false);
$Activo         = obterPost('Activo',false,false,false,true,false);

$listUsuarios=searchUsuarios($Nombre,$Apellidos,$DNI,$IdDepartamento,$Activo);

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>      !empty($listUsuarios),
    'listUsuarios'  =>      $listUsuarios
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