<?php
function generarPasswordAleatorio(){
    define('gs_PassVocalMayus', "AEIU");
    define('gs_PassVocalMinus', "aeiou");
    define('gs_PassConsoMayus', "BCDFGHJKLMNPQRSTVWXYZ");
    define('gs_PassConsoMinus', "bcdfghjkmnpqrstvwxyz");
    $Clave         = null;
    $CadenaBuscar  = null;
    $GrupoNumeros  = null;
    $GrupoLetras   = null;
    $TieneMayus    = False;
    $TieneMinus    = False;

    $PosPunto = rand(0,1);

    $GrupoNumeros = strtotime("now");

    $CadenaBuscar = gs_PassVocalMayus . gs_PassConsoMayus . gs_PassVocalMinus . gs_PassConsoMinus;
    for($i = 1; $i<=2;$i++) {
        usleep(mt_rand(100, 1000));
        $Caracter = substr($CadenaBuscar, intval( rand(0,strlen($CadenaBuscar)-1)), 1);
        $Clave = $Clave . $Caracter;
        $GrupoLetras = $GrupoLetras . $Caracter;
        if (strpos(strval(gs_PassVocalMayus) . strval(gs_PassConsoMayus), strval($Caracter)) > 0) {
            $TieneMayus = True;
        } else {
            $TieneMinus = True;
        }
        if (strpos(strval(gs_PassVocalMinus) . strval(gs_PassVocalMayus), strval($Caracter)) > 0) {
            $CadenaBuscar = gs_PassConsoMayus . gs_PassConsoMinus;
            if ($i == 2) {
                if (!$TieneMayus) {
                    $CadenaBuscar = gs_PassConsoMayus;
                }
                if (!$TieneMinus) {
                    $CadenaBuscar = gs_PassConsoMinus;
                }
            }
        } else {
            $CadenaBuscar = gs_PassVocalMayus . gs_PassVocalMinus;
            if ($i == 2) {
                if (!$TieneMayus) {
                    $CadenaBuscar = gs_PassVocalMayus;
                }
                if (!$TieneMinus) {
                    $CadenaBuscar = gs_PassVocalMinus;
                }
            }
        }
        usleep(mt_rand(100, 1000));
        $Caracter = substr($CadenaBuscar, intval( rand(0,strlen($CadenaBuscar)-1)), 1);
        $Clave = $Clave . $Caracter;
        $GrupoLetras = $GrupoLetras . $Caracter;
        if (strpos(strval(gs_PassVocalMayus) . strval(gs_PassConsoMayus), strval($Caracter)) > 0) {
            $TieneMayus = True;
        } else {
            $TieneMinus = True;
        }
    }
    if($PosPunto == 1){
        $Clave = substr($GrupoNumeros,0,3) . "." . $GrupoLetras;
    }else{
        $Clave = $GrupoLetras . "." . substr($GrupoNumeros,0,3);
    }

    return $Clave;
}
/*FUNCIÓN QUE ESTANDARIZA UN STRING MONEDA CON COMAS OU PUNTOS EN UN FORMATO VALIDO PARA SQLSERVER*/
function cambiarFormatoMoneda($valor){
    if($valor!=''){
        $patrones = '/,/';
        return number_format(preg_replace($patrones, '.', $valor), 2,".","");
    }else{
        return null;
    }
}
function cambiarMinusToMayusTexto($Texto){
    $Texto=trim(strtoupper($Texto));
    $vocales = array("Á", "É", "Í", "Ó", "Ú","Ñ");
    $vocalesAcento = array("á", "é", "í", "ó", "ú","ñ");
    return str_replace($vocalesAcento, $vocales, $Texto);
}
function cambiarMayusToMinusTexto($Texto){
    $Texto=trim(strtolower($Texto));
    $vocales = array("Á", "É", "Í", "Ó", "Ú","Ñ");
    $vocalesAcento = array("á", "é", "í", "ó", "ú","ñ");
    return str_replace($vocales, $vocalesAcento, $Texto);
}
function color_rand() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFF0));
}
function validarDNI($dni){
    $letra = substr($dni, -1);
    $numeros = substr($dni, 0, -1);
    if(is_numeric($numeros)){
        return (substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1) == $letra && strlen($letra) == 1 && strlen ($numeros) == 8 );
    }else{
        return false;
    }

}