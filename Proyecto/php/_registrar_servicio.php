<?php
require_once('globals.php');


//Funcion para crear una conexion con la base de datos
function connect4(){
    $conexion = mysqli_connect($GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"],$GLOBALS["db"]);
    if($conexion == NULL) {
        die("Error al conectarse con la base de datos/ error de conexion". mysqli_connect_errno() . PHP_EOL);
    }

    $conexion->set_charset("utf8");
    return $conexion;
}

function limpiar($x){
    $x = trim($x);
    $x = htmlspecialchars($x);
    $x = stripcslashes($x);
    return $x;
}

function cerrar(){
    session_start();
    session_unset();
    session_destroy();
}

function disconnect4($conexion) {
    mysqli_close($conexion);
}

function obtener_categorias(){
    $conexion = connect4();
    
    $query = ("SELECT Nombre FROM Categoria_de_servicios;");

    $results = mysqli_query($conexion, $query);


    $i = 0;
    $rows = [];
    echo '<option value="">Seleccione una categoria..</option>';
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        echo '<option value="'.$row['Nombre'].'">'.$row['Nombre'].'</option>';
    }
    mysqli_free_result($results);
    disconnect4($conexion);
}

//Funcion para registrar una nueva dependencia

function registrar_servcio($nombreSer, $tiempEdeT, $categoriaSer, $esc){
    $conexion = connect4();

    
//INSERTA UN SERVICIO EN LA TABLA CATALOGO_DE_SERVICOS
    $query="INSERT INTO Catalogo_de_servicios (Nombre,Duracion_estandar_del_trabajo,Descripcion) values('".$nombreSer."','".$tiempEdeT."','".$esc."')";
    $result = mysqli_query($conexion, $query);


    $query="INSERT INTO Categoria_de_servicios(Nombre)
    values('".$categoriaSer."')";
    $result = mysqli_query($conexion, $query);
    
    return $result;
    disconnect4($conexion);
}

?>