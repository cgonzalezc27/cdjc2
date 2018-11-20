<?php
require_once('globals.php');


//OBTIENE LAS MARCAS DE DISPOSITIVO
function obtener_clases(){
    $conexion = connect3();

    $query = ("SELECT Nombre FROM Clases_de_dispositivos");

    $results = mysqli_query($conexion, $query);


    $i = 1;
    $rows = [];
    echo '<option value="">Seleccione una Clase de Dispositivo...</option>';
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        echo '<option value="'.$row['Nombre'].'">'.$row['Nombre'].'</option>';
        $i++;
    }
    mysqli_free_result($results);
    disconnect3($conexion);
}





//Funcion para registrar un nuevo modelo de dispositivo

function registrar_tipoDispositivo($nombreTipoDis, $claseTipoDis, $comentarioTipoDis){
    $conexion = connect3();

    $query= "SELECT Id_clase_dispositivo FROM Clases_de_dispositivos WHERE Nombre = '".$claseTipoDis."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_claseDisSelect = $row["Id_clase_dispositivo"];





//INSERTA UN MODELO EN LA TABLA MODELO_DE_DISPOSITIVOS
    $query="INSERT INTO Tipo_de_dispositivos (Id_clase_dispositivo, Nombre, Descripcion) values('".$id_claseDisSelect."','".$nombreTipoDis."', '".$comentarioTipoDis."')";
    $result = mysqli_query($conexion, $query);

    return $result;
    disconnect3($conexion);
}

/*if(isset($_POST['submit'])){
    registrar_dependencia();
}
   */
?>
