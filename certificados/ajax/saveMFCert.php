<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(1,$_SESSION["Id"]);
$IdCertificado  = intval(obterPost('IdCertificado',true,false,false,false,false));
$Codigo         = obterPost('Codigo',true,true,false,false,false);
$IdModulo       = intval(obterPost('IdModulo',true,true,false,false,false));
$Nombre         = obterPost('Nombre',true,true,false,false,false);
$Transversal    = intval(obterPost('Transversal',false,false,false,true,false));
$Nivel          = intval(obterPost('Nivel',true,false,false,false,false));
$HorasTotales   = intval(obterPost('HorasTotales',true,false,false,false,false));
$HorasFormacion = intval(obterPost('HorasFormacion',true,false,false,false,false));
$HorasTutoria   = intval(obterPost('HorasTutoria',true,false,false,false,false));
$HorasExamen    = intval(obterPost('HorasExamen',true,false,false,false,false));
$Orden          = intval(obterPost('Orden',true,false,false,false,false));
$IdUC           = obterPost('UC',false,false,true,false,false);
$resultado      = false;$IdCertMF=null;

//var_dump($Transversal);

if(!comprobarMF($IdModulo)&&comprobarCert($IdCertificado)&&!comprobarMFCert($IdCertificado,$IdModulo)) {
    $IdModulo = newMF($Codigo,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,$Transversal);
    if(is_int($IdModulo)){
        if(asignarMFCert($IdCertificado,$IdModulo,$Orden)){
            $resultado = asignarUCMF($IdCertificado,$IdUC);
        }
    }
}else if(comprobarMF($IdModulo)&&comprobarCert($IdCertificado)&&comprobarMFCert($IdCertificado,$IdModulo)){
    if(actualizarMF($Codigo,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,$Transversal,$IdModulo)){
        $IdCertMF=obterRelCertMF($IdCertificado,$IdModulo);
        $resultado=actualizarMFCert($IdCertMF,$Orden);
    }
    if(!is_int($IdUC)){
        $IdUC=obterIdUC(explode(" - ", $IdUC)[0]);
        if(!comprobarUCMF($IdModulo,$IdUC)){
            $resultado=asignarUCMF($IdModulo,$IdUC);
        }
    }
    $resultado=true;
}else{
    $resultado=false;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>   $resultado,
    'IdModulo'      =>   $IdModulo,
    'IdCertificado' =>   $IdCertificado,
    '$IdCertMF'     =>  obterRelCertMF($IdCertificado,$IdModulo),
    '$IdRel'        =>  actualizarMFCert($IdCertMF,$Orden),
    '$Transversal'  =>  $Transversal,
    'listMFsUFs'    =>  obterMFsUFsCert($IdCertificado),
    'actualizacion' =>  actualizarMF($Codigo,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,'b'.$Transversal,$IdModulo),
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