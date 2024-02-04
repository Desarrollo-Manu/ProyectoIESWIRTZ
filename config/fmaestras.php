<?php
if(estarLogeado1()){
    date_default_timezone_set('Europe/Madrid');
    require_once($_SESSION['root'].'config/getVariables.php');
    require_once($_SESSION['root'].'grupos/lib.php');
    //require_once($_SESSION['root'].'config/funciones/contactos/lib.php');
    //require_once($_SESSION['root'].'config/funciones/direcciones/lib.php');
    require_once($_SESSION['root'].'config/patrones.php');
   // include_once($_SESSION['root'].'header/frameworks/Carbon/autoload.php');
   // require_once($_SESSION['root'].'config/imgs/lib.php');
    require_once($_SESSION['root'].'certificados/lib.php');
    require_once($_SESSION['root'].'config/bd/bd.php');
    //include_once($_SESSION['root'].'config/funciones/direcciones/templates/tablaDirecciones.html');
    //include_once($_SESSION['root'].'config/funciones/contactos/templates/tablaContactos.html');
}
function estarLogeado1(){
    if (isset($_COOKIE['PHPSESSID'])) {
        if($_COOKIE['PHPSESSID']===session_id()){
            return true;
        }else{
            session_start();
            if($_COOKIE['PHPSESSID']===session_id()){
                return true;
            }else{
                return false;
            }
        }
    } else {
        //header('Location: http://192.168.2.200/aixa/amarina.php');
        return false;
    }
}