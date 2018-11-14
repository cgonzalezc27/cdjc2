<?php
require_once('globals.php');


//Funcion para crear una conexion con la base de datos
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


//OBTIENE LAS MARCAS DE DISPOSITIVO
function obtener_marcas(){
    $conexion = connect3();
    
    $query = ("SELECT Nombre FROM Marca_de_dispositivos;");

    $results = mysqli_query($conexion, $query);


    $i = 0;
    $rows = [];
    echo '<option value="">Seleccione una Marca...</option>';
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        echo '<option value="'.$row['Nombre'].'">'.$row['Nombre'].'</option>';
    }
    mysqli_free_result($results);
    disconnect3($conexion);
}





//Funcion para registrar un nuevo modelo de dispositivo

function registrar_modeloDispositivo($nombreModDis, $marcaDisSelect, $descripcionModDis){
    $conexion = connect3();
    
    $query= "SELECT Id_marca_dispositivo FROM Marca_de_dispositivos WHERE Nombre = '".$marcaDisSelect."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_marcaDisSelect = $row["Id_marca_dispositivo"];

    
    
    
    
//INSERTA UN MODELO EN LA TABLA MODELO_DE_DISPOSITIVOS
    $query="INSERT INTO Modelos_de_dispositivos (Nombre,Descripcion,Id_marca_dispositivo) values('".$nombreModDis."','".$descripcionModDis."', '".$id_marcaDisSelect."')";
    $result = mysqli_query($conexion, $query);
    
    return $result;
    disconnect3($conexion);
}

/*if(isset($_POST['submit'])){
    registrar_dependencia();
}
   */ 
?>