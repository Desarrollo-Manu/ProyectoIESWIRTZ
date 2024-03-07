<?php
/*FUNCIÓN QUE OBTEN AS AREAS DE UNHA FAMILIA SELECCIONADA*/
function obterAreasFamilia($IdFamilia,$IdArea){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdArea,CodigoArea,Area FROM vw_fams_areas WHERE IdFamilia=? ORDER BY IdArea");
    $stmt->bind_param('i', $IdFamilia);
    $listAreas = array();
    if($stmt->execute()){
        $resultado=$stmt->get_result();
        if(($resultado->num_rows > 0)){
            while ($a = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listAreas[] = array(
                    'IdArea'        =>  $a['IdArea'],
                    'CodigoArea'    =>  $a['CodigoArea'],
                    'Area'          =>  $a['Area'],
                    'selected'      =>  ($a['IdArea']===$IdArea) ? 'selected' : ''
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listAreas;
}
/*FUNCIÓN QUE OBTEN UNHA LISTA DE TODOS OS CERTIFICADOS DE PROFESIONALIDAD ORDENADOS POR NOMBRE E NIVEL*/
function obterCertificados(){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Codigo,Nombre FROM tm_certificados  ORDER BY Codigo");
    $listCertificados = array();
    if($stmt->num_rows > 0){
        while ($c = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listCertificados[] = array(
                'Id'         =>  $c['Id'],
                'Nombre'     =>  $c['Nombre'],
                'Codigo'     =>  $c['Codigo'],
                'TextoCertificado'     =>  $c['Codigo'].' - '.$c['Nombre']
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listCertificados;
}
/*FUNCIÓN QUE CREA UN CERTIFICADO DE PROFESIONALIDAD*/
function saveCertificado($IdArea,$Codigo,$Nombre,$Nivel,$CompGeneral,$CualifProf,$Horas){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_certificados(Codigo,Nombre,IdArea,Nivel,CompGeneral,CualifProf,Horas,UsuAlta) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param('ssiissii', $Codigo,$Nombre,$IdArea,$Nivel,$CompGeneral,$CualifProf,$Horas,$_SESSION['Id']);
    if($stmt->execute()){
        $Id=$stmt->insert_id;
        $stmt->close();
        cerrarDBSQL($con);
        if(is_int($Id)){
            return $Id;
        }else{
            return null;
        }
    }else{
        $stmt->close();
        cerrarDBSQL($con);
        return false;
    }
}
/*ACTUALIZAR UN CERTIFICADO*/
function actualizarCertificado($Id,$IdArea,$Codigo,$Nombre,$Nivel,$CompGeneral,$CualifProf,$Horas){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_certificados SET Codigo=?,IdArea=?, Nombre=?, UsuModif=?, FecModif=CURDATE(), Nivel=?, CompGeneral=?, CualifProf=?, Horas=? WHERE Id=?");
    $stmt->bind_param('sisiissii', $Codigo,$IdArea,$Nombre,$_SESSION['Id'],$Nivel, $CompGeneral,$CualifProf,$Horas,$Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
/*FUNCIÓN QUE OBTEN TODA A INFORMACIÓN DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterCertificado($IdCert){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdCert,CodigoCert,Certificado,IdArea,CodigoArea,Area,Nivel,IdFamilia,Familia,CompGeneral,CualifProf,Horas FROM vw_fams_areas_certs WHERE IdCert = ? ");
    $stmt->bind_param('i', $IdCert);
    $certificado = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($c = $resultado->fetch_array(MYSQLI_ASSOC)){
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
                    'listAreas'         =>  obterAreasFamilia($c['IdFamilia'],$c['IdArea']),
                    'listFamilias'      =>  obterFamilias($c['IdFamilia']),
                );

            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $certificado;
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
/*FUNCIÓN QUE LISTA O ORDEN E MARCA COMO SELECT EN CANTO O ORDEN DOS MÓDULOS NUN CERTIFICADO*/
/*USADO PARA OS INPUT TYPE SELECT*/
function obterOrdenSelect($Orden){
    $or        = array();
    $or[]      = array(
        'Id'        =>  0,
        'selected'  =>  (0==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  1,
        'selected'  =>  (1==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  2,
        'selected'  =>  (2==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  3,
        'selected'  =>  (3==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  4,
        'selected'  =>  (4==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  5,
        'selected'  =>  (5==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  6,
        'selected'  =>  (6==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  7,
        'selected'  =>  (7==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  8,
        'selected'  =>  (8==$Orden) ? 'selected' : ''
    );
    $or[]      = array(
        'Id'        =>  9,
        'selected'  =>  (9==$Orden) ? 'selected' : ''
    );
    return $or;
}
/*FUNCIÓN QUE LISTA AS MFS COAS RESPECTIVAS UFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
/*LEVA UN COUNT PARA PODER PREPARAR TABLAS -> ROWSPAN*/
function obterMFsUFsCert($IdCert){
    $MFs=Array();
    foreach(obterMFsCert($IdCert) as $MF){
        $ufs            =   obterUFsModulo($MF['IdModulo']);
        //var_dump($ufs);
        $MFs[]=Array(
            'datosMFs'      =>  $MF,
            'listUFs'       =>  $ufs,
            'IdModulo'      =>  $MF['IdModulo'],
            'IdCert'        =>  $IdCert,
            'numUFs'        =>  !empty($ufs) ? count($ufs)  : 1
        );
    }
    return $MFs;
}
/*OBTER A LISTA DE MFS DONDE ESTÁ ASIGNADA UNHA UF*/
function obterMFsUF($IdUF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdMF FROM tr_modulos_unidades WHERE IdUF=?");
    $stmt->bind_param('i', $IdUF);
    if($stmt->execute()){
        $modulos = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($m = $stmt->fetch_array(MYSQLI_ASSOC)){
                $modulos[] = array(
                    'IdMF'    =>  $m['IdMF']
                );
            }
        }
        cerrarDBSQL($con);
        return ($modulos);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
/*FUNCIÓN QUE OBTEN OS MFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterMFsCert($IdCert){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdCert,IdModulo,CodModulo, NivelModulo, Modulo, HorasTotales, HorasFormacion, HorasTutoria, HorasExamen, Transversal,Orden,IdCertModulo FROM vw_certificados_modulos WHERE IdCert = ? ORDER BY Orden");
    $stmt->bind_param('i', $IdCert);
    $listModulos = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            while ($m = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listModulos[] = Array(
                    'IdCert'            =>  $m['IdCert'],
                    'MP'                =>  strpos($m['CodModulo'], 'MP')!==false,
                    'IdModulo'          =>  $m['IdModulo'],
                    'Modulo'            =>  $m['Modulo'],
                    'CodModulo'         =>  $m['CodModulo'],
                    'TextoModulo'       =>  $m['CodModulo'].' - '.$m['Modulo'],
                    'HorasModTotales'   =>  $m['HorasTotales'],
                    'HorasTotales'  =>  $m['HorasTotales'],
                    'HorasFormacion'=>  $m['HorasFormacion'],
                    'HorasTutoria'  =>  $m['HorasTutoria'],
                    'HorasExamen'   =>  $m['HorasExamen'],
                    'Nivel'         =>  $m['NivelModulo'],
                    /*'TUFs'             =>  comprobarUFsModulo($modulo['IdModulo']),*/
                    'Transversal'   =>  $m['Transversal'],
                    'ClassTransversal' =>  ($m['Transversal']) ? 'table-primary' : '',
                    'IdCertModulo'  =>  $m['IdCertModulo'],
                    'Orden'         =>  $m['Orden'],
                    'UC'            =>  obterUCsMF(intval($m['IdModulo']))
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return ($listModulos);
}
/*FUNCIÓN QUE OBTENR OS MFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterMF($IdCertificado,$IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdCert,IdModulo,CodModulo,NivelModulo,Modulo,HorasTotales,HorasFormacion,HorasTutoria,HorasExamen,Orden,Transversal FROM vw_certificados_modulos WHERE IdCert =? AND IdModulo=?");
    $stmt->bind_param('ii', $IdCertificado,$IdModulo);
    $listModulos = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($modulo = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listModulos[] = array(
                    'IdCertificado'         =>  $modulo['IdCert'],
                    'IdModulo'              =>  $modulo['IdModulo'],
                    'CodModulo'             =>  $modulo['CodModulo'],
                    'Nombre'                =>  $modulo['Modulo'],
                    'TextoModulo'           =>  $modulo['CodModulo'].' - '.$modulo['Modulo'],
                    'HorasTotales'          =>  $modulo['HorasTotales'],
                    'HorasFormacion'        =>  $modulo['HorasFormacion'],
                    'HorasTutoria'          =>  $modulo['HorasTutoria'],
                    'HorasExamen'           =>  $modulo['HorasExamen'],
                    'Nivel'                 =>  $modulo['NivelModulo'],
                    'UC'                    =>  obterUCsMF($modulo['IdModulo']),
                    'listUCs'               =>  obterUCs(),
                    'listNivelSelect'       =>  obterNivelCertSelect($modulo['NivelModulo']),
                    'listOrdenSelect'       =>  obterOrdenSelect($modulo['Orden']),
                    'TransversalCheckbox'   => ($modulo['Transversal']==1) ? 'checked' : ''
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return ($listModulos);
}
/*FUNCIÓN QUE OBTER OS DATOS SIN ASOCIAR DE UNHA UNIDAD FORMATIVA*/
function obterUFSolo($IdUF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,Codigo, Nombre, HorasTotales, HorasFormacion, HorasTutoria, HorasExamen FROM tm_unidades WHERE IdUF=?");
    $stmt->bind_param('i', $IdUF);
    if($stmt->execute()){
        $unidades = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
                $unidades[] = array(
                    'Id'              =>  $u['Id'],
                    'Codigo'                =>  $u['Codigo'],
                    'Nombre'                =>  $u['Nombre'],
                    'TextoModulo'           =>  $u['Codigo'].' - '.$u['Nombre'],
                    'HorasTotales'          =>  $u['HorasTotales'],
                    'HorasFormacion'        =>  $u['HorasFormacion'],
                    'HorasTutoria'          =>  $u['HorasTutoria'],
                    'HorasExamen'           =>  $u['HorasExamen'],
                    'Nivel'                 =>  $u['Nivel'],
                    'listNivelSelect'       =>  obterNivelCertSelect($u['Nivel']),
                    'listOrdenSelect'       =>  obterOrdenSelect($u['Orden']),
                    'TransversalCheckbox'   => ($u['Transversal']) ? 'checked' : ''
                );
            }
        }
        cerrarDBSQL($con);
        return ($unidades);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
function obterIdUCOLD($Codigo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_unidades_competencia WHERE Codigo=?");
    $stmt->bind_param('s', $Codigo);
    $listUnidades = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listUnidades[] = Array(
                    'Id'   =>  $u['Id']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUnidades;
}
function obterIdUC($Codigo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_unidades_competencia WHERE Codigo LIKE ?");
    $stmt->bind_param('s', $Codigo);$Id=null;
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        $Id=$resultado->fetch_array(MYSQLI_ASSOC)['Id'];
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}

function obterRelCertMF($IdCertificado,$IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_certificados_modulos WHERE IdCert=? AND IdMF=?");
    $stmt->bind_param('ss', $IdCertificado,$IdModulo);
    $Id = null;
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $Id   =  $u['Id'];
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function obterRelMFUF($IdModulo,$IdUnidad){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_modulos_unidades WHERE IdMF=? AND IdUF=?");
    $stmt->bind_param('ss', $IdModulo,$IdUnidad);
    $Id = null;
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $Id   =  $u['Id'];
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}



/*FUNCIÓN QUE OBTENR OS MFS DE UN CERTIFICADO DE PROFESIONALIDAD*/
function obterMFSolo($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdModulo,Codigo, Nivel, Nombre, TextoModulo, HorasTotales, HorasFormacion, HorasTutoria, HorasExamen, Transversal FROM vw_modulos WHERE IdModulo=?");
    $stmt->bind_param('i', $IdModulo);
    if($stmt->execute()){
        $modulos = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
                $modulos[] = array(
                    'IdModulo'              =>  $u['IdModulo'],
                    'Codigo'                =>  $u['Codigo'],
                    'Nombre'                =>  $u['Nombre'],
                    'TextoModulo'           =>  $u['TextoModulo'],
                    'HorasTotales'          =>  $u['HorasTotales'],
                    'HorasFormacion'        =>  $u['HorasFormacion'],
                    'HorasTutoria'          =>  $u['HorasTutoria'],
                    'HorasExamen'           =>  $u['HorasExamen'],
                    'Nivel'                 =>  $u['Nivel'],
                    'UC'                    =>  obterUCsMF($u['IdEntidad']),
                    'Transversal'           =>  $u['Transversal'],
                    'listNivelSelect'       =>  obterNivelCertSelect($u['Nivel']),
                    'TransversalCheckbox'   => ($u['Transversal']) ? 'checked' : ''
                );
            }
        }
        cerrarDBSQL($con);
        return ($modulos);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
function obterCertificadosParaModulo($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdModulo,CodModulo,IdCertificado,NombreCertificado,TextoCertificado, Nivel, NombreModulo, TextoModulo, HorasModTotales, HorasModFormacion, HorasModTutoria, HorasModExamen, Orden, Transversal FROM vw_certificados_modulos WHERE IdModulo=?");
    $stmt->bind_param('i', $IdModulo);
    if($stmt->execute()){
        $modulos = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
                $modulos[] = array(
                    'IdModulo'              =>  $u['IdModulo'],
                    'CodModulo'             =>  $u['CodModulo'],
                    'Nombre'                =>  $u['Nombre'],
                    'TextoModulo'           =>  $u['TextoModulo'],
                    'HorasTotales'          =>  $u['HorasTotales'],
                    'HorasFormacion'        =>  $u['HorasFormacion'],
                    'HorasTutoria'          =>  $u['HorasTutoria'],
                    'HorasExamen'           =>  $u['HorasExamen'],
                    'Nivel'                 =>  $u['Nivel'],
                    'UC'                    =>  obterUCsMF($u['IdEntidad']),
                    'Transversal'           =>  $u['Transversal'],
                    'listNivelSelect'       =>  obterNivelCertSelect($u['Nivel']),
                    'TransversalCheckbox'   => ($u['Transversal']) ? 'checked' : ''
                );
            }
        }
        cerrarDBSQL($con);
        return ($modulos);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
function obterCertificadosParaUnidad($IdUnidad){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdModulo,IdCertificado,NombreCertificado,TextoCertificado, Nivel, NombreModulo, TextoModulo, HorasModTotales, HorasModFormacion, HorasModTutoria, HorasModExamen, OrdenModulo, OrdenUnidad, Transversal,IdUnidad,NombreUnidad,HorasUniExamen,HorasUniFormacion,HorasUniTotales,HorasUniTutoria FROM vw_certificados_modulos_unidades WHERE IdUnidad=?");
    $stmt->bind_param('i', $IdUnidad);
    if($stmt->execute()){
        $modulos = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
                $modulos[] = array(
                    'IdModulo'              =>  $u['IdModulo'],
                    'IdUnidad'              =>  $u['IdUnidad'],
                    'TipoEntidad'           =>  'Unidad',
                    'NombreModulo'          =>  $u['NombreModulo'],
                    'IdCertificado'         =>  $u['IdCertificado'],
                    'NombreCertificado'     =>  $u['NombreCertificado'],
                    'TextoCertificado'      =>  $u['TextoCertificado'],
                    'OrdenUnidad'           =>  $u['OrdenUnidad'],
                    'OrdenModulo'           =>  $u['OrdenModulo'],
                    'TextoModulo'           =>  $u['TextoModulo'],
                    'HorasModTotales'       =>  $u['HorasModTotales'],
                    'HorasModFormacion'     =>  $u['HorasModFormacion'],
                    'HorasModTutoria'       =>  $u['HorasModTutoria'],
                    'HorasModExamen'        =>  $u['HorasModExamen'],
                    'HorasUniTotales'       =>  $u['HorasUniTotales'],
                    'HorasUniFormacion'     =>  $u['HorasUniFormacion'],
                    'HorasUniTutoria'       =>  $u['HorasUniTutoria'],
                    'HorasUniExamen'        =>  $u['HorasUniExamen'],
                    'Nivel'                 =>  $u['Nivel'],
                    'UC'                    =>  obterUCsMF($u['IdModulo']),
                    'Transversal'           =>  $u['Transversal'],
                    'TransversalCheckbox'   => ($u['Transversal']) ? 'checked' : '',
                );
            }
        }
        cerrarDBSQL($con);
        return ($modulos);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
/*FUNCIÓN QUE ACTUALIZA A RELACIÓN DUN MÓDULO FORMATIVO CON RESPECTO AO CERTIFICADO*/
function actualizarMFCert($Id,$Orden){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tr_certificados_modulos SET Orden=?, UsuModif=? WHERE Id=?");
    $stmt->bind_param('iii', $Orden, $_SESSION["Id"],$Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
/*FUNCIÓN QUE ACTUALIZA UN MÓDULO FORMATIVO*/
function actualizarMF($Codigo,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,$Transversal,$IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_modulos SET Codigo=?, Nombre=?, HorasTotales=?, HorasTutoria=?, HorasExamen=?, HorasFormacion=?,Nivel=?,Transversal=?, UsuModif=? WHERE Id=?");
    $stmt->bind_param('ssiiiiiiii', $Codigo, $Nombre, $HorasTotales, $HorasTutoria, $HorasExamen, $HorasFormacion,$Nivel,$Transversal,$_SESSION["Id"],$IdModulo);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
/*FUNCIÓN QUE CREA UN MÓDULO FORMATIVO*/
function newMF($Codigo,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$Nivel,$Transversal){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_modulos(Codigo,Nombre,HorasTotales,HorasFormacion,HorasTutoria,HorasExamen,Nivel,Transversal,UsuAlta) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('ssiiiiiii', $Codigo, $Nombre, $HorasTotales, $HorasFormacion, $HorasTutoria, $HorasExamen, $Nivel, $Transversal,$_SESSION['Id']);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}

function saveMFUC($IdMF,$IdUC){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_modulos_ucs(IdMF,IdUC,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('iii', $IdMF,$IdUC,$_SESSION['Id']);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return is_int($Id);
}
/*FUNCIÓN QUE ASIGNA UN MODULO FORMATIVO A UN CERTIFICADO*/
function asignarMFCert($IdCertificado, $IdModulo, $Orden){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_certificados_modulos(IdCert,IdMF,Orden,UsuAlta) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii', $IdCertificado, $IdModulo, $Orden, $_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return is_int($Id);
}
/*FUNCIÓN QUE OBTEN TODAS AS UFS*/
function obterUFs(){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Codigo, Nombre, HorasTotales,HorasTutoria,HorasExamen,HorasFormacion,Transversal FROM tm_unidades");
    $listUnidades = array();
    if($stmt->num_rows > 0){
        while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listUnidades[] = array(
                'Id'                    =>  $u['Id'],
                'Codigo'                =>  $u['Codigo'],
                'Nombre'                =>  $u['Nombre'],
                'HorasTotales'          =>  $u['HorasTotales'],
                'HorasFormacion'        =>  $u['HorasFormacion'],
                'HorasTutoria'          =>  $u['HorasTutoria'],
                'HorasExamen'           =>  $u['HorasExamen'],
                'Transversal'           =>  $u['Transversal'],
                'TransversalCheckbox'   => ($u['Transversal']) ? 'checked' : ''
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUnidades;
}
/*FUNCIÓN QUE OBTEN TODAS AS UFS DE UN MF*/
function obterUFsModulo($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdUF,CodUF,Unidad,HorasUFTotales,HorasUFFormacion,HorasUFTutoria,HorasUFExamen,IdModulo,CodModulo,Modulo,
       HorasModTotales,HorasModExamen,HorasModTutoria,HorasModFormacion,ModTransversal,IdMFUF,Orden FROM vw_modulos_unidades WHERE IdModulo=? ORDER BY Orden");
    $stmt->bind_param('i', $IdModulo);
    $listUnidades = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listUnidades[] = Array(
                    'IdUF'          =>  $u['IdUF'],
                    'CodUF'         =>  $u['CodUF'],
                    'Unidad'            =>  $u['Unidad'],
                    'TextoUnidad'       =>  $u['CodUF'].' - '.$u['Unidad'],
                    'HorasUniTotales'   =>  $u['HorasUFTotales'],
                    'HorasUniFormacion' =>  $u['HorasUFFormacion'],
                    'HorasUniTutoria'   =>  $u['HorasUFTutoria'],
                    'HorasUniExamen'    =>  $u['HorasUFExamen'],
                    'IdModulo'          =>  $u['IdModulo'],
                    'CodModulo'         =>  $u['CodModulo'],
                    'Modulo'            =>  $u['Modulo'],
                    'HorasModTotales'   =>  $u['HorasModTotales'],
                    'HorasModExamen'    =>  $u['HorasModExamen'],
                    'HorasModTutoria'   =>  $u['HorasModTutoria'],
                    'HorasModFormacion' =>  $u['HorasModFormacion'],
                    'ModTransversal'    =>  $u['ModTransversal'],
                    'ModTransversalCheckbox'   => ($u['ModTransversal']) ? 'checked' : '',
                    'ModClassTransversal'  =>  ($u['ModTransversal']) ? 'table-primary' : '',
                    'IdMFUF'            =>  $u['IdMFUF'],
                    'Orden'             =>  $u['Orden'],
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUnidades;
}
/*FUNCIONI QUE OBTEN UNHA UF EN CONCRETO DENTRO DE UN MÓDULO FORMATIVO*/
function obterUFdelMF($IdModulo,$IdUnidad){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdUF,CodUF, Unidad, HorasUFTotales,HorasUFFormacion,HorasUFTutoria,HorasUFExamen,Orden FROM vw_modulos_unidades WHERE IdModulo=? AND IdUF=? ORDER BY Orden; ");
    $stmt->bind_param('ii', $IdModulo,$IdUnidad);
    $listUnidades = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listUnidades[] = array(
                    'IdUF'              =>  $u['IdUF'],
                    'CodUF'             =>  $u['CodUF'],
                    'IdModulo'          =>  $IdModulo,
                    'Unidad'            =>  $u['Unidad'],
                    'HorasUniTotales'   =>  $u['HorasUFTotales'],
                    'HorasUniFormacion' =>  $u['HorasUFFormacion'],
                    'HorasUniTutoria'   =>  $u['HorasUFTutoria'],
                    'HorasUniExamen'    =>  $u['HorasUFExamen'],
                    'Orden'             =>  $u['Orden'],
                    'listOrdenSelect'   =>  obterOrdenSelect($u['Orden']),
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUnidades;
}
/*FUNCIÓN QUE LISTA TODALAS UCS*/
function obterUCs(){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Codigo,Denominacion FROM tm_unidades_competencia");
    $listUCs = array();
    if($stmt->num_rows > 0) {
        while ($uc = $stmt->fetch_array(MYSQLI_ASSOC)) {
            $listUCs[] = array(
                'Id' =>             $uc['Id'],
                'Codigo' =>         $uc['Codigo'],
                'Denominacion' =>   $uc['Denominacion'],
                'TextoUC' =>        $uc['Codigo'] . ' - ' . $uc['Denominacion']
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUCs;
}
/*FUNCIÓN QUE LISTA A UC DE UN MODULO*/
function obterUCsMF($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT tr_modulos_ucs.Id,Codigo, Denominacion  FROM tr_modulos_ucs, tm_unidades_competencia WHERE IdMF=? AND tr_modulos_ucs.IdUC=tm_unidades_competencia.Id");
    $stmt->bind_param('i', $IdModulo);
    $listUCs = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            while ($uc = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listUCs[] = array(
                    'Id'           =>  $uc['Id'],
                    'Codigo'       =>  $uc['Codigo'],
                    'Denominacion' =>  $uc['Denominacion'],
                    'TextoUC'      =>  $uc['Codigo'] .' - ' . $uc['Denominacion']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUCs;
}
/*FUNCIÓN QUE CREA UNHA UC*/
function newUC($Codigo,$Descripcion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_unidades_competencia(Codigo,Denominacion,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('ssi', $Codigo,$Descripcion,$_SESSION['Id']);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
/*FUNCIÓN QUE ASIGNA UNHA UC A UN MODULO*/
function asignarUCMF($IdModulo,$IdUC){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_modulos_ucs(IdMF,IdUC,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('iii', $IdModulo,$IdUC,$_SESSION['Id']);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return is_int($Id);
}

/*FUNCIÓN QUE OBTEN AS OCUPACIONS EN BASE A UNHA FAMILIA PROFESIONAL*/
function obterOcupaciones(){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Codigo,Denominacion FROM tm_ocupaciones");
    $listOcupa = array();
    if($stmt->num_rows > 0){
        while ($oc = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listOcupa[] = array(
                'Id'                =>  $oc['Id'],
                'Codigo'            =>  $oc['Codigo'],
                'TextoOcupacion'    =>  $oc['Codigo'] . ' - ' . $oc['Denominacion']
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listOcupa;
}
function obterOcupacion($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,Codigo,Denominacion FROM tm_ocupaciones WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $ocupacion = Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($o = $resultado->fetch_array(MYSQLI_ASSOC)){
                $ocupacion[] = array(
                    'Id'            =>  $o['Id'],
                    'Codigo'        =>  $o['Codigo'],
                    'Denominacion'  =>  $o['Denominacion'],
                    'TextoOcupacion'=>  $o['Codigo'] .' - ' . $o['Denominacion']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $ocupacion;
}
/*FUNCIÓN QUE COMPROBA SI EXISTE UN CODIGO DE OCUPACIÓN*/
function comprobarOcupacion($Codigo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_ocupaciones WHERE Codigo=?");
    $stmt->bind_param('s',$Codigo);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA SI EXISTE UNHA OCUPACIÓN*/
function comprobarIdOcupacion($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_ocupaciones WHERE Id=?");
    $stmt->bind_param('i',$Id);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function comprobarOcupaCert($IdCertificado,$IdOcupacion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_certificados_ocupaciones WHERE IdOcup=? AND IdCert=?");
    $stmt->bind_param('ii',$IdOcupacion,$IdCertificado);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE OBTEN AS OCUPACIONS EN BASE A UNHA FAMILIA PROFESIONAL*/
function obterOcupacionesCert($IdCertificado){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,IdCert,CodigoCert,Certificado,IdOcup,CodOcup,Ocupacion FROM vw_certificados_ocupaciones WHERE IdCert=?");
    $stmt->bind_param('i', $IdCertificado);
    $listOcupaciones=Array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            while ($oc = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listOcupaciones[] = array(
                    'Id'                =>  $oc['Id'],
                    'IdCert'            =>  $oc['IdCert'],
                    'CodCert'           =>  $oc['CodigoCert'],
                    'Certificado'       =>  $oc['Certificado'],
                    'IdOcup'            =>  $oc['IdOcup'],
                    'CodOcup'           =>  $oc['CodOcup'],
                    'Ocupacion'         =>  $oc['Ocupacion'],
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return ($listOcupaciones);
}
/*FUNCIÓN QUE OBTEN AS OCUPACIONS*/
function obterListadoOcupaciones(){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,Codigo, Denominacion FROM tm_ocupaciones ORDER BY Denominacion");
    if($stmt->execute()){
        $listOcupaciones = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($oc = $stmt->fetch_array(MYSQLI_ASSOC)){
                $listOcupaciones[] = array(
                    'Id'            =>  $oc['Id'],
                    'Codigo'        =>  $oc['Codigo'],
                    'TextoOcupacion'=>  $oc['Codigo'] . ' - ' . $oc['Denominacion'],
                    'Denominacion'  =>  $oc['Denominacion']
                );
            }
        }
        cerrarDBSQL($con);
        return ($listOcupaciones);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
/*FUNCIÓN QUE LISTA TODOS OS MFS QUE NON ESTÁN ASIGNADOS A UN CERTIFICADO DE PROFESIONALIDAD.*/
function obterNoMFsCert($IdCertificado){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,Codigo, Nombre, Transversal FROM tm_modulos");
    //$stmt->bind_param('i', $IdCertificado);
    if($stmt->execute()){
        $listModulos = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($m = $stmt->fetch_array(MYSQLI_ASSOC)){
                $listModulos[] = array(
                    'Id'                =>  $m['Id'],
                    'Codigo'            =>  $m['Codigo'],
                    'Nombre'            =>  $m['Nombre'],
                    'TextoModulo'       =>  $m['Codigo'] . ' - '.$m['Nombre'],
                    'TransversalClass'  =>  ($m['Transversal']) ? 'Transversal' : ''
                );
            }
        }
        cerrarDBSQL($con);
        return ($listModulos);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
/*OBTER TODAS LAS UNIDADES FORMATIVAS QUE NO ESTÁN ASOCIADAS A UN MÓDULO FORMATIVO*/
function obterUFsNoMF($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT u.Id,u.Codigo,u.Nombre FROM tm_unidades AS u LEFT JOIN tr_modulos_unidades AS mu ON mu.IdUF=u.Id");
    //$stmt->bind_param('i', $IdModulo);
    $listUnidades = Array();
    if($stmt->execute()){
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listUnidades[] = array(
                    'Id'            =>  $u['Id'],
                    'Codigo'        =>  $u['Codigo'],
                    'Nombre'        =>  $u['Nombre'],
                    'TextoUnidad'   =>  $u['Codigo'].' - '.$u['Nombre']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return ($listUnidades);
}
/*FUNCIÓN QUE OBTEN AS FAMILIAS PROFESIONALES*/
function obterFamilias($IdFamilia){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id, Nombre FROM tm_familias_profesionales");
    $listFamilias = array();
    if($stmt->num_rows > 0){
        while ($f = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listFamilias[] = array(
                'Id'            =>  $f['Id'],
                'Nombre'        =>  $f['Nombre'],
                'TextoFamilia'  =>  $f['Id'].' - '.$f['Nombre'],
                'selected'      =>  ($f['Id']==$IdFamilia) ? 'selected' : ''
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listFamilias;
}
/*FUNCIÓN QUE OBTEN OS CERTIFICADOS DE PROFESIONALIDAD DE UNHA FAMILIA*/
function obterCertificadosFamilia($IdFamilia){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,Codigo, Nombre, TextoCertificado FROM vw_certificados WHERE IdFamilia=?");
    $stmt->bind_param('i', $IdFamilia);
    $listCertificados = array();
    if($stmt->execute()){
        if(($stmt->get_result()->num_rows > 0)){
            while ($c = $stmt->fetch_array(MYSQLI_ASSOC)){
                $listCertificados[] = array(
                    'Id'                =>  $c['Id'],
                    'Codigo'            =>  $c['Codigo'],
                    'Nombre'            =>  $c['Nombre'],
                    'TextoCertificado'  =>  $c['TextoCertificado']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return ($listCertificados);
}
/*FUNCIÓN QUE OBTEN TODOS OS MFS*/
function obterMFs(){
    $con  =   conectarDBSQL();
    $listModulos = array();
    $stmt = $con->query("SELECT Id,Codigo, Nombre FROM tm_modulos");
    if($stmt->num_rows > 0){
        while ($m = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listModulos[] = array(
                'Id'            =>  $m['Id'],
                'Codigo'        =>  $m['Codigo'],
                'Nombre'        =>  $m['Nombre'],
                'TextoModulo'   =>  $m['Codigo'] .' - '. $m['Nombre']
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return ($listModulos);
}

/*FUNCIÓN QUE COMPROBA QUE UNHA FAMILIA É CORRECTA.*/
function comprobarFamilia($IdFamilia){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id  FROM tm_familias_profesionales WHERE Id=?");
    $stmt->bind_param('i', $IdFamilia);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA QUE UNHA AREA É CORRECTA E ADEMAIS SE INDICA A FAMILIA CORRECTA.*/
function comprobarAreaFam($IdArea,$IdFamilia){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id  FROM tm_areas WHERE Id=? AND IdFamilia=?");
    $stmt->bind_param('ii', $IdArea,$IdFamilia);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA QUE UNHA AREA É CORRECTA E ADEMAIS SE INDICA A FAMILIA CORRECTA.*/
function comprobarArea($IdArea){
    $sentencia =   'SELECT Id  FROM tm_areas WHERE Codigo=?';
    return DBSQLCheck($sentencia,Array($IdArea));
}
/*FUNCION QUE COMPROBA SE UN MÓDULO FORMATIVO XA ESTA ASIGNADO A UN CERTIFICADO*/
function comprobarMFCert($IdCert,$IdMF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id  FROM tr_certificados_modulos WHERE IdCert=? AND IdMF=?");
    $stmt->bind_param('ii', $IdCert,$IdMF);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCION QUE COMPROBA SE XA EXISTE UN MÓDULO FORMATIVO*/
function comprobarMF($IdMF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id  FROM tm_modulos WHERE  Id=?");
    $stmt->bind_param('i', $IdMF);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows=== 1);
}
function comprobarCodMF($Codigo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id  FROM tm_modulos WHERE  Codigo=?");
    $stmt->bind_param('s', $Codigo);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows=== 1);
}
/*FUNCION QUE COMPROBA SE XA EXISTE UN CERTIFICADO DE PROFESIONALIDAD*/
function comprobarCert($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id  FROM tm_certificados WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA SE UNHA UC E VÁLIDA*/
function comprobarUC($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_unidades_competencia WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCION QUE COMPROBA SE UNHA UF ESTÁ ASIGNADA A UN MF*/
function comprobarUFMF($IdMF,$IdUF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_modulos_unidades WHERE IdMF=? AND IdUF=?");
    $stmt->bind_param('ii', $IdMF,$IdUF);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCION QUE COMPROBA SI EXISTE UNHA UF*/
function comprobarUF($IdUF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_unidades WHERE Id=?");
    $stmt->bind_param('i',$IdUF);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA SE UN MÓDULO FORMATIVO TEN ASOCIADO UNHA UC*/
function comprobarUCMF($IdModulo,$IdUC){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_modulos_ucs WHERE IdMF=? AND IdUC=?");
    $stmt->bind_param('ii',$IdModulo,$IdUC);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE ACTUALIZA UNHA UNIDAD DE COMPETENCIA NUN MÓDULO FORMATIVO*/
function actualizarUCMF($IdUC,$IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tr_modulos_ucs SET IdMF=?, IdUC=?, UsuModif=? WHERE Id=?");
    $stmt->bind_param('ssiiiiii', $IdModulo, $IdUC, $_SESSION["Id"],$Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function updateOcupacion($Id,$Codigo,$Denominacion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_ocupaciones SET Codigo=?, Denominacion=?, UsuModif=? WHERE Id=?");
    $stmt->bind_param('ssii', $Codigo, $Denominacion, $_SESSION["Id"],$Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
/*FUNCIÓN QUE COMPROBA SE UN MÓDULO FORMATIVO TEN UNHA UNIDAD DE COMPETENCIA ASIGNADA*/
function comprobarMFTieneUC($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_modulos_ucs WHERE IdMF=?");
    $stmt->bind_param('i',$IdModulo);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA SE UN MÓDULO FORMATIVO TEN UFS*/
function comprobarUFsModulo($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_mdulos_unidades WHERE IdMF=?");
    $stmt->bind_param('i',$IdModulo);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function saveUC($CodUC,$TextoUC){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_unidades_competencia(Codigo,Denominacion,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('ssi', $CodUC,$TextoUC,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function saveCertUFMF($IdModulo,$IdUnidad,$Orden){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_modulos_unidades(IdMF,IdUF,Orden,UsuAlta) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii', $IdModulo,$IdUnidad,$Orden,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return is_int($Id);
}
function saveCertOcup($IdCertificado,$IdOcupacion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_certificados_ocupaciones(IdCert,IdOcup,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('iii', $IdCertificado,$IdOcupacion,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return is_int($Id);
}
function saveCertAddMFCert($IdCertificado,$IdModulo,$Orden){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_certificados_modulos(IdCert,IdMF,Orden,UsuAlta) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii', $IdCertificado,$IdModulo,$Orden,$_SESSION['Id']);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function newUF($Codigo,$Nombre,$HorasTotales,$HorasFormacion,$HorasTutoria,$HorasExamen){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_unidades(Codigo,Nombre,HorasTotales,HorasFormacion,HorasTutoria,HorasExamen,UsuAlta) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param('ssiiiii', $Codigo,$Nombre,$HorasTotales,$HorasFormacion,$HorasTutoria,$HorasExamen,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function saveUFMF($IdModulo,$Codigo,$Orden){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_modulos_unidades(IdMF,IdUF,Orden,UsuAlta) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii', $IdModulo,$Codigo,$Orden,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function updateUF($Codigo,$Nombre,$HorasTotales,$HorasTutoria,$HorasExamen,$HorasFormacion,$IdUF){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_unidades SET Codigo=?, Nombre=?, HorasTotales=?, HorasTutoria=?, HorasExamen=?, HorasFormacion=?, UsuModif=? WHERE Id=?");
    $stmt->bind_param('ssiiiiii', $Codigo, $Nombre, $HorasTotales, $HorasTutoria, $HorasExamen, $HorasFormacion, $_SESSION["Id"],$IdUF);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function delCertMFCert($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tr_certificados_modulos WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function delCertOcupacion($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tr_certificados_ocupaciones WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function delCerUFMF($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tr_modulos_unidades WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function saveOcupacion($Codigo,$Nombre){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_ocupaciones(Codigo,Denominacion,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('ssi', $Codigo,$Nombre,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function obterModulo($IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,Codigo, Nombre, HorasTotales,TextoModulo,Transversal,Nivel FROM vw_modulos WHERE Id=?");
    $stmt->bind_param('i', $IdModulo);
    if($stmt->execute()){
        $listModulos = array();
        if(($stmt->get_result()->num_rows > 0)){
            while ($m = $stmt->fetch_array(MYSQLI_ASSOC)){
                $listModulos[] = array(
                    'Id'                =>  $m['Id'],
                    'Codigo'            =>  $m['Codigo'],
                    'Nombre'            =>  $m['Nombre'],
                    'HorasTotales'      =>  $m['HorasTotales'],
                    'TextoModulo'       =>  $m['TextoModulo'],
                    'Transversal'       =>  $m['Transversal'],
                    'Nivel'             =>  $m['Nivel']
                );
            }
        }
        cerrarDBSQL($con);
        return ($listModulos);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}
function searchCerts($IdFamilia,$Nivel){
    $sqlSelect  = 'SELECT IdFamilia,Familia,IdArea,CodigoArea,Area,IdCert,CodigoCert,Certificado,Nivel,CompGeneral,CualifProf,Horas';
    $sqlFrom    = ' FROM vw_fams_areas_certs';
    $sqlWhere   = '';
    $sqlOrder   = ' ORDER BY IdFamilia';
    if($IdFamilia){
        $sqlWhere   .= ' WHERE IdFamilia=$IdFamilia';
    }
    if($Nivel){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE Nivel=$Nivel' : ' Nivel=$Nivel ';
    }

    $sentencia   = $sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
    $con  =   conectarDBSQL();
    $stmt = $con->query($sentencia);
    if($stmt->num_rows > 0){
        $listCertificados = array();
        while ($c = $stmt->fetch_array(MYSQLI_ASSOC)){
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
        cerrarDBSQL($con);
        return ($listCertificados);
    }else{
        cerrarDBSQL($con);
        return false;
    }
}