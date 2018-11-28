<?php

require_once('globals.php');


//Funcion para registrar un servicio

function registrar_servicio($nombreServicio, $descripcionSer, $testandarser, $catSerSelect){
    $conexion = connect2();

    //INSERTA UNA CATEGORIA EN CATEGORIA DE SERVICIOS
    $query="INSERT INTO Catalogo_de_servicios(Nombre,Descripcion,Duracion_estandar_del_trabajo,Id_categoria) values('".$nombreServicio."','".$descripcionSer."','".$testandarser."', '".$catSerSelect."')";

    $result = mysqli_query($conexion, $query);
    
    /*$query= "SELECT Id_categoria FROM Categoria_de_servicios WHERE Nombre = '".$catSerSelect."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_categoria = $row["Id_categoria"];*/

    return $result;
    disconnect2($conexion);
}

function obtener_cat_servicio(){
    $conexion = connect2();

    $query = ("SELECT Nombre, Id_categoria FROM Categoria_de_servicios ORDER BY Nombre;");

    $results = mysqli_query($conexion, $query);

    $i = 0;
    
    echo '<option value="">Seleccione una categoria...</option>';
    while($row = mysqli_fetch_array($results, MYSQLI_BOTH)){
        echo '<option value="'.$row['Id_categoria'].'">'.$row['Nombre'].'</option>';
    }
    mysqli_free_result($results);
    disconnect2($conexion);
    /*
   while($row = mysqli_fetch_array($results, MYSQLI_BOTH)){

        $rows['Id_categoria'][$i]= $row['Id_categoria'][$i];

        $rows['Nombre'][$i] = $row['Nombre'][$i];
        
        $i++;
    }
    */


}

?>