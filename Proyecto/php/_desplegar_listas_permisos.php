
<?php 

function lista_permisos(){
    
    $conexion = connect2();
    $query= "SELECT Id_permiso, Nombre FROM Permisos"
    $results = mysql_query($conexion, $query);
    $i = 0;
    $select = "";
    
    while($row = mysqli_fetch_array($results, MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = row ['Nombre'];
        $resultado['Id_permiso'][$i] = row ['Id_permiso'];
        $i++;   
    }
    
    mysqli_free_result($results);
    $columnas = ceil($i / 3);
    $s = 0;
    
    for($c=0; $c<3; $c++){
        $select .='<dic class="col-lg-4 col-md-12 col-sm-12">';
        if ($c == 2 && fmod($i,3) != 0){
            for ($s = $s;$s < $columnas + ($c * $columnas) - ($columnas + ($c * $columnas) - $i); $s++){
                $select .= '<div class="form-check col-form-label">
                <input class="form-check-input form-" type="checkbox" value="" id="'.$resultado['Id_permiso'][$s].'">
                <label class="form-check-label" for="'.$resultado['Id_permiso'][$s].'">'.$resultado['Nombre'][$s].'</label>
                </div>';
            } 
        } else {
            for ($s = $s;$s < $columnas + ($c * $columnas); $s++){
                $select .= '<div class="form-check col-form-label">
                <input class="form-check-input form-" type="checkbox" value="" id="'.$resultado['Id_permiso'][$s].'">
                <label class="form-check-label" for="'.$resultado['Id_permiso'][$s].'">'.$resultado['Nombre'][$s].'</label>
                </div>';
            } 
        }
        
        $select .= "</div>";        
        
    }
    
}
disconnect2($conexion);
return $selesct;    
    
?> 