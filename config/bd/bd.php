<?php
function conectarDBSQLB(){
    $serverName = "DESKTOP-7LOS6MQ\IESFERNANDOWIRTZ";
    $connectionInfo = array("Database"=>"BDIESWIRTZ","CharacterSet" => "UTF-8", "UID"=>"sa", "PWD"=>"05121985");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), true));
    }
    return $conn;
}
function cerrarDBSQLB($conn){
    sqlsrv_close($conn);
}
function ejecutarConsultaLecturaB($c,$s,$d){
    $resultado = sqlsrv_query($c,$s,$d);
    if( $resultado === false ) {
        die( print_r( sqlsrv_errors(), true));
    }else{
        return $resultado;
    }
}
function ejecutarConsultaB($c,$s,$d){
    $resultado = sqlsrv_query($c,$s,$d);
    if( $resultado === false ) {
        die( print_r( sqlsrv_errors(), true));
    }else{
        sqlsrv_next_result($resultado);
        sqlsrv_fetch($resultado);
        return sqlsrv_get_field($resultado, 0);
    }
}
/*SENTENCIA ACTUALIZACIÓN DE DATOS*/
function DBSQLUpdateB($sentencia,$datos){
    $con   =    conectarDBSQL();
    if ( sqlsrv_begin_transaction( $con ) === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }
    $resultado  =   ejecutarConsulta($con,$sentencia,$datos);
    if(!$resultado) {
        sqlsrv_commit( $con );
        cerrarDBSQL($con);
        return false;
    }else{
        sqlsrv_rollback( $con );
        cerrarDBSQL($con);
        return true;
    }
}
/*SENTENCIA INSERCIÓN DE DATOS. DevolverId debera usaer para tablas autoincrementales que desexemos que devolva o ID*/
function DBSQLInsertB($sentencia,$datos,$DevolverId){
    $con   =    conectarDBSQL();
    if ( sqlsrv_begin_transaction( $con ) === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }
    $resultado  =   ejecutarConsulta($con,$sentencia,$datos);
    if($DevolverId){
        if(is_numeric($resultado)){
            sqlsrv_commit( $con );
            cerrarDBSQL($con);
            return $resultado;
        }else{
            sqlsrv_rollback( $con );
            cerrarDBSQL($con);
            return true;
        }
    }else{
        if(!$resultado) {
            sqlsrv_commit( $con );
            cerrarDBSQL($con);
            return false;
        }else{
            sqlsrv_rollback( $con );
            cerrarDBSQL($con);
            return true;
        }
    }
}
/*SENTENCIA BORRADO DE DATOS*/
function DBSQLDeleteB($sentencia,$datos){
    $con=conectarDBSQL();
    if ( sqlsrv_begin_transaction( $con ) === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }

    $resultado  =   delDatos($con,$sentencia,$datos);

    if(is_numeric($resultado)) {
        sqlsrv_commit( $con );
        cerrarDBSQL($con);
        return false;
    } else {
        sqlsrv_rollback( $con );
        cerrarDBSQL($con);
        return true;
    }
}
/*SENTENCIA COMPROBA SE TEMOS DATOS*/
function DBSQLCheckB($sentencia,$datos){
    $con       =   conectarDBSQL();
    $resultado = comprobarSiTieneRegistros($con,$sentencia,$datos);
    cerrarDBSQL($con);
    return      $resultado;
}
/*FUNCIÓN QUE LEE UN CAMPOw*/
function DBSQLReadOne($sentencia,$datos){
    $con        =conectarDBSQL();
    $resultado   = leer1Parametro($con,$sentencia,$datos);
    cerrarDBSQL($con);
    return $resultado;
}
/*FUNCIÓN QUE DEVOLVE O NUMERO DE REGISTROS*/
function DBSQLNumRegistros($sentencia,$datos){
    $con         = conectarDBSQL();
    $resultado   = numRegistros($con,$sentencia,$datos);
    cerrarDBSQL($con);
    return $resultado;
}

/*FUNCIÓN QUE DEVOLVE O NUMERO DE REGISTROS, IGUAL QUE COUNT PERO APARTANDOI DO CODIGO PRINCIPAL*/
function numRegistrosB($c,$s,$d){
    $o =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
    $resultado = sqlsrv_query($c,$s,$d,$o);
    if( $resultado === false ) {
        die( print_r( sqlsrv_errors()));
    }else{
        $row_count = sqlsrv_num_rows($resultado);
        if ($row_count === false){
            return "Error in retrieveing row count.";
        }else{
            return $row_count;
        }
    }
}
/*EXISTEN REGISTROS*/
function comprobarSiTieneRegistrosB($c,$s,$d){
    $resultado = sqlsrv_query($c,$s,$d);
    if( $resultado === false ) {
        die( print_r( sqlsrv_errors()));
    }else{
        return sqlsrv_has_rows($resultado);
    }
}
/*LEER Y DEVOLVER UN SOLO RESULTADO*/
function leer1ParametroB($c,$s,$d){
    $resultado = sqlsrv_query($c,$s,$d);
    if( $resultado === false ) {
        die( print_r( sqlsrv_errors(), true));
    }else{
        if( sqlsrv_fetch( $resultado ) === false) {
            die( print_r( sqlsrv_errors(), true));
        }else{
            return sqlsrv_get_field( $resultado, 0);
        }
    }
}
/*FUNCION QUE COMPROBA SE O USUARIO QUE INTENTA LOGUEARSE EO CORRECTO*/
function comprobarUserPassB($Username,$Password){
    $sentencia   =   "SELECT Id FROM tm_usuarios WHERE Username=(?) AND Password=(?)";
    return DBSQLCheck($sentencia,Array($Username,$Password));
}
