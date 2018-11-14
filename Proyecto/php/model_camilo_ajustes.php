<?php
function registrar_marca($nombreM, $descripcionMarca){
    $conexion = connect2();
    $query= "INSERT INTO Marca_de_dispositivos (Nombre, Descripcion)
    VALUES ('".$nombreM."', '".$descripcionMarca."')";
    
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;     
    } else {
        $resultado = FALSE;
    }
    disconnect2($conexion);
    return $resultado;
}

function lista_servicios_manuales(){
    $conexion = connect2();
    $query= "SELECT Id_trabajo, Nombre FROM Catalogo_de_servicios WHERE Visible = 1";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Id_trabajo'] [$i] = $row['Id_trabajo'];
        $i++;
    }
    mysqli_free_result($results);
    $select = "<input value='".$i."' name = 'no_servicios' hidden>";
    $columnas = ceil($i / 3);
    $s = 0;
    $no_ser = 0;
    for($c=0; $c < 3; $c++){
        $select .='<div class="col-lg-4 col-md-12 col-sm-12">'; 
        if ($c == 2 && fmod($i,3) != 0){
            for ($s = $s;$s < $columnas + ($c * $columnas) - ($columnas + ($c * $columnas) - $i); $s++){
                $select .= '<div class="form-check col-form-label">
                <input class="form-check-input form-" type="checkbox" value="'.$resultado['Id_trabajo'][$s].'" name="s'.$no_ser.'" id="'.$resultado['Id_trabajo'][$s].'">
                <label class="form-check-label" for="'.$resultado['Id_trabajo'][$s].'">'.$resultado['Nombre'][$s].'</label>
                </div>';
                $no_ser++;
            } 
        } else {
            for ($s = $s;$s < $columnas + ($c * $columnas); $s++){
                $select .= '<div class="form-check col-form-label">
                <input class="form-check-input form-" type="checkbox" value="'.$resultado['Id_trabajo'][$s].'" name="s'.$no_ser.'" id="'.$resultado['Id_trabajo'][$s].'">
                <label class="form-check-label" for="'.$resultado['Id_trabajo'][$s].'">'.$resultado['Nombre'][$s].'</label>
                </div>';
                $no_ser++;
            } 
        }
        $select .= "</div>";
    }
    disconnect2($conexion);
    return $select;    
}

function registrar_manual($NombreManual, $VersionManual, $DescripcionManual, $Id_trabajo){
    $conexion = connect2();
    $query= "INSERT INTO Manuales (Nombre, Version, Descripcion)
    VALUES ('".$NombreManual."','".$VersionManual."','".$DescripcionManual."');";
    for($i = 0; $i < sizeof($Id_trabajo); $i++){
        $query .= " ";
    }
    disconnect2($conexion);
    return $resultado;
}
?>