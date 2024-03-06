<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$IdPlan     = obterPost('IdPlan',true,false,false,false,true);
$IdFormacion= obterPost('IdFormacion',true,false,false,false,true);
$Usuarios   = obterArray('Usuarios',true,false,false,true);
$IdRol      = obterPost('IdRol',true,false,false,false,true);

$resultado=true;

if(comprobarIdPlan($IdPlan)&&comprobarIdFormacion($IdFormacion)){
    foreach($Usuarios as $Usuario){
        if(comprobarUsuario($Usuario,null)&&!comprobarUsuarioFormacion($IdFormacion, $Usuario)){
            if(!saveUsuariosFormacion($IdFormacion, $Usuario, $IdRol)){
                $resultado=false;
            }
        }
    }
}else{
    $resultado=null;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'      =>   $resultado,
    'IdPlan'         =>   $IdPlan,
    'listAlumnos'    =>   obterFormacionUsuarios($IdFormacion,1),
    'listTutores'    =>   obterFormacionUsuarios($IdFormacion,2)
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