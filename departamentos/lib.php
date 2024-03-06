<?php
function obterDepartamentos(){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,Departamento FROM tm_departamentos");
    $listDepartamentos = array();
    if($stmt->num_rows > 0){
        while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listDepartamentos[] = array(
                'Id'            =>  $u['Id'],
                'Departamento'  =>  $u['Departamento'],
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listDepartamentos;
}
function searchUsuarios($Nombre,$Apellidos,$DNI,$IdDepartamento,$Activo){
    $sqlSelect  = 'SELECT IdUsuario,Nombre,Apellidos,DNI,Departamento,IdDepartamento,Activo,IdDepUsu';
    $sqlFrom    = ' FROM vw_departamentos_usuarios  ';
    $sqlWhere   = '';
    $sqlOrder   = ' ORDER BY Nombre';
    if($Nombre){
        $sqlWhere   .= ' WHERE Nombre LIKE "%'.$Nombre.'%"';
    }
    if($Apellidos){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE Apellidos LIKE "%'.$Apellidos.'%"' : 'AND Apellidos LIKE %"'.$Apellidos.'"%';
    }
    if($DNI){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE DNI LIKE "%'.$DNI.'%"' : 'AND DNI LIKE "%'.$DNI.'%"';
    }
    if($IdDepartamento){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE IdDepartamento='.$IdDepartamento : 'AND IdDepartamento='.$IdDepartamento;
    }
    if($Activo){
        $sqlWhere   .= empty($sqlWhere) ? ' WHERE Activo='.$Activo : 'AND Activo='.$Activo;
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
                'DNI'           =>  $p['DNI'],
                'IdDepartamento'=>  $p['IdDepartamento'],
                'Departamento'  =>  $p['Departamento'],
                'Activo'        =>  $p['Activo'],
                'IdDepUsu'      =>  $p['IdDepUsu'],
            );
        }
    }
    cerrarDBSQL($con);
    return ($listUsuarios);
}
function comprobarDepartamento($IdDepartamento){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_departamentos WHERE Id=?");
    $stmt->bind_param('i',$IdDepartamento);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function comprobarUsuarioDepartamento($IdDepartamento,$IdUsuario){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_departamentos_usuarios WHERE IdUsuario=? AND (IdDepartamento=? OR IdDepartamento=2)");
    $stmt->bind_param('ii',$IdUsuario,$IdDepartamento);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function saveUsuarioDepartamento($IdUsuario,$IdDepartamento){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tr_departamentos_usuarios(IdUsuario,IdDepartamento,UsuAlta) VALUES (?,?,?)");
    $stmt->bind_param('iii', $IdUsuario,$IdDepartamento,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return is_int($Id);
}
function activarUsuario($IdUsuario,$Activo){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_usuarios SET Activo=?, UsuModif=?, FecModif=CURDATE()  WHERE Id=?");
    $stmt->bind_param('iii', $Activo,$_SESSION['Id'],$IdUsuario);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function delUsuarioDepartamento($IdDepUsu){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tr_departamentos_usuarios WHERE Id=?");
    $stmt->bind_param('i', $IdDepUsu);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
