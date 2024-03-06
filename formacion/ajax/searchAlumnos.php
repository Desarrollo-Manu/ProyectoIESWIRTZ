<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/fmaestras.php');
comprobarPermisoAcceso(3,$_SESSION["Id"]);
$IdCertificado  = obterPost('IdCertificado',false,false,true,false,true);
$Nivel          = obterPost('Nivel',false,false,true,false,true);
$IdPlan         = obterPost('IdPlan',false,false,true,false,true);
$IdRol          = obterPost('IdRol',false,false,true,false,true);

header('Content-Type: application/json');
$json = json_encode(
    Array(
        'listAlumnos'  =>  searchAlumnos($IdCertificado,$IdPlan,$Nivel,$IdRol)
    )
);
if ($json === false){
    $json = json_encode(["jsonError" => json_last_error_msg()]);
    if ($json === false) {
        $json = '{"jsonError":"unknown"}';
    }
    http_response_code(500);
}
echo $json;
exit();