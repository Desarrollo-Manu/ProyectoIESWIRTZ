<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/ProyectoIESWIRTZ/config/fmaestras.php');

$IdFormacion     = obterPost('IdFormacion', true, false, false, false, true);

if (comprobarIdFormacion($IdFormacion)) {
    $listAlumnos = obterFormacionUsuarios($IdFormacion,1);
    $listTutores = obterFormacionUsuarios($IdFormacion,2);
} else {
    $listAlumnos = null;$listTutores=null;
}
header('Content-Type: application/json');
$json = json_encode(array(
    'resultado'     =>  !empty($listAlumnos) ,
    'listAlumnos'   =>  $listAlumnos,
    'listTutores'   =>  $listTutores,
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