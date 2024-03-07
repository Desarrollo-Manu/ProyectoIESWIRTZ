<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCertificado',true,false,false,false,false);
$IdModulo       = obterPost('IdModulo',true,false,false,false,false);
$IdUF           = obterPost('IdUnidad',false,false,false,false,false);
$Codigo         = obterPost('Codigo',true,false,false,false,false);
$Nombre         = obterPost('Nombre',true,true,false,false,false);
$HorasTotales   = obterPost('HorasTotales',true,false,false,false,false);
$HorasFormacion = obterPost('HorasFormacion',true,false,false,false,false);
$HorasTutoria   = obterPost('HorasTutoria',true,false,false,false,false);
$HorasExamen    = obterPost('HorasExamen',true,false,false,false,false);
$Orden          = obterPost('Orden',true,false,false,false,false);

if(!comprobarUF($IdUF)&&comprobarMF($IdModulo)&&!comprobarUFMF($IdUF,$IdModulo)){
    $IdUF=newUF($Codigo,$Nombre,$HorasTotales,$HorasFormacion,$HorasTutoria,$HorasExamen);
    if(is_int($IdUF)){
        $resultado=is_int(saveUFMF($IdModulo,$IdUF,$Orden));
    }else{
        $resultado=false;
    }
}else if(comprobarUF($IdUF)&&comprobarMF($IdModulo)&&comprobarUFMF($IdModulo,$IdUF)){
    $resultado=updateUF($Codigo,$Nombre,intval($HorasTotales),intval($HorasTutoria),intval($HorasExamen),intval($HorasFormacion),intval($IdUF));
}else{
    $resultado  =   false;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>   $resultado,
    'IdUnidad'      =>   $IdUF,
    'IdCertificado' =>   $IdCertificado,
    'listMFsUFs'    =>   obterMFsUFsCert($IdCertificado),
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