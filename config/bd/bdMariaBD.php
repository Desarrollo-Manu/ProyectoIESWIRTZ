<?php
function conectarDBSQL(){
    $mysqli = new mysqli("bbdd-manu3eeb5daa-desarrollomanucastro-7c74.a.aivencloud.com", "avnadmin", "AVNS_85lbfwLJ3AdtSKG-y0v", "defaultdb", 13205);

    if ($mysqli->connect_errno) {
        die(print_r( "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error,true));
    }
    return $mysqli;
}
function cerrarDBSQL($conn){
    mysqli_close($conn);
}
/*FUNCION QUE COMPROBA SE O USUARIO QUE INTENTA LOGUEARSE EO CORRECTO*/
function comprobarUserPass($Username,$Password){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tm_usuarios WHERE username LIKE ? AND password LIKE ? AND Activo=1");
    $stmt->bind_param('ss', $Username,$Password);
    $IdUser=false;
    if($stmt->execute()){
        $resultado=$stmt->get_result();$IdUser=null;
        if($resultado->num_rows === 1) {
            while ($u = $resultado->fetch_array(MYSQLI_ASSOC)){
                $IdUser=$u['Id'];
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $IdUser;
}
function obterDepartamentosUsuario($IdUsuario){
    if(esAdmin($IdUsuario)){
        $con  =   conectarDBSQL();
        $stmt = $con->query("SELECT Id,Departamento,Alias,Titulo FROM tm_departamentos");
        $listDepartamentos = array();
        if($stmt->num_rows > 0){
            while ($d = $stmt->fetch_array(MYSQLI_ASSOC)){
                $listDepartamentos[] = array(
                    'Id'            =>  $d['Id'],
                    'Departamento'  =>  $d['Departamento'],
                    'Alias'         =>  $d['Alias'],
                    'Titulo'        =>  $d['Titulo']
                );
            }
        }
        $stmt->close();
        cerrarDBSQL($con);
        return $listDepartamentos;
    }else{
        $con  =   conectarDBSQL();
        $stmt = $con->prepare("SELECT d.Id,d.Departamento,d.Alias,d.Titulo FROM tm_departamentos AS d,tr_departamentos_usuarios AS du WHERE du.IdDepartamento=d.Id AND IdUsuario=?");
        $stmt->bind_param('i', $IdUsuario);
        $listDepartamentos = array();
        if($stmt->execute()) {
            $resultado = $stmt->get_result();
            if ($resultado->num_rows >= 1) {
                while ($d = $resultado->fetch_array(MYSQLI_ASSOC)){
                    $listDepartamentos[] = array(
                        'Id'            =>  $d['Id'],
                        'Departamento'  =>  $d['Departamento'],
                        'Alias'         =>  $d['Alias'],
                        'Titulo'        =>  $d['Titulo']
                    );
                }
            }
        }
    }
    $stmt->close();
    cerrarDBSQL($con);
    return $listDepartamentos;
}
function esAdmin($IdUsuario){
    $con  =   conectarDBSQL();
    $stmt = $con->prepare("SELECT Id FROM tr_departamentos_usuarios WHERE IdUsuario=? AND IdDepartamento=2");
    $stmt->bind_param('i', $IdUsuario);
    $stmt->execute();
    $Rows=$stmt->get_result()->num_rows;
    $stmt->close();
    cerrarDBSQL($con);
    return ($Rows === 1);
}