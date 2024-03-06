<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$IdFormacion        = obterPost('IdFormacion',true,false,false,false,true);
$IdUsuarioFormacion = obterPost('IdUsuarioFormacion',true,false,false,false,true);
$IdRol	            = obterPost('IdRol',true,false,false,false,true);

if(comprobarIdUsuForm($IdUsuarioFormacion)&&comprobarIdFormacion($IdFormacion)){
    $resultado  =delUsuariosIdFormacion($IdUsuarioFormacion);
}else{
    $resultado=null;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'         =>   $resultado,
    'listAlumnos'       =>  obterFormacionUsuarios($IdFormacion,1),
    'listTutores'       =>  obterFormacionUsuarios($IdFormacion,2)
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