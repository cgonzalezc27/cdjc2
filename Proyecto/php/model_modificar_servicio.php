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



function buscar_cat_ser($nombreCatSerBus){
    $conexion = connect();
    $query = "
    
        SELECT CAS2.Nombre, CAS2.Descripcion, CAS2.Id_trabajo
        FROM (SELECT CAS.Nombre AS Nombre, CAS.Descripcion AS Descripcion, CAS.Id_trabajo AS Id_trabajo
              FROM Catalogo_de_servicios CAS
              WHERE Visible = 1) CAS2
        WHERE CAS2.Nombre LIKE '%".$nombreCatSerBus."%' OR CAS2.Descripcion LIKE '%".$nombreCatSerBus."%' OR CAS2.Descripcion LIKE '%".$nombreCatSerBus."%'
        ORDER BY CAS2.Nombre";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows['Id_trabajo'] [$i] = $row['Id_trabajo'];
        $rows['Nombre'] [$i] = $row['Nombre'];
        if(strlen($row['Descripcion'])>50){
            $rows['Descripcion'] [$i] = substr($row['Descripcion'],0,50)."...";
        }else{
        $rows['Descripcion'] [$i] = $row['Descripcion'];
        }
        $i++;
    }
    mysqli_free_result($results);
    if (isset($rows['Id_trabajo'] [0]) && $rows['Id_trabajo'] [0] != ""){
    $tabla = '
        <div class="table-responsive col-12">
        <table class="table table-hover table-responsive-xl">
            <thead>
                <tr class="text-center">
                    <th>Nombre</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>';
    $link = 'window.location.href="./_modificar_servicios.php?buscar='.$nombreCatSerBus.'&Id_trabajo=';
    $i=0;
    foreach ($rows['Nombre'] as $a){
        $tabla .= '
        <tr class="row_buscar_dispositivo text-center" onclick='.$link.$rows['Id_trabajo'][$i].'">
            <td>'.$rows['Nombre'][$i].'</td>
            <td>'.$rows['Descripcion'][$i].'</td>
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

function consultar_cat_ser($Id_trabajo){
    $conexion = connect();
    $query = "
        SELECT CAS.Nombre AS Nombre, CAS.Descripcion AS Descripcion, CAS.Id_trabajo AS Id_trabajo
        FROM Catalogo_de_servicios CAS  
        WHERE CAS.Id_trabajo = ".$Id_trabajo." ORDER BY Nombre";
    
    
    
    
    //SELECT M.Nombre AS Nombre, M.Descripcion FROM Catalogo_de_servicios M WHERE M.Id_trabajo = '".$Id_trabajo."'";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Descripcion'] [$i] = $row['Descripcion'];
        $resultado['Id_trabajo'] [$i] = $row['Id_trabajo'];
        $i++;
    }
    mysqli_free_result($results);
    //echo"HLA";
    disconnect($conexion);
    return $resultado;
}

function modificar_categoria($Id_trabajo, $NombreCat, $DescripcionCat){
    $conexion = connect();
    $query = "UPDATE Catalogo_de_servicios SET Nombre = '".$NombreCat."', Descripcion = '".$DescripcionCat."' WHERE Id_trabajo = ".$Id_trabajo;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
function eliminar_categoria($Id_trabajo){
    $conexion = connect();
    $query = "UPDATE Catalogo_de_servicios SET Visible = 0 WHERE Id_trabajo = ".$Id_trabajo;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
?>
