<?php
require_once('globals.php');


//Funcion para registrar una nueva categoria de servicio

function registrar_categoria_servicio($nombreCatServicio, $descripcionCatSer){
    $conexion = connect2();


    
//INSERTA UNA CATEGORIA EN CATEGORIA DE SERVICIOS
    $query="INSERT INTO Categoria_de_servicios (Nombre,Descripcion) values('".$nombreCatServicio."','".$descripcionCatSer."')";
    $result = mysqli_query($conexion, $query);
    
    return $result;
    disconnect2($conexion);
}


?>
