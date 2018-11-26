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
    
        SELECT CAS2.Nombre, CAS2.Descripcion, CAS2.Id_categoria
        FROM (SELECT CAS.Nombre AS Nombre, CAS.Descripcion AS Descripcion, CAS.Id_categoria AS Id_categoria
              FROM Categoria_de_servicios CAS
              WHERE Visible = 1) CAS2
        WHERE CAS2.Nombre LIKE '%".$nombreCatSerBus."%' OR CAS2.Descripcion LIKE '%".$nombreCatSerBus."%' OR CAS2.Descripcion LIKE '%".$nombreCatSerBus."%'
        ORDER BY CAS2.Nombre";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows['Id_categoria'] [$i] = $row['Id_categoria'];
        $rows['Nombre'] [$i] = $row['Nombre'];
        if(strlen($row['Descripcion'])>50){
            $rows['Descripcion'] [$i] = substr($row['Descripcion'],0,50)."...";
        }else{
        $rows['Descripcion'] [$i] = $row['Descripcion'];
        }
        $i++;
    }
    mysqli_free_result($results);
    if (isset($rows['Id_categoria'] [0]) && $rows['Id_categoria'] [0] != ""){
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
    $link = 'window.location.href="./_modificar_catservicios.php?buscar='.$nombreCatSerBus.'&Id_categoria=';
    $i=0;
    foreach ($rows['Nombre'] as $a){
        $tabla .= '
        <tr class="row_buscar_dispositivo text-center" onclick='.$link.$rows['Id_categoria'][$i].'">
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

function consultar_cat_ser($Id_categoria){
    $conexion = connect();
    $query = "
        SELECT CAS.Nombre AS Nombre, CAS.Descripcion AS Descripcion, CAS.Id_categoria AS Id_categoria
        FROM Categoria_de_servicios CAS  
        WHERE CAS.Id_categoria = ".$Id_categoria."";
    
    
    
    
    //SELECT M.Nombre AS Nombre, M.Descripcion FROM Categoria_de_servicios M WHERE M.Id_categoria = '".$Id_categoria."'";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Descripcion'] [$i] = $row['Descripcion'];
        $resultado['Id_categoria'] [$i] = $row['Id_categoria'];
        $i++;
    }
    mysqli_free_result($results);
    //echo"HLA";
    disconnect($conexion);
    return $resultado;
}

function modificar_categoria($Id_categoria, $NombreCat, $DescripcionCat){
    $conexion = connect();
    $query = "UPDATE Categoria_de_servicios SET Nombre = '".$NombreCat."', Descripcion = '".$DescripcionCat."' WHERE Id_categoria = ".$Id_categoria;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
function eliminar_categoria($Id_categoria){
    $conexion = connect();
    $query = "UPDATE Categoria_de_servicios SET Visible = 0 WHERE Id_categoria = ".$Id_categoria;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
?>
