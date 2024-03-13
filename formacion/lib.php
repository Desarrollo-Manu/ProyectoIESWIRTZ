<?php

/*FUNCIÓN QUE OBTEN TODAS AS UFS*/
function obterTiposFormacion($IdTipo){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Tipo FROM tm_formaciones_tipos");
    $listTipos = array();
    if($stmt->num_rows > 0){
        while ($t = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listTipos[] = array(
                'Id'     =>  $t['Id'],
                'Tipo'   =>  $t['Tipo'],
                'Selected'>=    ($IdTipo==$t['Id']) ? 'selected' : ''
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listTipos;
}
function obterRoles($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Rol FROM tm_roles");
    $listTipos = array();
    if($stmt->num_rows > 0){
        while ($t = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listTipos[] = array(
                'Id'        =>  $t['Id'],
                'Rol'       =>  $t['Rol'],
                'Selected'  =>    ($Id==$t['Id']) ? 'selected' : ''
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listTipos;
}

/*FUNCIÓN QUE CREA UN NOVO PLAN DE ESTUDIOS*/
function newPlan($Codigo,$IdCert,$Tipo,$FecIni,$FecFin){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_planes_estudios(Codigo,IdCert,IdTipo,FechaIni,FechaFin,UsuAlta) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param('ssissi', $Codigo,$IdCert,$Tipo,$FecIni,$FecFin,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
/*FUNCIÓN QUE COMPROBA SI EXISTE O TIPO DE FORMACIÓN SELECCIONADA*/
function comprobarTipoFormacion($IdTipo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_formaciones_tipos WHERE Id=?");
    $stmt->bind_param('i',$IdTipo);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE COMPROBA SI EXISTE O CODIGO DO PLAN DE ESTUDIOS*/
function comprobarCodigoFormacion($Codigo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_planes_estudios WHERE Codigo=?");
    $stmt->bind_param('s',$Codigo);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
/*FUNCIÓN QUE ACTUALIZA A INFORMACIÓN DE UN PLAN DE ESTUDIOS.*/
function actualizarPlan($Codigo,$IdTipo,$FecIni,$FecFin){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_planes_estudios SET Codigo=?, IdTipo=?,FechaIni=?,FechaFin=?,UsuModif=?,FecModif=curdate() WHERE Id=?");
    $stmt->bind_param('sissii', $Codigo, $IdTipo,$FecIni,$FecFin,$_SESSION["Id"],$Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
/*FUNCIÓN QUE COMPROBA SI EXISTE O ID DA FORMACIÓN DO PLAN DE ESTUDIOS*/
function comprobarIdFormacion($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_plan_estudios_formaciones WHERE Id=?");
    $stmt->bind_param('i',$Id);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function comprobarIdPlan($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_planes_estudios WHERE Id=?");
    $stmt->bind_param('i',$Id);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function obterPlanes($IdPlan){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT IdPlan,CodigoPlan,IdTipo,Tipo FROM vw_planes_estudios");
    $listPlanes = array();
    if($stmt->num_rows > 0){
        while ($t = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listPlanes[] = array(
                'IdPlan'        =>  $t['IdPlan'],
                'CodigoPlan'    =>  $t['CodigoPlan'],
                'IdTipo'        =>  $t['IdTipo'],
                'Tipo'          =>  $t['Tipo'],
                'Selected'      =>    ($IdPlan==$t['IdPlan']) ? 'selected' : ''
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listPlanes;
}
/*FUNCIÓN QUE OBTEN OS DATOS DO PLAN*/
function obterPlan($IdPlan){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdPlan,CodigoPlan,IdTipo,Tipo,FechaIni,FechaFin,IdCert,CodigoCert,Certificado,Nivel,Horas FROM vw_planes_estudios WHERE IdPlan=?");
    $stmt->bind_param('i', $IdPlan);
    $plan = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            while ($p = $resultado->fetch_array(MYSQLI_ASSOC)){
                $dateIni = new DateTimeImmutable($p['FechaIni']);
                $dateFin = new DateTimeImmutable($p['FechaFin']);
                $plan[] = array(
                    'IdPlan'            =>  $p['IdPlan'],
                    'CodigoPlan'        =>  $p['CodigoPlan'],
                    'IdTipo'            =>  $p['IdTipo'],
                    'Tipo'              =>  $p['Tipo'],
                    'IdCert'            =>  $p['IdCert'],
                    'CodigoCert'        =>  $p['CodigoCert'],
                    'Certificado'       =>  $p['Certificado'],
                    'Horas'             =>  $p['Horas'],
                    'Nivel'             =>  $p['Nivel'],
                    'FechaFin'          =>  ($p['FechaFin']) ? $dateFin->format('d/m/Y') : null,
                    'FechaIni'          =>  ($p['FechaIni']) ? $dateIni->format('d/m/Y') : null,
                    'TipoFormacionSelect'=>  obterTiposFormacion($p['IdTipo']),
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $plan;
}
/*FUNCIÓN QUE AÑADE UNHA FORMACIÓN AO PLAN DE ESTUDIOS*/
function saveModuloFormacion($IdPlan,$IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_plan_estudios_formaciones(IdPlan,IdModulo,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('iii', $IdPlan,$IdModulo,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
/*FUNCIÓN QUE AÑADE UN USUARIO AO PLAN DE FORMACIÓN*/
function saveUsuariosFormacion($IdFormacion,$Usuario,$IdRol){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_formaciones(IdFormacion,IdUsuario,IdRol,UsuAlta) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii', $IdFormacion,$Usuario,$IdRol,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function comprobarModuloFormacion($IdPlan,$IdModulo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_plan_estudios_formaciones WHERE IdPlan = ? AND IdModulo = ?");
    $stmt->bind_param('ii', $IdPlan,$IdModulo);$IdFormacion=null;
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        $IdFormacion=$resultado->fetch_array(MYSQLI_ASSOC)['Id'];
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $IdFormacion;
}
function obterformacionPlan($IdPlan){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdFormacion,IdPlan,FecIni,FecFin,IdModulo,Codigo,Nombre,Nivel,Transversal,HorasTotales FROM vw_planes_formaciones WHERE IdPlan=?");
    $stmt->bind_param('i', $IdPlan);
    $listformacion = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows >= 1) {
            while ($p = $resultado->fetch_array(MYSQLI_ASSOC)){
                $dateIni = new DateTimeImmutable($p['FecIni']);
                $dateFin = new DateTimeImmutable($p['FecFin']);
                $listformacion[] = array(
                    'IdPlan'            =>  $p['IdPlan'],
                    'IdFormacion'       =>  $p['IdFormacion'],
                    'IdModulo'          =>  $p['IdModulo'],
                    'Codigo'            =>  $p['Codigo'],
                    'Nombre'            =>  $p['Nombre'],
                    'TextoModulo'       =>  $p['Codigo'].' - '.$p['Nombre'],
                    'Nivel'             =>  $p['Nivel'],
                    'Transversal'       =>  $p['Transversal'],
                    'HorasTotales'      =>  $p['HorasTotales'],
                    'FechaFin'          =>  ($p['FecFin']) ? $dateFin->format('d/m/Y') : null,
                    'FechaIni'          =>  ($p['FecIni']) ? $dateIni->format('d/m/Y') : null
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listformacion;
}
function obterFormacionUsuarios($IdFormacion,$IdRol){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdUsuario,Nombre,Apellidos,IdFormUsuario,IdFormacion,IdRol,Rol FROM vw_formaciones_usuarios WHERE IdFormacion=? AND IdRol=?");
    $stmt->bind_param('ii', $IdFormacion,$IdRol);
    $listUsuarios = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows >= 1) {
            while ($p = $resultado->fetch_array(MYSQLI_ASSOC)){
                $listUsuarios[] = array(
                    'IdUsuario'     =>  $p['IdUsuario'],
                    'Nombre'        =>  $p['Nombre'],
                    'Apellidos'     =>  $p['Apellidos'],
                    'TextoUsuario'  =>  $p['Nombre'].' '.$p['Apellidos'],
                    'IdFormUsuario' =>  $p['IdFormUsuario'],
                    'IdFormacion'   =>  $p['IdFormacion'],
                    'IdRol'         =>  $p['IdRol'],
                    'Rol'           =>  $p['Rol']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUsuarios;
}
function obterFormacionUsuario($IdUsuario){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT IdPlan,CodigoPlan,FecIniPlan,FecFinPlan,IdCert,CodigoCert,Certificado,IdFormacion,IdModulo,CodigoModulo,Modulo,Horas,Nivel,IdUsuario,IdRol,Rol,IdFormUsuario FROM vw_planes_formaciones_usuarios WHERE IdUsuario=?");
    $stmt->bind_param('i', $IdUsuario);
    $listUsuarios = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows >= 1) {
            while ($p = $resultado->fetch_array(MYSQLI_ASSOC)){
                $dateIni = new DateTimeImmutable($p['FecIniPlan']);
                $dateFin = new DateTimeImmutable($p['FecFinPlan']);
                $listUsuarios[] = array(
                    'IdPlan'            =>  $p['IdPlan'],
                    'CodigoPlan'        =>  $p['CodigoPlan'],
                    'IdCert'            =>  $p['IdCert'],
                    'CodigoCert'        =>  $p['CodigoCert'],
                    'Certificado'       =>  $p['Certificado'],
                    'IdFormacion'       =>  $p['IdFormacion'],
                    'IdModulo'          =>  $p['IdModulo'],
                    'CodigoModulo'      =>  $p['CodigoModulo'],
                    'Modulo'            =>  $p['Modulo'],
                    'Horas'             =>  $p['Horas'],
                    'Nivel'             =>  $p['Nivel'],
                    'IdUsuario'         =>  $p['IdUsuario'],
                    'IdRol'             =>  $p['IdRol'],
                    'Rol'               =>  $p['Rol'],
                    'IdFormUsuario'     =>  $p['IdFormUsuario'],
                    'FecIniPlan'        =>  ($p['FecIniPlan']) ? $dateFin->format('d/m/Y') : null,
                    'FecFinPlan'        =>  ($p['FecFinPlan']) ? $dateIni->format('d/m/Y') : null
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUsuarios;
}
function delUsuariosFormacion($IdFormacion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tr_formaciones WHERE IdFormacion=?");
    $stmt->bind_param('i', $IdFormacion);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas>=1);
}
function delUsuariosIdFormacion($IdUsuFormacion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tr_formaciones WHERE Id=?");
    $stmt->bind_param('i', $IdUsuFormacion);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function delFormacion($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tm_plan_estudios_formaciones WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function comprobarUsuarioFormacion($IdFormacion, $Usuario){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_formaciones WHERE IdFormacion=? AND IdUsuario=?");
    $stmt->bind_param('ii',$IdFormacion, $Usuario);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function comprobarIdUsuForm($IdUsuarioFormacion){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_formaciones WHERE Id=?");
    $stmt->bind_param('i', $IdUsuarioFormacion);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function searchFormacion($IdPlan,$Tipo){
    $sqlSelect  = 'SELECT IdPlan,CodigoPlan,FechaIni,FechaFin,IdCert,CodigoCert,Certificado,IdTipo,Tipo,Nivel,Horas';
    $sqlFrom    = ' FROM vw_planes_estudios';
    $sqlWhere   = '';
    $sqlOrder   = ' ORDER BY FechaIni';
    if($IdPlan){
        $sqlWhere   .= ' WHERE IdPlan='.$IdPlan;
    }
    if($Tipo){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE IdTipo='.$Tipo : ' AND IdTipo='.$Tipo;
    }
    $listPlanes = array();
    $sentencia   = $sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
    $con  =   conectarDBSQL();
    $stmt = $con->query($sentencia);
    if($stmt->num_rows > 0){
        while ($p = $stmt->fetch_array(MYSQLI_ASSOC)){
            $dateIni = new DateTimeImmutable($p['FechaIni']);
            $dateFin = new DateTimeImmutable($p['FechaFin']);
            $listPlanes[] = array(
                'IdPlan'            =>  $p['IdPlan'],
                'CodigoPlan'        =>  $p['CodigoPlan'],
                'IdTipo'            =>  $p['IdTipo'],
                'Tipo'              =>  $p['Tipo'],
                'IdCert'            =>  $p['IdCert'],
                'CodigoCert'        =>  $p['CodigoCert'],
                'Certificado'       =>  $p['Certificado'],
                'TextoCert'         =>  $p['CodigoCert'].' - '.$p['Certificado'],
                'Horas'             =>  $p['Horas'],
                'Nivel'             =>  $p['Nivel'],
                'FechaFin'          =>  ($p['FechaFin']) ? $dateFin->format('d/m/Y') : null,
                'FechaIni'          =>  ($p['FechaIni']) ? $dateIni->format('d/m/Y') : null,
                'TipoFormacionSelect'=>  obterTiposFormacion($p['IdTipo']),
            );
        }
    }
    cerrarDBSQL($con);
    return ($listPlanes);
}

function searchAlumnos($IdCertificado,$IdPlan,$Nivel,$IdRol){
    $sqlSelect  = 'SELECT IdUsuario,Nombre,Apellidos,Rol,Tipo,IdPlan,CodigoPlan,IdCert,CodigoCert,Certificado,Nivel,Horas';
    $sqlFrom    = ' FROM vw_planes_formaciones_usuarios';
    $sqlWhere   = '';
    $sqlOrder   = ' ORDER BY Nombre';
    if($IdCertificado){
        $sqlWhere   .= ' WHERE IdCert='.$IdCertificado;
    }
    if($IdPlan){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE IdPlan='.$IdPlan : ' AND IdPlan='.$IdPlan;
    }
    if($Nivel){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE Nivel='.$IdPlan : ' AND Nivel='.$IdPlan;
    }
    if($IdRol){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE IdRol='.$IdRol : ' AND IdRol='.$IdRol;
    }
    $listUsuarios = array();
    $sentencia   = $sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
    $con  =   conectarDBSQL();
    $stmt = $con->query($sentencia);
    if($stmt->num_rows > 0){
        while ($p = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listUsuarios[] = array(
                'IdUsuario'     =>  $p['IdUsuario'],
                'Nombre'        =>  $p['Nombre'],
                'Apellidos'     =>  $p['Apellidos'],
                'Rol'           =>  $p['Rol'],
                'Tipo'          =>  $p['Tipo'],
                'Nivel'         =>  $p['Nivel'],
                'Horas'         =>  $p['Horas'],
                'IdPlan'        =>  $p['IdPlan'],
                'CodigoPlan'    =>  $p['CodigoPlan'],
                'Certificado'   =>  $p['Certificado'],
                'IdCert'        =>  $p['IdCert'],
                'CodigoCert'    =>  $p['CodigoCert'],
            );
        }
    }
    cerrarDBSQL($con);
    return ($listUsuarios);
}
