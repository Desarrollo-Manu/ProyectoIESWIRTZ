<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$IdPlan     = obterPost('IdPlan',true,false,false,false,true);
$Modulos    = obterArray('Modulos',true,false,false,true);
$Usuarios   = obterArray('Usuarios',true,false,false,true);
$IdRol      = obterPost('IdRol',true,false,false,false,true);

if(comprobarIdPlan($IdPlan)){
    $resultado=true;
    foreach($Modulos as  $Modulo){
        if(comprobarMF($Modulo)){
            $IdFormacion=saveModuloFormacion($IdPlan,$Modulo);
            if(is_int($IdFormacion)){
                foreach($Usuarios as $Usuario){
                    if(comprobarUsuario($Usuario,null)){
                        if(!saveUsuariosFormacion($IdFormacion, $Usuario, $IdRol)){
                            $resultado=false;
                        }
                    }
                }
            }
        }
    }
}else{
    $resultado=null;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'      =>   $resultado,
    'Id'             =>   $IdPlan,
    'listformacion'=>   obterformacionPlan($IdPlan)
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
