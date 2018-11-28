<?php
function lista_permisos_asignables(){
    $conexion = connect2();
    $query= "SELECT Id_permiso, Nombre FROM Permisos";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Id_permiso'] [$i] = $row['Id_permiso'];
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
                <input class="form-check-input form-" type="checkbox" value="'.$resultado['Id_permiso'][$s].'" name="s'.$no_ser.'" id="'.$resultado['Id_permiso'][$s].'">
                <label class="form-check-label" for="'.$resultado['Id_permiso'][$s].'">'.$resultado['Nombre'][$s].'</label>
                </div>';
                $no_ser++;
            } 
        } else {
            for ($s = $s;$s < $columnas + ($c * $columnas); $s++){
                $select .= '<div class="form-check col-form-label">
                <input class="form-check-input form-" type="checkbox" value="'.$resultado['Id_permiso'][$s].'" name="s'.$no_ser.'" id="'.$resultado['Id_permiso'][$s].'">
                <label class="form-check-label" for="'.$resultado['Id_permiso'][$s].'">'.$resultado['Nombre'][$s].'</label>
                </div>';
                $no_ser++;
            } 
        }
        $select .= "</div>";
    }
    disconnect2($conexion);
    return $select;    
}

function registrar_rol($NombreRol,$DescripcionRol, $Id_permiso){
    $conexion = connect2();
    $query= "INSERT INTO Roles (Nombre, Descripcion)
    VALUES ('".$NombreRol."','".$DescripcionRol."');";
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;     
    } else {
        $resultado = FALSE;
    }
    $query = "SELECT Id_rol FROM Roles WHERE Nombre = '".$NombreRol."'  AND Descripcion = '".$DescripcionRol."' ORDER BY Id_rol DESC LIMIT 1";
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $Id_rol = $row['Id_rol'];
    }
    
    for($i = 0; $i < sizeof($Id_permiso); $i++){
        $query = "INSERT INTO Roles_Permisos(Id_permiso, Id_rol) VALUES (".$Id_permiso[$i].",".$Id_rol.")";
        if ($conexion->query($query) === TRUE) {
            $resultado = $resultado * TRUE;     
        } else {
            $resultado = $resultado * FALSE;
        }
    }
    disconnect2($conexion);
    return $resultado;
}
?>