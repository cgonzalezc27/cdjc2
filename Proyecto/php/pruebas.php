<?php
require_once("model.php");
    //    $no_ingenieros = 2;
    //$duracion = $_GET['duracion'];
    $conexion = connect();

/*
$id_usr = 42;
$id_rol = 1;
    $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = 1";
    $result = mysqli_query($conexion, $query);
    //$i=1;
//var_dump($results);
    while($row = mysqli_fetch_array($result,MYSQLI_BOTH)){
        //echo $row[$i];
        var_dump($row['Id_permiso']);
        if($row ['Id_permiso'] == 18){
            echo "Si entro";
            $query = "INSERT INTO Ingenieros(Id_usuario) VALUES (".$id_usr.")";
            $result = mysqli_query($conexion, $query);
            //$permisos_usuario_anterior [] = $row ['Id_permiso'];
            //$i++;
        }
            
    }
    mysqli_free_result($result);

*/

$query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = 1";
        $results = mysqli_query($conexion, $query);
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            if($row ['Id_permiso'] == 18){
                
                $query = "INSERT INTO Ingenieros(Id_usuario) VALUES (35)";
                $result = mysqli_query($conexion, $query);
                if($result = TRUE){
                    echo "Si registro";
                }
            }
        }
        mysqli_free_result($results);
    disconnect($conexion);

?>