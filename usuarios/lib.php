<?php
function obterUsuarios(){
    $con  =   conectarDBSQL();
    $stmt = $con->query("SELECT Id,DNI,Nombre,Apellidos FROM tm_usuarios");
    $listUsuarios = array();
    if($stmt->num_rows > 0){
        while ($u = $stmt->fetch_array(MYSQLI_ASSOC)){
            $listUsuarios[] = array(
                'Id'         =>  $u['Id'],
                'DNI'        =>  $u['DNI'],
                'Nombre'     =>  $u['Nombre'],
                'Apellidos'  =>  $u['Apellidos'],
                'TextoUsuario'=>  $u['Nombre'] .' '.$u['Apellidos'],
            );
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listUsuarios;
}
function comprobarUsuario($Id,$DNI){
    if($DNI){
        $con  =   conectarDBSQL();
        $stmt = $con->prepare("SELECT Id FROM tm_usuarios WHERE DNI=?");
        $stmt->bind_param('s',$DNI);
    }else if($Id){
        $con  =   conectarDBSQL();
        $stmt = $con->prepare("SELECT Id FROM tm_usuarios WHERE Id=?");
        $stmt->bind_param('i',$Id);
    }else{
        return null;
    }
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function newUsuario($DNI,$Nombre,$Apellidos,$NSS,$FecNacimiento,$Sexo){
    $contrasenha=md5(generarPasswordAleatorio());
    $usuario=strtolower($DNI);
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_usuarios(DNI,Nombre,Apellidos,NumSegSocial,FecNacimiento,Sexo,password,username,UsuAlta) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssisissi', $DNI,$Nombre,$Apellidos,$NSS,$FecNacimiento,$Sexo,$contrasenha,$usuario,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function comprobarContacto($Id,$Tipo,$Valor){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_contactos WHERE IdUsuario=? AND Tipo=? AND Valor=?");
    $stmt->bind_param('iis',$Id,$Tipo,$Valor);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function comprobarIdContacto($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_contactos WHERE Id=? ");
    $stmt->bind_param('i',$Id);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}
function saveContacto($IdUsuario,$Tipo,$Valor){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("INSERT INTO tm_contactos(IdUsuario,Tipo,Valor,UsuAlta) VALUES (?,?,?,?)");
    $stmt->bind_param('iisi', $IdUsuario,$Tipo,$Valor,$_SESSION["Id"]);
    $stmt->execute();
    $Id=$stmt->insert_id;
    $stmt->close();
    cerrarDBSQL($con);
    return $Id;
}
function obterContactos($IdUSuario){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,IdUsuario,IdTipo,Tipo,Valor,Nombre,Apellidos FROM vw_contactos WHERE IdUsuario=?");
    $stmt->bind_param('i', $IdUSuario);
    $plan = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows >= 1) {
            while ($p = $resultado->fetch_array(MYSQLI_ASSOC)){
                $plan[] = array(
                    'Id'                =>  $p['Id'],
                    'IdUsuario'         =>  $p['IdUsuario'],
                    'Tipo'              =>  $p['Tipo'],
                    'Valor'             =>  $p['Valor'],
                    'Nombre'            =>  $p['Nombre'],
                    'Apellidos'         =>  $p['Apellidos']
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $plan;
}
function obterUsuario($Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id,DNI,Nombre,Apellidos,FecNacimiento,NumSegSocial,Sexo FROM tm_usuarios WHERE Id=?");
    $stmt->bind_param('i', $Id);
    $usuario = array();
    if($stmt->execute()) {
        $resultado = $stmt->get_result();
        if ($resultado->num_rows ===1) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)) {
                $FecNacimiento = new DateTimeImmutable($u['FecNacimiento']);
                $usuario[] = array(
                    'Id'        => $u['Id'],
                    'DNI'       => $u['DNI'],
                    'Nombre'    => $u['Nombre'],
                    'Apellidos' => $u['Apellidos'],
                    'TextoUsuario' => $u['Nombre'] . ' ' . $u['Apellidos'],
                    'NumSegSocial' => $u['NumSegSocial'],
                    'FecNacimientoInput'    =>  ($u['FecNacimiento']) ? $FecNacimiento->format('Y-m-d') : null,
                    'FecNacimiento'         =>  ($u['FecNacimiento']) ? $FecNacimiento->format('d/m/Y') : null,
                    'listSexo'  => obterSexoSelect($u['Sexo']),
                );
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $usuario;
}
/*FUNCIÓN QUE LISTA OS Tipos de Sexo*/
function obterSexoSelect($s){
    $sexo        = array();
    $niveles[]      = array(
        'Id'        =>  null,
        'Texto'     =>  '',
        'Selected'  =>  (null==$s) ? 'selected' : ''
    );
    $sexo[]      = array(
        'Id'        =>  1,
        'Texto'     =>  'Hombre',
        'Selected'  =>  (1==$s) ? 'selected' : ''
    );
    $sexo[]      = array(
        'Id'        =>  2,
        'Texto'     =>  'Mujer',
        'Selected'  =>  (2==$s) ? 'selected' : ''
    );
    return $sexo;
}
function saveUsuario($DNI,$Nombre,$Apellidos,$NSS,$FecNacimiento,$Sexo,$Id){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("UPDATE tm_usuarios SET DNI=?, Nombre=?,Apellidos=?,NumSegSocial=?,FecNacimiento=?,Sexo=?,UsuModif=?,FecModif=curdate() WHERE Id=?");
    $stmt->bind_param('sssisiii', $DNI,$Nombre,$Apellidos,$NSS,$FecNacimiento,$Sexo,$_SESSION["Id"],$Id);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function delContacto($IdContacto){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("DELETE FROM tm_contactos WHERE Id=?");
    $stmt->bind_param('i', $IdContacto);
    $stmt->execute();
    $Filas=$stmt->affected_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Filas===1);
}
function comprobarPermisoAcceso($IdDepartamento,$IdUsuario){
    require_once($_SESSION['root'].'config/bd/bdMariaBD.php');
    if(!comprobarUsuarioDepartamento($IdDepartamento,$IdUsuario)){
        header('Location: http://localhost/ProyectoIESWIRTZ/index.php?log=1');
    }
}