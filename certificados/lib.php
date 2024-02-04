<?php
/*FUNCIÓN QUE OBTEN AS AREAS DE UNHA FAMILIA SELECCIONADA*/
function obterAreasFamilia($IdFamilia,$IdArea){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT IdArea,CodigoArea,Area FROM VW_Fams_Areas WHERE IdFamilia=(?) ORDER BY IdArea";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdFamilia));
    if($stmt) {
        $listAreas = array();
        while ($a = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $listAreas[] = array(
                'IdArea'        =>  $a['IdArea'],
                'CodigoArea'    =>  $a['CodigoArea'],
                'Area'          =>  $a['Area'],
                'selected'      =>  ($a['IdArea']===$IdArea) ? 'selected' : ''
            );
        }
        cerrarDBSQL($con);
        return $listAreas;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN UNHA LISTA DE TODOS OS CERTIFICADOS DE PROFESIONALIDAD ORDENADOS POR NOMBRE E NIVEL*/
function obterCertificados(){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT  IdEntidad,Nombre,TextoCertificado FROM VW_Certificados WHERE TipoEntidad=1 ORDER BY IdEntidad";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $certificados = array();
        while ($c = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $certificados[] = array(
                'IdEntidad'         =>  $c['IdEntidad'],
                'Nombre'            =>  $c['Nombre'],
                'TextoCertificado'  =>  $c['TextoCertificado']
            );
        }
        cerrarDBSQL($con);
        return $certificados;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE CREA UN CERTIFICADO DE PROFESIONALIDAD*/
function saveCertificado($IdArea,$Codigo,$Nombre,$Nivel,$CompGeneral,$CualifProf,$Horas){
    $sentencia  = "INSERT INTO TM_Certificados(Codigo,Nombre,IdArea,Nivel,CompGeneral,CualifProf,Horas,UsuAlta) VALUES (?,?,?,?,?,?,?,?); SELECT SCOPE_IDENTITY()";
    $datos      = Array($Codigo,$Nombre,$IdArea,$Nivel,$CompGeneral,$CualifProf,$Horas,2);
    return DBSQLInsert($sentencia,$datos,true);
}
/*ACTUALIZAR UN CERTIFICADO*/
function actualizarCertificado($Id,$IdArea,$Codigo,$Nombre,$Nivel,$CompGeneral,$CualifProf,$Horas){
    $sentencia     = "UPDATE TM_Certificados SET Codigo=(?),IdArea=(?), Nombre=(?), UsuModif=(?), FecModif=GETDATE(), Nivel=(?), CompGeneral=(?), CualifProf=(?), Horas=(?) WHERE Id=(?)";
    $datos         = Array($Codigo,$IdArea,$Nombre,2,$Nivel, $CompGeneral,$CualifProf,$Horas,$Id);
    return DBSQLUpdate($sentencia,$datos);
}
/*FUNCIÓN QUE OBTEN TODA A INFORMACIÓN DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterCertificado($Idcert){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdCert,CodigoCert,Certificado,IdArea,CodigoArea,Area,Nivel,IdFamilia,Familia,CompGeneral,CualifProf,Horas FROM VW_Fams_Areas_Certs WHERE IdCert=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($Idcert));
    if($stmt) {
        $certificado=null;
        while ($c = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
            $certificado = array(
                'IdCert'            =>  $c['IdCert'],
                'CodigoCert'        =>  $c['CodigoCert'],
                'Certificado'       =>  $c['Certificado'],
                'IdArea'            =>  $c['IdArea'],
                'CodigoArea'        =>  $c['CodigoArea'],
                'Area'              =>  $c['Area'],
                'Nivel'             =>  $c['Nivel'],
                'NivelSelect'       =>  obterNivelCertSelect($c['Nivel']),
                'IdFamilia'         =>  $c['IdFamilia'],
                'Familia'           =>  $c['Familia'],
                'CompGeneral'       =>  $c['CompGeneral'],
                'CualifProf'        =>  $c['CualifProf'],
                'Horas'             =>  $c['Horas'],
                /*'Modulos'           =>  obterMFsCert($c['IdCert']),*/
                'Horas'             =>  $c['Horas'],
                'listAreas'         =>  obterAreasFamilia($c['IdFamilia'],$c['IdArea']),
                'listFamilias'      =>  obterFamilias($c['IdFamilia']),
            );
        }
        cerrarDBSQL($con);
        return $certificado;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE LISTA OS NIVELES E MARCA COMO SELECT O NIVEL DO CERTIFICADO*/
/*USADO PARA OS INPUT TYPE SELECT*/
function obterNivelCertSelect($Nivel){
    $niveles        = array();
    $niveles[]      = array(
        'Id'        =>  0,
        'selected'  =>  (0==$Nivel) ? 'selected' : ''
    );
    $niveles[]      = array(
        'Id'        =>  1,
        'selected'  =>  (1==$Nivel) ? 'selected' : ''
    );
    $niveles[]      = array(
        'Id'        =>  2,
        'selected'  =>  (2==$Nivel) ? 'selected' : ''
    );
    $niveles[]      = array(
        'Id'        =>  3,
        'selected'  =>  (3==$Nivel) ? 'selected' : ''
    );
    return $niveles;
}
/*FUNCIÓN QUE LISTA AS MFS COAS RESPECTIVAS UFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
/*LEVA UN COUNT PARA PODER PREPARAR TABLAS -> ROWSPAN*/
function obterMFsUFsCert($IdCert){
    $MFs=Array();
    foreach(obterMFsCert($IdCert) as $MF){
        $ufs            =   obterUFsModulo($MF['IdModulo']);
        $MFs[]=Array(
            'datosMFs'      =>  $MF,
            'listUFs'       =>  $ufs,
            'IdModulo'      =>  $MF['IdModulo'],
            'IdCert'        =>  $IdCert,
            'numUFs'        =>  (count($ufs)) ? count($ufs)  : 1
        );
    }
    return $MFs;
}
/*OBTER A LISTA DE MFS DONDE ESTÁ ASIGNADA UNHA UF*/
function obterMFsUF($IdUnidad){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdMF FROM TR_Modulos_Unidades WHERE IdUF=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdUnidad));
    if($stmt) {
        $modulos = array();
        while ($m = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdMF'    =>  $m['IdMF']
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN OS MFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterMFsCert($IdCert){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdCert,IdModulo,CodModulo, NivelModulo, Modulo, HorasTotales, HorasFormacion, HorasTutoria, HorasExamen, Transversal,IdUC,Orden,IdCertModulo FROM VW_Certificados_Modulos WHERE IdCert=(?) ORDER BY Orden";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdCert));
    if($stmt) {
        $modulos = array();
        while ($modulo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdCert'            =>  $modulo['IdCert'],
                'MP'                =>  strpos($modulo['CodModulo'], 'MP')!==false,
                'IdModulo'          =>  $modulo['IdModulo'],
                'Modulo'            =>  $modulo['Modulo'],
                'HorasModTotales'   =>  $modulo['HorasTotales'],
                'HorasTotales'  =>  $modulo['HorasTotales'],
                'HorasFormacion'=>  $modulo['HorasFormacion'],
                'HorasTutoria'  =>  $modulo['HorasTutoria'],
                'HorasExamen'   =>  $modulo['HorasExamen'],
                'NivelModulo'   =>  $modulo['NivelModulo'],
                'CodModulo'     =>  $modulo['CodModulo'],
                /*'TUFs'             =>  comprobarUFsModulo($modulo['IdModulo']),*/
                'Transversal'   =>  $modulo['Transversal'],
                'ClassTransversal' =>  ($modulo['Transversal']) ? 'table-primary' : '',
                'IdCertModulo'  =>  $modulo['IdCertModulo'],
                'Orden'         =>  $modulo['Orden'],
                'IdUC'          =>  $modulo['IdUC'],
                'UC'            =>  obterUCsMF($modulo['IdModulo']),

            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTENR OS MFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterMF($IdCertificado,$IdModulo){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT IdModulo, Nivel, NombreModulo, TextoModulo, HorasModTotales, HorasModFormacion, HorasModTutoria, HorasModExamen, Orden, Transversal FROM VW_Certificados_Modulos WHERE IdCertificado=(?) AND IdModulo=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdCertificado,$IdModulo));
    if($stmt) {
        $modulos = array();
        while ($modulo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdModulo'              =>  $modulo['IdModulo'],
                'TipoEntidad'           =>  'Modulo',
                'Nombre'                =>  $modulo['NombreModulo'],
                'TextoModulo'           =>  $modulo['TextoModulo'],
                'HorasTotales'          =>  $modulo['HorasModTotales'],
                'HorasFormacion'        =>  $modulo['HorasModFormacion'],
                'HorasTutoria'          =>  $modulo['HorasModTutoria'],
                'HorasExamen'           =>  $modulo['HorasModExamen'],
                'Nivel'                 =>  $modulo['Nivel'],
                'UC'                    =>  obterUCsMF($modulo['IdModulo']),
                'listUCs'               =>  obterUCs(),
                'listNivelSelect'       =>  obterNivelCertSelect($modulo['Nivel']),
                'listOrdenSelect'       =>  obterOrdenSelect($modulo['Orden']),
                'TransversalCheckbox'   => ($modulo['Transversal']) ? 'checked' : ''
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTER OS DATOS SIN ASOCIAR DE UNHA UNIDAD FORMATIVA*/
function obterUFSolo($IdModulo){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad, Nombre, HorasTotales, HorasOnline As HorasFormacion, HorasTutoria, HorasExamen FROM TM_Unidades WHERE IdEntidad=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt){
        $unidades = array();
        while ($unidad = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $unidades[] = array(
                'IdUnidad'              =>  $unidad['IdEntidad'],
                'IdEntidad'             =>  $unidad['IdEntidad'],
                'TipoEntidad'           =>  'Unidad',
                'Nombre'                =>  $unidad['Nombre'],
                'TextoUnidad'           =>  $unidad['IdEntidad'].' - '.$unidad['Nombre'],
                'HorasTotales'          =>  $unidad['HorasTotales'],
                'HorasFormacion'        =>  $unidad['HorasFormacion'],
                'HorasTutoria'          =>  $unidad['HorasTutoria'],
                'HorasExamen'           =>  $unidad['HorasExamen'],
            );
        }
        cerrarDBSQL($con);
        return $unidades;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTENR OS MFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterMFSolo($IdModulo){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad, Nivel, Nombre, TextoModulo, HorasTotales, HorasFormacion, HorasTutoria, HorasExamen, Transversal FROM VW_Modulos WHERE IdEntidad=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt) {
        $modulos = array();
        while ($modulo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdModulo'              =>  $modulo['IdEntidad'],
                'IdEntidad'             =>  $modulo['IdEntidad'],
                'TipoEntidad'           =>  'Modulo',
                'Nombre'                =>  $modulo['Nombre'],
                'TextoModulo'           =>  $modulo['TextoModulo'],
                'HorasTotales'          =>  $modulo['HorasTotales'],
                'HorasFormacion'        =>  $modulo['HorasFormacion'],
                'HorasTutoria'          =>  $modulo['HorasTutoria'],
                'HorasExamen'           =>  $modulo['HorasExamen'],
                'Nivel'                 =>  $modulo['Nivel'],
                'UC'                    =>  obterUCsMF($modulo['IdEntidad']),
                'Transversal'           =>  $modulo['Transversal'],
                'listNivelSelect'       =>  obterNivelCertSelect($modulo['Nivel']),
                'TransversalCheckbox'   => ($modulo['Transversal']) ? 'checked' : ''
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
function obterCertificadosParaModulo($IdModulo){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT IdModulo,IdCertificado,NombreCertificado,TextoCertificado,IdMoodle, Nivel, NombreModulo, TextoModulo, HorasModTotales, HorasModFormacion, HorasModTutoria, HorasModExamen, Orden, Transversal FROM VW_Certificados_Modulos WHERE IdModulo=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt) {
        $modulos = array();
        while ($modulo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdModulo'              =>  $modulo['IdModulo'],
                'TipoEntidad'           =>  'Modulo',
                'Nombre'                =>  $modulo['NombreModulo'],
                'IdCertificado'         =>  $modulo['IdCertificado'],
                'NombreCertificado'     =>  $modulo['NombreCertificado'],
                'TextoCertificado'      =>  $modulo['TextoCertificado'],
                'IdMoodle'              =>  $modulo['IdMoodle'],
                'SincroMoodle'          =>  comprobarPlatCert($modulo['IdCertificado'],1),
                'Orden'                 =>  $modulo['Orden'],
                'TextoModulo'           =>  $modulo['TextoModulo'],
                'HorasTotales'          =>  $modulo['HorasModTotales'],
                'HorasFormacion'        =>  $modulo['HorasModFormacion'],
                'HorasTutoria'          =>  $modulo['HorasModTutoria'],
                'HorasExamen'           =>  $modulo['HorasModExamen'],
                'Nivel'                 =>  $modulo['Nivel'],
                'UC'                    =>  obterUCsMF($modulo['IdModulo']),
                'Transversal'           =>  $modulo['Transversal'],
                'TransversalCheckbox'   => ($modulo['Transversal']) ? 'checked' : ''
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
function obterCertificadosParaUnidad($IdUnidad){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT IdModulo,IdCertificado,NombreCertificado,TextoCertificado, Nivel, NombreModulo, TextoModulo, HorasModTotales, HorasModFormacion, HorasModTutoria, HorasModExamen, OrdenModulo, OrdenUnidad, Transversal,IdUnidad,NombreUnidad,HorasUniExamen,HorasUniFormacion,HorasUniTotales,HorasUniTutoria,IdMoodleModulo,IdMoodleUnidad FROM VW_Certificados_ModulosUnidades WHERE IdUnidad=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdUnidad));
    if($stmt) {
        $modulos = array();
        while ($modulo = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdModulo'              =>  $modulo['IdModulo'],
                'IdUnidad'              =>  $modulo['IdUnidad'],
                'TipoEntidad'           =>  'Unidad',
                'NombreModulo'          =>  $modulo['NombreModulo'],
                'IdCertificado'         =>  $modulo['IdCertificado'],
                'NombreCertificado'     =>  $modulo['NombreCertificado'],
                'TextoCertificado'      =>  $modulo['TextoCertificado'],
                'SincroMoodle'          =>  comprobarPlatCert($modulo['IdCertificado'],1),
                'OrdenUnidad'           =>  $modulo['OrdenUnidad'],
                'OrdenModulo'           =>  $modulo['OrdenModulo'],
                'TextoModulo'           =>  $modulo['TextoModulo'],
                'HorasModTotales'       =>  $modulo['HorasModTotales'],
                'HorasModFormacion'     =>  $modulo['HorasModFormacion'],
                'HorasModTutoria'       =>  $modulo['HorasModTutoria'],
                'HorasModExamen'        =>  $modulo['HorasModExamen'],
                'HorasUniTotales'       =>  $modulo['HorasUniTotales'],
                'HorasUniFormacion'     =>  $modulo['HorasUniFormacion'],
                'HorasUniTutoria'       =>  $modulo['HorasUniTutoria'],
                'HorasUniExamen'        =>  $modulo['HorasUniExamen'],
                'Nivel'                 =>  $modulo['Nivel'],
                'UC'                    =>  obterUCsMF($modulo['IdModulo']),
                'Transversal'           =>  $modulo['Transversal'],
                'TransversalCheckbox'   => ($modulo['Transversal']) ? 'checked' : '',
                'IdMoodleModulo'        =>  $modulo['IdMoodleModulo'],
                'IdMoodleUnidad'        =>  $modulo['IdMoodleUnidad']
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE ACTUALIZA A RELACIÓN DUN MÓDULO FORMATIVO CON RESPECTO AO CERTIFICADO*/
function actualizarMFCert($IdModulo,$IdCertificado,$Orden){
    $sentencia     = "UPDATE TR_CertificadosModulos SET Orden=(?), UsuModif=(?), FecModif=GETDATE() WHERE IdCertificado=(?) AND IdModulo=(?)";
    $datos         = Array($Orden,$_SESSION["name"],$IdCertificado, $IdModulo);
    return DBSQLUpdate($sentencia,$datos);
}
/*FUNCIÓN QUE ACTUALIZA UN MÓDULO FORMATIVO*/
function actualizarMF($IdEntidad,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,$Transversal,$IdModuloOrig){
    $con            = conectarDBSQL();
    if ( sqlsrv_begin_transaction( $con ) === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }
    $sentencia1     = "UPDATE TM_Modulos SET IdEntidad=(?), Nombre=(?), HorasTotales=(?), HorasTutoria=(?), HorasExamen=(?), HorasFormacion=(?), UsuModif=(?), FecModif=GETDATE(), Nivel=(?), Transversal=(?) WHERE IdEntidad=(?)";
    $datos1         = Array($IdEntidad, $Nombre, $HorasTotales, $HorasTutoria, $HorasExamen, $HorasFormacion, $_SESSION["name"],$Nivel,$Transversal,$IdModuloOrig);
    $resultado1     = !ejecutarConsulta($con, $sentencia1, $datos1);

    if( $resultado1 ){
        sqlsrv_commit( $con );
        cerrarDBSQL($con);
        return false;
    }else{
        sqlsrv_rollback( $con );
        cerrarDBSQL($con);
        return true;
    }
}
/*FUNCIÓN QUE CREA UN MÓDULO FORMATIVO*/
function newMF($IdEntidad,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,$Transversal,$IdUC){
    $con = conectarDBSQL();
    if (sqlsrv_begin_transaction($con) === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $sentencia1 = "INSERT INTO TM_Modulos(IdEntidad,Nombre,HorasTotales,HorasFormacion,HorasTutoria,HorasExamen,Nivel,Transversal,UsuAlta,FecAlta) VALUES (?,?,?,?,?,?,?,?,?,GETDATE())";
    $datos1 = array($IdEntidad, $Nombre, $HorasTotales, $HorasFormacion, $HorasTutoria, $HorasExamen, $Nivel, $Transversal, $_SESSION["name"]);
    $resultado1 = ejecutarConsulta($con, $sentencia1, $datos1);
    if($IdUC&&comprobarUC($IdUC)){
        $sentencia2 = "INSERT INTO TR_ModulosUCs(IdModulo,IdUC,UsuAlta,FecAlta) VALUES (?,?,?,GETDATE())";
        $datos2 = array($IdEntidad, $IdUC, $_SESSION["name"]);
        $resultado2 = ejecutarConsulta($con, $sentencia2, $datos2);
    } else {
        $resultado2 = false;
    }
    $sentencia3 = "INSERT INTO TR_FormArbol(IdEntidad,TipoEntidad,IdNodoPadre,UsuAlta,FecAlta) VALUES (?,?,?,?,GETDATE()); SELECT SCOPE_IDENTITY()";
    $datos3 = array($IdEntidad, 'Modulo', null, $_SESSION["name"]);
    $resultado3 = ejecutarConsulta($con, $sentencia3, $datos3);


    /* var_dump($resultado1);var_dump($resultado2);var_dump($resultado3);*/
    if (!$resultado1 && !$resultado2 && is_numeric($resultado3) ) {
        sqlsrv_commit($con);
        cerrarDBSQL($con);
        return false;
    } else {
        sqlsrv_rollback($con);
        cerrarDBSQL($con);
        return true;
    }
}
/*FUNCIÓN QUE ASIGNA UN MODULO FORMATIVO A UN CERTIFICADO*/
function asignarMFCert($IdCertificado, $IdModulo, $Orden){
    $sentencia = "INSERT INTO TR_CertificadosModulos(IdCertificado,IdModulo,Orden,UsuAlta,FecAlta) VALUES (?,?,?,?,GETDATE())";
    $datos     = array($IdCertificado, $IdModulo, $Orden, $_SESSION["name"]);
    return DBSQLInsert($sentencia,$datos,false);
}
/*FUNCIÓN QUE OBTEN TODAS AS UFS*/
function obterUFs(){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT Id,Codigo, Nombre, HorasTotales,HorasTutoria,HorasExamen,HorasFormacion,Transversal FROM TM_Unidades";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $ufs = array();
        while ($u = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ufs[] = array(
                'Id'     =>  $u['Id'],
                'Codigo'        =>  $u['Codigo'],
                'Nombre'        =>  $u['Nombre'],
                'HorasTotales'  =>  $u['HorasTotales'],
                'HorasFormacion'=>  $u['HorasFormacion'],
                'HorasTutoria'  =>  $u['HorasTutoria'],
                'HorasExamen'   =>  $u['HorasExamen'],
                'Transversal'   =>  $u['Transversal'],
                'ClassTransversal' =>  ($u['Transversal']) ? 'table-primary' : '',

            );
        }
        cerrarDBSQL($con);
        return $ufs;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN TODAS AS UFS DE UN MF*/
function obterUFsModulo($IdModulo){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdUnidad,CodUnidad,UFTransversal, Unidad, HorasUniTotales,HorasUniFormacion,HorasUniTutoria,HorasUniExamen,IdModulo,CodModulo,Modulo,
       HorasModTotales,HorasModExamen,HorasModTutoria,HorasModPresencial,ModTransversal,IdMFUF FROM VW_Modulos_Unidades WHERE IdModulo=(?) ORDER BY Orden";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt) {
        $ufs = array();
        while ($uf = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ufs[] = array(
                'IdUnidad'          =>  $uf['IdUnidad'],
                'CodUnidad'         =>  $uf['CodUnidad'],
                'UFTransversal'     =>  $uf['NombreModulo'],
                'UFTransversalCheckbox'   => ($uf['UFTransversal']) ? 'checked' : '',
                'Unidad'            =>  $uf['Unidad'],
                'HorasUniTotales'   =>  $uf['HorasUniTotales'],
                'HorasUniFormacion' =>  $uf['HorasUniFormacion'],
                'HorasUniTutoria'   =>  $uf['HorasUniTutoria'],
                'HorasUniExamen'    =>  $uf['HorasUniExamen'],
                'IdModulo'          =>  $uf['IdModulo'],
                'CodModulo'         =>  $uf['CodModulo'],
                'Modulo'            =>  $uf['Modulo'],
                'HorasModTotales'   =>  $uf['HorasModTotales'],
                'HorasModExamen'    =>  $uf['HorasModExamen'],
                'HorasModTutoria'   =>  $uf['HorasModTutoria'],
                'HorasModPresencial'   =>  $uf['HorasModPresencial'],
                'ModTransversal'    =>  $uf['ModTransversal'],
                'ModTransversalCheckbox'   => ($uf['ModTransversal']) ? 'checked' : '',
                'ClassTransversal'  =>  ($uf['Transversal']) ? 'table-primary' : '',
                'IdMFUF'            =>  $uf['IdMFUF'],
                'Orden'             =>  $uf['Orden'],
            );
        }
        cerrarDBSQL($con);
        return $ufs;
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
/*FUNCIONI QUE OBTEN UNHA UF EN CONCRETO DENTRO DE UN MÓDULO FORMATIVO*/
function obterUFdelMF($IdModulo,$IdUnidad){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdUnidad, TextoUnidad, NombreUnidad, HorasUniTotales,HorasUniFormacion,HorasUniTutoria,HorasUniExamen,TextoUnidad, Orden FROM VW_ModulosUnidades WHERE IdModulo=(?) AND IdUnidad=(?) ORDER BY Orden";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo,$IdUnidad));
    if($stmt) {
        $ufs = array();
        while ($uf = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ufs[] = array(
                'IdUnidad'          =>  $uf['IdUnidad'],
                'IdModulo'          =>  $IdModulo,
                'TipoEntidad'       =>  'Unidad',
                'TextoUnidad'       =>  $uf['TextoUnidad'],
                'NombreUnidad'      =>  $uf['NombreUnidad'],
                'HorasUniTotales'   =>  $uf['HorasUniTotales'],
                'HorasUniFormacion' =>  $uf['HorasUniFormacion'],
                'HorasUniTutoria'   =>  $uf['HorasUniTutoria'],
                'HorasUniExamen'    =>  $uf['HorasUniExamen'],
                'listOrdenSelect'   =>  obterOrdenSelect($uf['Orden']),
            );
        }
        cerrarDBSQL($con);
        return $ufs;
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
/*FUNCIÓN QUE LISTA TODALAS UCS*/
function obterUCs(){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdUc, Descripcion FROM TM_UnidadesCompetencias";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $ucs = array();
        while ($uc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ucs[] = array(
                'IdUc'         =>  $uc['IdUc'],
                'Descripcion'  =>  $uc['Descripcion'],
                'TextoUC'      =>  $uc['IdUc'] .' - ' . $uc['Descripcion']
            );
        }
        cerrarDBSQL($con);
        return $ucs;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE LISTA A UC DE UN MODULO*/
function obterUCsMF($IdModulo){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT TR_ModulosUCs.IdUC, Descripcion FROM TR_ModulosUCs, TM_UnidadesCompetencias WHERE IdModulo=(?) AND TR_ModulosUCs.IdUC=TM_UnidadesCompetencias.IdUC";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt) {
        $ucs = array();
        while ($uc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ucs[] = array(
                'IdUC'         =>  $uc['IdUC'],
                'Descripcion'  =>  $uc['Descripcion'],
                'TextoUC'      =>  $uc['IdUC']. ' - '. $uc['Descripcion']
            );
        }
        cerrarDBSQL($con);
        return $ucs;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE CREA UNHA UC*/
function newUC($IdUC,$Descripcion){
    $sentencia  = "INSERT INTO TM_UnidadesCompetencias(IdUC,Descripcion,UsuAlta,FecAlta) VALUES (?,?,?,GETDATE())";
    $datos      = Array($IdUC,$Descripcion,$_SESSION["name"]);
    return DBSQLInsert($sentencia,$datos,false);
}
/*FUNCIÓN QUE ASIGNA UNHA UC A UN MODULO*/
function asignarUCMF($IdModulo,$IdUC){
    $sentencia  = "INSERT INTO TR_ModulosUCs(IdModulo,IdUC,UsuAlta,FecAlta) VALUES (?,?,?,GETDATE())";
    $datos      = Array($IdModulo,$IdUC,$_SESSION["name"]);
    return DBSQLInsert($sentencia,$datos,false);
}

/*FUNCIÓN QUE OBTEN AS OCUPACIONS EN BASE A UNHA FAMILIA PROFESIONAL*/
function obterOcupaciones($IdFamilia){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEspecialidad, Nombre FROM TM_Especialidades WHERE IdFamiliasProfesionales=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdFamilia));
    if($stmt) {
        $ocup = array();
        while ($oc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ocup[] = array(
                'IdEspecialidad'=>  $oc['IdEspecialidad'],
                'Nombre'        =>  $oc['Nombre'],
                'TextoOcupacion'=>  $oc['IdEspecialidad'] . ' - ' . $oc['Nombre']
            );
        }
        cerrarDBSQL($con);
        return $ocup;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE COMPROBA SI EXISTE UNHA OCUPACIÓN*/
function comprobarOcupacion($IdOcupacion){
    $sentencia   =   "SELECT IdEspecialidad FROM TM_Especialidades WHERE IdEspecialidad=(?)";
    return DBSQLCheck($sentencia,Array($IdOcupacion));
}
function comprobarOcupaCert($IdCertificado,$IdOcupacion){
    $sentencia   =   "SELECT Id FROM TR_EspecialidadesCertificados WHERE IdEspecialidad=(?) AND IdCertificado=(?)";
    return DBSQLCheck($sentencia,Array($IdOcupacion,$IdCertificado));
}
/*FUNCIÓN QUE OBTEN AS OCUPACIONS EN BASE A UNHA FAMILIA PROFESIONAL*/
function obterOcupacionesCert($IdCertificado){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT ec.Id, ec.IdEspecialidad, e.Nombre FROM TM_Especialidades AS e, TR_EspecialidadesCertificados AS ec WHERE ec.IdCertificado=(?) AND ec.IdEspecialidad=e.IdEspecialidad";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdCertificado));
    if($stmt) {
        $ocup = array();
        while ($oc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ocup[] = array(
                'Id'            =>  $oc['Id'],
                'IdCertificado' =>  $IdCertificado,
                'IdOcupacion'   =>  $oc['IdEspecialidad'],
                'Nombre'        =>  $oc['Nombre'],
                'TextoOcupacion'=>  $oc['IdEspecialidad'] . ' - ' . $oc['Nombre']
            );
        }
        cerrarDBSQL($con);
        return $ocup;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN AS OCUPACIONS*/
function obterListadoOcupaciones(){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEspecialidad, Nombre, Propia FROM TM_Especialidades ORDER BY Nombre";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $ocup = array();
        while ($oc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ocup[] = array(
                'IdEspecialidad'=>  $oc['IdEspecialidad'],
                'IdEntidad'     =>  $oc['IdEspecialidad'],
                'Nombre'        =>  $oc['Nombre'],
                'TextoOcupacion'=>  $oc['IdEspecialidad'] . ' - ' . $oc['Nombre'],
                'Propia'        =>  $oc['Propia'],
                'PropiaChecked' =>  ($oc['Propia']) ? 'checked' : ''
            );
        }
        cerrarDBSQL($con);
        return $ocup;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE LISTA TODOS OS MFS QUE NON ESTÁN ASIGNADOS A UN CERTIFICADO DE PROFESIONALIDAD.*/
function obterNoMFsCert($IdCertificado){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad, Nombre, Transversal FROM TM_Modulos";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdCertificado));
    if($stmt) {
        $modulos = array();
        while ($m = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'TextoModulo'      =>  $m['IdEntidad'] . ' - '.$m['Nombre'],
                'TransversalClass' =>  ($m['Transversal']) ? 'Transversal' : ''
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*OBTER TODAS LAS UNIDADES FORMATIVAS QUE NO ESTÁN ASOCIADAS A UN MÓDULO FORMATIVO*/
function obterUFsNoMF($IdModulo){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT DISTINCT TextoUnidad, IdUnidad FROM VW_ModulosUnidades WHERE IdModulo != (?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt) {
        $ufs = array();
        while ($u = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $ufs[] = array(
                'TextoUnidad' =>  $u['TextoUnidad'],
                'IdUnidad'    =>  $u['IdUnidad']
            );
        }
        cerrarDBSQL($con);
        return $ufs;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN AS FAMILIAS PROFESIONALES*/
function obterFamilias($IdFamilia){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT Id, Nombre FROM TM_Familias_Profesionales";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $familias = array();
        while ($f = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $familias[] = array(
                'Id'            =>  $f['Id'],
                'Nombre'        =>  $f['Nombre'],
                'TextoFamilia'  =>  $f['Id'].' - '.$f['Nombre'],
                'selected'      =>  ($f['Id']===$IdFamilia) ? 'selected' : ''

            );
        }
        cerrarDBSQL($con);
        return $familias;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN OS CERTIFICADOS DE PROFESIONALIDAD DE UNHA FAMILIA*/
function obterCertificadosFamilia($IdEntidad){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad, Nombre, TextoCertificado FROM VW_Certificados WHERE IdFamilia=(?) AND TipoEntidad=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdEntidad,1));
    if($stmt) {
        $certificados = array();
        while ($c = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $certificados[] = array(
                'IdEntidad'         =>  $c['IdEntidad'],
                'Nombre'            =>  $c['Nombre'],
                'TextoCertificado'  =>  $c['TextoCertificado']
            );
        }
        cerrarDBSQL($con);
        return $certificados;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
function obterContratosOcupacionesPorFamilia($IdFamilia){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdItinerario, IdOcupacion, Nombre, Alias, Detalles, IdFamilia, Horas3M, Horas6M, Horas9M, Horas12M, Horas24M FROM VW_ItinerariosContratosResumen WHERE IdFamilia=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdFamilia));
    if($stmt) {
        $datos = array();
        while ($m = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $datos[] = array(
                'IdItinerario'  =>  $m['IdItinerario'],
                'IdOcupacion'   =>  $m['IdOcupacion'],
                'Nombre'        =>  $m['Nombre'],
                'TextoOcupacion'=>  $m['IdOcupacion'].' - '.$m['Nombre'],
                'Alias'         =>  $m['Alias'],
                'Detalles'      =>  $m['Detalles'],
                'IdFamilia'     =>  $m['IdFamilia'],
                'Horas3M'       =>  $m['Horas3M'],
                'Horas6M'       =>  $m['Horas6M'],
                'Horas9M'       =>  $m['Horas9M'],
                'Horas12M'      =>  $m['Horas12M'],
                'Horas24M'      =>  $m['Horas24M']
            );
        }
        cerrarDBSQL($con);
        return $datos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
function obterContratosFamiliasConOcupacion($IdFamilia){
    $con         =   conectarDBSQL();
    $sentencia   =   "SELECT DISTINCT IdFamilia, Familia FROM VW_ItinerariosContratosResumen ";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $datos = array();
        while ($m = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $datos[] = array(
                'IdFamilia'     =>  $m['IdFamilia'],
                'Familia'       =>  $m['Familia'],
                'TextoFamilia'  =>  $m['IdFamilia'].' - '.$m['Familia']
            );
        }
        cerrarDBSQL($con);
        return $datos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
/*FUNCIÓN QUE OBTEN TODOS OS MFS*/
function obterMFs(){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad, Nombre FROM TM_Modulos";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array());
    if($stmt) {
        $modulos = array();
        while ($m = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $modulos[] = array(
                'IdEntidad'         =>  $m['IdEntidad'],
                'Nombre'            =>  $m['Nombre'],
                'TextoModulo'       =>  $m['IdEntidad'] .' - '. $m['Nombre']
            );
        }
        cerrarDBSQL($con);
        return $modulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}

/*FUNCIÓN QUE COMPROBA QUE UNHA FAMILIA É CORRECTA.*/
function comprobarFamilia($IdFamilia){
    $sentencia =   'SELECT Id  FROM TM_Familias_Profesionales WHERE Id=(?)';
    return DBSQLCheck($sentencia,Array($IdFamilia));
}
/*FUNCIÓN QUE COMPROBA QUE UNHA AREA É CORRECTA E ADEMAIS SE INDICA A FAMILIA CORRECTA.*/
function comprobarAreaFam($IdArea,$IdFamilia){
    $sentencia =   'SELECT Id  FROM TM_Areas WHERE Id=(?) AND IdFamilia=(?)';
    return DBSQLCheck($sentencia,Array($IdArea,$IdFamilia));
}
/*FUNCIÓN QUE COMPROBA QUE UNHA AREA É CORRECTA E ADEMAIS SE INDICA A FAMILIA CORRECTA.*/
function comprobarArea($IdArea){
    $sentencia =   'SELECT IdEntidad  FROM TM_Areas WHERE IdEntidad=(?)';
    return DBSQLCheck($sentencia,Array($IdArea));
}
/*FUNCION QUE COMPROBA SE UN MÓDULO FORMATIVO XA ESTA ASIGNADO A UN CERTIFICADO*/
function comprobarMFCert($IdCertificado,$IdMF){
    $sentencia =   'SELECT IdCertificado  FROM TR_CertificadosModulos WHERE IdCertificado=(?) AND IdModulo=(?)';
    return DBSQLCheck($sentencia,Array($IdCertificado,$IdMF));
}
/*FUNCION QUE COMPROBA SE XA EXISTE UN MÓDULO FORMATIVO*/
function comprobarMF($IdMF){
    $sentencia =   'SELECT IdEntidad  FROM TM_Modulos WHERE  IdEntidad=(?)';
    return DBSQLCheck($sentencia,Array($IdMF));
}
/*FUNCION QUE COMPROBA SE XA EXISTE UN CERTIFICADO DE PROFESIONALIDAD*/
function comprobarCert($Id,$Codigo){
    if(!empty($Id)){
        $sentencia  =   'SELECT Id  FROM TM_Certificados WHERE Id=(?)';
        $datos      =   Array($Id);
    }else{
        $sentencia =   'SELECT Id  FROM TM_Certificados WHERE Codigo=(?)';
        $datos      =   Array($Codigo);
    }

    return DBSQLCheck($sentencia,$datos);
}
/*FUNCIÓN QUE COMPROBA SE UNHA UC E VÁLIDA*/
function comprobarUC($IdUC){
    $sentencia   =  "SELECT IdUc FROM TM_UnidadesCompetencias WHERE IdUC=(?)";
    return DBSQLCheck($sentencia,Array($IdUC));
}
/*FUNCION QUE COMPROBA SE UNHA UF ESTÁ ASIGNADA A UN MF*/
function comprobarUFMF($IdModulo,$IdUnidad){
    $sentencia   =   "SELECT IdModulo FROM TR_ModulosUnidades WHERE IdModulo=(?) AND IdUnidad=(?)";
    return DBSQLCheck($sentencia,Array($IdModulo,$IdUnidad));
}
/*FUNCION QUE COMPROBA SI EXISTE UNHA UF*/
function comprobarUF($IdUnidad){
    $sentencia   =   "SELECT IdEntidad FROM TM_Unidades WHERE IdEntidad=(?)";
    return DBSQLCheck($sentencia,Array($IdUnidad));
}
/*FUNCIÓN QUE COMPROBA SE UN MÓDULO FORMATIVO TEN ASOCIADO UNHA UC*/
function comprobarUCMF($IdModulo,$IdUC){
    $sentencia   =   "SELECT IdModulo FROM TR_ModulosUCs WHERE IdModulo=(?) AND IdUC=(?)";
    return DBSQLCheck($sentencia,Array($IdModulo,$IdUC));
}
/*FUNCIÓN QUE ACTUALIZA UNHA UNIDAD DE COMPETENCIA NUN MÓDULO FORMATIVO*/
function actualizarUCMF($IdUC,$IdModulo,$IdModuloOrig){
    $sentencia     = "UPDATE TR_ModulosUCs SET IdModulo=(?), IdUC=(?), UsuModif=(?), FecModif=GETDATE() WHERE IdModulo=(?)";
    $datos         = Array($IdModulo, $IdUC, $_SESSION["name"],($IdModuloOrig) ? $IdModuloOrig :$IdModulo);
    return DBSQLUpdate($sentencia,$datos);
}
/*FUNCIÓN QUE ELIMINA A ASIGNACIÓN DUNHA UNIDAD DE COMPETENCIA NUN MÓDULO FORMATIVO*/
function eliminarUCMF($IdModulo){
    $sentencia =   "DELETE FROM TR_ModulosUCs WHERE IdModulo=(?)";
    $datos     =   Array($IdModulo);
    return DBSQLDelete($sentencia,$datos);
}
/*FUNCIÓN QUE COMPROBA SE UN MÓDULO FORMATIVO TEN UNHA UNIDAD DE COMPETENCIA ASIGNADA*/
function comprobarMFTieneUC($IdModulo){
    $sentencia   =   "SELECT IdModulo FROM TR_ModulosUCs WHERE IdModulo=(?)";
    return DBSQLCheck($sentencia,Array($IdModulo));
}
/*FUNCIÓN QUE COMPROBA SE UN MÓDULO FORMATIVO TEN UFS*/
function comprobarUFsModulo($IdModulo){
    $sentencia   =   "SELECT IdModulo FROM TR_ModulosUnidades WHERE IdModulo=(?)";
    return DBSQLCheck($sentencia,Array($IdModulo));
}
function saveCertUFMF($IdModulo,$IdUnidad,$Orden){
    $sentencia  = "INSERT INTO TR_ModulosUnidades(IdModulo,IdUnidad,Orden,UsuAlta,FecAlta) VALUES (?,?,?,?,GETDATE()); SELECT SCOPE_IDENTITY()";
    $datos      = Array($IdModulo,$IdUnidad,$Orden,$_SESSION["name"]);
    return DBSQLInsert($sentencia,$datos,false);
}
function saveCertOcup($IdCertificado,$IdOcupacion){
    $sentencia  = "INSERT INTO TR_EspecialidadesCertificados(IdCertificado,IdEspecialidad) VALUES (?,?); SELECT SCOPE_IDENTITY()";
    $datos      = Array($IdCertificado,$IdOcupacion);
    return DBSQLInsert($sentencia,$datos,true);
}
function saveCertAddMFCert($IdCertificado,$IdModulo,$Orden){
    $sentencia  = "INSERT INTO TR_CertificadosModulos(IdCertificado,IdModulo,Orden,UsuAlta,FecAlta) VALUES (?,?,?,?,GETDATE()); SELECT SCOPE_IDENTITY()";
    $datos      = Array($IdCertificado,$IdModulo,$Orden,$_SESSION["name"]);
    return DBSQLInsert($sentencia,$datos,false);
}
function delCertMFCert($IdCertificado,$IdModulo){
    $sentencia  =   "DELETE FROM TR_CertificadosModulos WHERE IdCertificado=(?) AND IdModulo=(?)";
    $datos      =   Array($IdCertificado,$IdModulo);
    return DBSQLDelete($sentencia,$datos);
}
function delCerUFMF($IdUnidad,$IdModulo){
    $sentencia  =   "DELETE FROM TR_ModulosUnidades WHERE IdUnidad=(?) AND IdModulo=(?)";
    $datos      =   Array($IdUnidad,$IdModulo);
    return DBSQLDelete($sentencia,$datos);
}
function saveCertOcupaAddCert($Codigo,$Nombre,$IdCertificado,$IdFamilia,$Modalidad,$Propia,$Observaciones){
    $con         = conectarDBSQL();
    if ( sqlsrv_begin_transaction( $con ) === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }
    $sentencia1     = "INSERT INTO TM_Especialidades(IdEspecialidad,Nombre,IdFamiliasProfesionales,IdFamilia,Modalidad,UsuAlta,FecAlta,Propia) VALUES (?,?,?,?,?,?,GETDATE(),?)";
    $datos1         = Array($Codigo,$Nombre,$IdFamilia,$IdFamilia,$Modalidad,$_SESSION["name"],$Propia);
    $resultado1     = ejecutarConsulta($con,$sentencia1,$datos1);

    if($Observaciones){
        $sentencia2     = "INSERT INTO TM_ObservContratosF(IdEntidad,TipoEntidad,Observaciones,UsuAlta,FecAlta) VALUES (?,?,?,?,GETDATE())";
        $datos2         = Array($Codigo,'Especialidad',$Observaciones,$_SESSION["name"]);
        $resultado2     = ejecutarConsulta($con,$sentencia2,$datos2);

    }else{
        $resultado2=false;
    }

    $sentencia3     = "INSERT INTO TR_EspecialidadesCertificados(IdEspecialidad,IdCertificado) VALUES (?,?); SELECT SCOPE_IDENTITY()";
    $datos3         = Array($Codigo,$IdCertificado);
    $resultado3     = ejecutarConsulta($con,$sentencia3,$datos3);

    if( !$resultado1 && !$resultado2 && is_numeric($resultado3)) {
        sqlsrv_commit( $con );
        cerrarDBSQL($con);
        return false;
    } else {
        sqlsrv_rollback( $con );
        cerrarDBSQL($con);
        return true;
    }
}
/*FUNCIÓN QUE OBTEN TODA A INFORMACIÓN DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterCertificadoBasico($IdEntidad){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad,Nombre,IdAreaProf,NomAreaProf,TextoCertificado,Nivel,IdFamilia,Familia,AcredPres,AcredOnline,CompGeneral,CualifProf FROM VW_Certificados WHERE IdEntidad=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdEntidad));
    if($stmt) {
        $certificado=null;
        while ($c = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
            $certificado = array(
                'IdEntidad'             =>  $c['IdEntidad'],
                'Nombre'                =>  $c['Nombre'],
                'TextoCertificado'      =>  $c['TextoCertificado'],
                'IdAreaProf'            =>  $c['IdAreaProf'],
                'TextoArea'             =>  $c['IdAreaProf'] . ' - ' .$c['NomAreaProf'],
                'Nivel'                 =>  $c['Nivel'],
                'Familia'               =>  $c['Familia'],
                'TextoFamilia'          =>  $c['IdFamilia']. ' - ' . $c['Familia'],
                'AcredPres'             =>  $c['AcredPres'],
                'AcredOnlineIcon'       =>  ($c['AcredOnline']) ? '<i class="far fa-thumbs-up text-success fa-lg" title="Si"></i>' : null,
                'AcredOnline'           =>  $c['AcredOnline'],
                'AcredPresIcon'         =>  ($c['AcredPres']) ? '<i class="far fa-thumbs-up text-success fa-lg" title="Si"></i>' : null,
                'CompGeneral'           =>  $c['CompGeneral'],
                'CualifProf'         =>  $c['CualifProf'],
                'listAulas'             =>  obterAulasCert($c['IdEntidad']),
                'listProveedores'       =>  (comprobarPlatCert($IdEntidad,1)) ? obterProveCert($c['IdEntidad']) : '',
                'PlatMoodle'            =>  (comprobarPlatCert($IdEntidad,1)) ? 'Moodle' : '',
                'PlatMoodleCheck'       =>  (comprobarPlatCert($IdEntidad,1)) ? 'checked' : '' ,
                'DisPlatMoodleProve'    =>  (!comprobarPlatCert($IdEntidad,1)) ? 'disabled' : '',
                'PlatVertice'           =>  (comprobarPlatCert($IdEntidad,2)) ? 'Vertice' : '',
                'PlatVerticeCheck'      =>  (comprobarPlatCert($IdEntidad,2)) ? 'checked' : '' ,
            );
        }
        cerrarDBSQL($con);
        return $certificado;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
function obterModulo($IdModulo){
    $con         = conectarDBSQL();
    $sentencia   =   "SELECT IdEntidad, Nombre, HorasTotales,TextoModulo,Transversal,Nivel FROM VW_Modulos WHERE IdEntidad=(?)";
    $stmt        =   ejecutarConsultaLectura($con,$sentencia,Array($IdModulo));
    if($stmt) {
        $listModulos = array();
        while ($c = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $listModulos[] = array(
                'IdEntidad'         =>  $c['IdEntidad'],
                'Nombre'            =>  $c['Nombre'],
                'HorasTotales'      =>  $c['HorasTotales'],
                'TextoModulo'       =>  $c['TextoModulo'],
                'Transversal'       =>  $c['Transversal'],
                'Nivel'             =>  $c['Nivel']
            );
        }
        cerrarDBSQL($con);
        return $listModulos;
    }else{
        cerrarDBSQL($con);
        return null;
    }
}
function searchCerts($IdFamilia,$Nivel){
    $sqlSelect  = 'SELECT IdFamilia,Familia,IdArea,CodigoArea,Area,IdCert,CodigoCert,Certificado,Nivel,CompGeneral,CualifProf,Horas';
    $sqlFrom    = ' FROM VW_Fams_Areas_Certs';
    $sqlWhere   = '';
    $sqlOrder   = ' ORDER BY IdFamilia';
    $datos      =  Array();

    if($IdFamilia){
        $sqlWhere   .= ' WHERE IdFamilia=(?)';
        array_push($datos, $IdFamilia);
    }
    if($Nivel){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE Nivel=(?)' : ' Nivel=(?) ';
        array_push($datos, $Nivel);
    }

    $con         = conectarDBSQL();
    $sentencia   = $sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
    $stmt        = ejecutarConsultaLectura($con,$sentencia,$datos);
    if($stmt) {
        $listCertificados = array();
        while ($c = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $listCertificados[] = array(
                'IdFamilia'        =>  $c['IdFamilia'],
                'Familia'          =>  $c['Familia'],
                'IdArea'           =>  $c['IdArea'],
                'CodigoArea'       =>  $c['CodigoArea'],
                'Area'             =>  $c['Area'],
                'IdCert'           =>  $c['IdCert'],
                'CodigoCert'       =>  $c['CodigoCert'],
                'Certificado'      =>  $c['Certificado'],
                'Nivel'            =>  $c['Nivel'],
                'CompGeneral'      =>  $c['CompGeneral'],
                'CualifProf'       =>  $c['CualifProf'],
                'Horas'            =>  $c['Horas']
            );

        }
    }else{
        $listCertificados = null;
    }
    cerrarDBSQL($con);
    return $listCertificados;
}