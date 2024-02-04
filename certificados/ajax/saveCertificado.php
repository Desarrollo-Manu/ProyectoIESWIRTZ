<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');

$IdCert     = obterPost('IdCert',false,false,false,false,false);
$IdFamilia  = obterPost('IdFamilia',true,false,false,false,false);
$IdArea     = obterPost('IdArea',true,false,false,false,false);
$Codigo     = obterPost('Codigo',true,true,false,false,false);
$Nombre     = obterPost('Nombre',true,true,false,false,false);
$Nivel      = obterPost('Nivel',true,false,false,false,false);
$Horas      = obterPost('Horas',true,false,false,false,false);
$CualifProfRef= obterPost('CualifProfRef',true,true,false,false,false);
$CompGeneral= obterPost('CompGeneral',true,false,false,false,false);


/*INSERTAR UN CERTIFICADO POR PRIMERA VEZ*/
if(!comprobarCert(null,$Codigo)&&comprobarFamilia($IdFamilia)&&comprobarAreaFam($IdArea,$IdFamilia)){
    $Id=saveCertificado($IdArea,$Codigo,$Nombre,$Nivel,$CompGeneral,$CualifProfRef,$Horas);
    $resultado=is_int($Id);
}else if(comprobarCert($IdCert,null)){/*ACTUALIZARLO*/
    $resultado=actualizarCertificado($IdCert,$IdArea,$Codigo,$Nombre,$Nivel,$CompGeneral,$CualifProfRef,$Horas);
    $Id=null;
}else{
    $resultado=true;$Id=null;
}

header('Content-Type: application/json');
$json = json_encode(Array(
    'resultado'     =>   $resultado,
    'Id'            =>   $Id
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