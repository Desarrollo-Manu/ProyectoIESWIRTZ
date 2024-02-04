<?php
$root=realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/';
require_once $root.'config/frameworks/mustache/src/Mustache/Autoloader.php';
require_once $root.'login/lib.php';
Mustache_Autoloader::register();
$m = new Mustache_Engine;

$logIncorrecto=false;
error_reporting(E_ALL); //activar los errores (en modo depuración)

if (isset($_POST['user'])&&isset($_POST['pass'])&&(!isset($_COOKIE['PHPSESSID']))){
    include_once($root.'config/bd/bd.php');
    if(comprobarUserPass($_POST['user'],md5($_POST['pass']))){
        session_start();
        $_COOKIE['PHPSESSID']       =   session_id();
        $_SESSION['name']           =   $_POST['user'];
        $_SESSION['SameSite']       =   'Lax';
        $_SESSION['files']          =   'http://localhost/ProyectoIESWIRTZDATA';
        $_SESSION['root']           =   $root;
        $_SESSION['url']            =   'http://localhost/ProyectoIESWIRTZ/';
        $_SESSION['Desarrollo']     =   false;
        header('Location: http://localhost/ProyectoIESWIRTZ/index.php');
    }else{
        //$msg = "Invalid user / password";
        ///echo $msg;
        $logIncorrecto=true;
    }


}
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/ProyectoIESWIRTZ/config/config.php');

//PLANTILLAS MUSTACHE*/
//$footer   = file_get_contents($root."header/templates/footer.mustache");
if(estarLogeado()){
    require_once($_SESSION['root'].'config/fmaestras.php');
    $login=file_get_contents($_SESSION['root']."login/templates/login.mustache");
}else{
    $login=file_get_contents($root.'login/templates/noLogin.mustache');
}
echo $m->render( $login ,Array(
    'root'      =>  'http://localhost/ProyectoIESWIRTZ/',
    'errorLog'  =>  $logIncorrecto,
));