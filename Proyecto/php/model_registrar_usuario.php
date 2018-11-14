<?php
require_once('globals.php');


//Funcion para crear una conexion con la base de datos
/*
function connect3(){
    $conexion = mysqli_connect($GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"],$GLOBALS["db"]);
    if($conexion == NULL) {
        die("Error al conectarse con la base de datos/ error de conexion". mysqli_connect_errno() . PHP_EOL);
    }
    $conexion->set_charset("utf8");
    return $conexion;
}

function clean2($x){
    $x = trim($x);
    $x = htmlspecialchars($x);
    $x = stripcslashes($x);
    return $x;
}

function cerrar_sesion2(){
    session_start();
    session_unset();
    session_destroy();
}

function disconnect3($conexion) {
    mysqli_close($conexion);
}

*/
//OBTIENE LAS MARCAS DE DISPOSITIVO
function obtener_roles(){
    $conexion = connect3();
    
    $query = ("SELECT Nombre FROM Roles;");

    $results = mysqli_query($conexion, $query);


    $i = 0;
    $rows = [];
    echo '<option value="">Seleccione un Rol...</option>';
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        echo '<option value="'.$row['Nombre'].'">'.$row['Nombre'].'</option>';
    }
    mysqli_free_result($results);
    disconnect3($conexion);
}



function obten_id(){
    $conexion = connect3();
    
    $query = ("SELECT Id_usuario FROM Usuarios ORDER BY Id_usuario DESC LIMIT 1;");
    $result = mysqli_query($conexion, $query);
    $rows = [];
    while($row = mysqli_fetch_array($result,MYSQLI_BOTH))
        {
            $rows = $row;
        }
    $id_usuario = $rows['Id_usuario'];
    mysqli_free_result($result);
    /*
    $row = mysqli_fetch_array($result);
    $id_usuario = $row["Id_usuario"];
    */
    return $id_usuario;
    disconnect3($conexion);
}



//Funcion para registrar un nuevo modelo de dispositivo

function registrar_usuario($nombre_usr, $apellido1_usr, $apellido2_usr, $rfc_usr, $mote_usr, $pass_usr, $calle_usr, $num1_usr, $num2_usr, $ciudad_usr, $estado_usr, $cp_usr, $mail_usr, $rol_usr, $foto_usr){
    $conexion = connect3();
    
 /*   $query= "SELECT Id_marca_dispositivo FROM Marca_de_dispositivos WHERE Nombre = '".$marcaDisSelect."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_marcaDisSelect = $row["Id_marca_dispositivo"];

    
    */
    $id_usr = obten_id();
    $id_usr++;
    
    
//INSERTA UN USUARIO EN LA TABLA Usuarios
    $query="INSERT INTO Usuarios(Nombre_de_usuario, Contraseña, Nombre, Apellido1, Apellido2, RFC, Estado, Ciudad, Calle, Numero_exterior, Numero_interior, CP, Foto) VALUES ('".$nombre_usr."', '".$pass_usr."', '".$nombre_usr."', '".$apellido1_usr."', '".$apellido2_usr."', '".$rfc_usr."', '".$estado_usr."', '".$ciudad_usr."', '".$calle_usr."', '".$num1_usr."', '".$num2_usr."', '".$cp_usr."', '".$foto_usr."')";
    $result = mysqli_query($conexion, $query);
    
//INSERTA EL USUARIO ACORDE A SU ROL
    $query= "SELECT Id_rol FROM Roles WHERE Nombre = '".$rol_usr."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_rol = $row["Id_rol"];
    
    $query="INSERT INTO Usuarios_Roles(Id_usuario, Id_rol) VALUES (".$id_usr.", ".$id_rol.")";
    $result = mysqli_query($conexion, $query);
    //return $result;
    
    
    
//COSA DE CAMILO    
    /*$query = "SELECT Id_permiso FROM Permisos";
    $results = mysqli_query($conexion, $query);
    $i=1;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $todos_los_permisos [$i]= $row ['Id_permiso'];
        $i++;
    }*/
    //mysqli_free_result($result);
    $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$id_rol;
        $results = mysqli_query($conexion, $query);
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            if($row ['Id_permiso'] == 18){
                
                $query = "INSERT INTO Ingenieros(Id_usuario) VALUES (".$id_usr.")";
                $result = mysqli_query($conexion, $query);
                //if($result = TRUE){
                    //echo "Si registro";
                //}
            }
        }
        mysqli_free_result($results);

    
    
    
    
    disconnect3($conexion);
}

/*if(isset($_POST['submit'])){
    registrar_dependencia();
}
   */ 
?>