<?php
require_once('globals.php');

function connect(){
    $conexion = mysqli_connect($GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"],$GLOBALS["db"]);
    if($conexion == NULL) {
        die("Error al conectarse con la base de datos/ error de conexion". mysqli_connect_errno() . PHP_EOL);
    }
    $conexion->set_charset("utf8");
    return $conexion;
}

function clean($x){
    $x = trim($x);
    $x = htmlspecialchars($x);
    $x = stripcslashes($x);
    return $x;
}

function cerrar_sesion(){
    session_start();
    session_unset();
    session_destroy();
}

function disconnect($conexion) {
    mysqli_close($conexion);
}



function buscar_modelo($nombreModeloBus){
    $conexion = connect();
    $query = "
        SELECT M.Nombre AS Nombre, M.Descripcion, M.Id_modelo_dispositivo, Mar.Nombre AS NombreMar 
        FROM Modelos_de_dispositivos M 
        INNER JOIN Marca_de_dispositivos Mar ON M.Id_marca_dispositivo = Mar.Id_marca_dispositivo AND M.Visible = 1
        WHERE M.Nombre LIKE '%".$nombreModeloBus."%' OR M.Descripcion LIKE '%".$nombreModeloBus."%' OR Mar.Nombre LIKE '%".$nombreModeloBus."%'
        ORDER BY M.Nombre";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows['Id_modelo_dispositivo'] [$i] = $row['Id_modelo_dispositivo'];
        $rows['Nombre'] [$i] = $row['Nombre'];
        $rows['NombreMar'] [$i] = $row['NombreMar'];
        if(strlen($row['Descripcion'])>50){
            $rows['Descripcion'] [$i] = substr($row['Descripcion'],0,50)."...";
        }else{
        $rows['Descripcion'] [$i] = $row['Descripcion'];
        }
        $i++;
    }
    mysqli_free_result($results);
    if (isset($rows['Id_modelo_dispositivo'] [0]) && $rows['Id_modelo_dispositivo'] [0] != ""){
    $tabla = '
        <div class="table-responsive col-12">
        <table class="table table-hover table-responsive-xl">
            <thead>
                <tr class="text-center">
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                </tr>
            </thead>
            <tbody>';
    $link = 'window.location.href="./_modificar_moddispositivos.php?buscar='.$nombreModeloBus.'&Id_modelo_dispositivo=';
    $i=0;
    foreach ($rows['Nombre'] as $a){
        $tabla .= '
        <tr class="row_buscar_dispositivo text-center" onclick='.$link.$rows['Id_modelo_dispositivo'][$i].'">
            <td>'.$rows['Nombre'][$i].'</td>
            <td>'.$rows['Descripcion'][$i].'</td>
            <td>'.$rows['NombreMar'][$i].'</td>
        </tr>';
        $i++;
    }
    $tabla .= '</tbody></table></div>';
    } else {
        $tabla = '<div class = "error"> *Sin resultados en la base de datos :c';
    }
    disconnect($conexion);
    return $tabla;
}

function consultar_modelo($Id_modelo_dispositivo){
    $conexion = connect();
    $query = "
        SELECT M.Nombre AS Nombre, M.Descripcion, M.Id_marca_dispositivo, Mar.Nombre AS NombreMar 
        FROM Modelos_de_dispositivos M 
        INNER JOIN Marca_de_dispositivos Mar ON M.Id_marca_dispositivo = Mar.Id_marca_dispositivo 
        WHERE M.Id_modelo_dispositivo = ".$Id_modelo_dispositivo."";
    
    
    
    
    //SELECT M.Nombre AS Nombre, M.Descripcion FROM Modelos_de_dispositivos M WHERE M.Id_modelo_dispositivo = '".$Id_modelo_dispositivo."'";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Descripcion'] [$i] = $row['Descripcion'];
        $resultado['Id_marca_dispositivo'] [$i] = $row['Id_marca_dispositivo'];
        $resultado['NombreMar'] [$i] = $row['NombreMar'];
        $i++;
    }
    mysqli_free_result($results);

    disconnect($conexion);
    return $resultado;
}

function modificar_modelo($Id_modelo_dispositivo, $NombreM, $DescripcionM, $NombreMarSel){
    $conexion = connect();
    $query = "UPDATE Modelos_de_dispositivos SET Nombre = '".$NombreM."', Id_marca_dispositivo = '".$NombreMarSel."', Descripcion = '".$DescripcionM."' WHERE Id_modelo_dispositivo = ".$Id_modelo_dispositivo;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
function eliminar_modelo($Id_modelo_dispositivo){
    $conexion = connect();
    $query = "UPDATE Modelos_de_dispositivos SET Visible = 0 WHERE Id_modelo_dispositivo = ".$Id_modelo_dispositivo;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}

function obtener_marcas($ignorar){
    $conexion = connect();
    
    $query = ("SELECT Nombre, Id_marca_dispositivo FROM Marca_de_dispositivos WHERE Nombre != '".$ignorar."';");

    $results = mysqli_query($conexion, $query);


    $i = 0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        echo '<option value="'.$row['Id_marca_dispositivo'].'">'.$row['Nombre'].'</option>';
        $i++;
    }
    mysqli_free_result($results);
    disconnect($conexion);
}
?>
