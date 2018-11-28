<?php
require_once("./model.php");
if(isset($_GET['tipo_destino'])){
    $destino = $_GET['tipo_destino'];
    $conexion = connect();
    if($destino == "Dependencia"){
        $query="SELECT DE.Id_destino, DE.Razon_social FROM Dependencias D, Destino DE WHERE D.Id_destino = DE.Id_destino";
        $results = mysqli_query($conexion, $query);
        $rows = '
                <div class="col-lg-6 col-md-9 col-sm-11">
                    <label for="destino_">Dependencias:</label>
                    <select type="list" class="form-control" id="destino" name="destino">';
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $rows .= '<option>'.$row["Razon_social"].'</option>';
        }
        $rows .= '</select></div>';
        mysqli_free_result($results);   
    } else if ($destino == "Mesa"){
        $query="SELECT DE.Id_destino, DE.Razon_social FROM Mesas M, Destino DE WHERE M.Id_destino = DE.Id_destino";
        $results = mysqli_query($conexion, $query);
        $rows = '
                <div class="col-lg-6 col-md-9 col-sm-11">
                    <label for="destino_">Mesas:</label>
                    <select type="list" class="form-control" id="destino" name="destino">';
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $rows .= '<option>'.$row["Razon_social"].'</option>';
        }
        $rows .= '</select></div>';
        mysqli_free_result($results);   
    } else if ($destino == "Seleccione uno"){
        $rows = "";
    }
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}
if(isset($_GET['mesa_modificar_ticket'])){
    $mesa = $_GET['mesa_modificar_ticket'];
    $conexion = connect();
    $query = "SELECT DE.Razon_social FROM Destino DE, Dependencias D, NombresMesas M WHERE M.Id_mesa = D.Id_mesa AND D.Id_destino = DE.Id_destino AND M.NombreM = '".$mesa."'";
    $results = mysqli_query($conexion, $query);
    $rows = "<select class='form-control' id='dependencia' name='dependencia'>";
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows .= "<option>".$row['Razon_social']."</option>";
    }
    mysqli_free_result($results);
    $rows .= "</select>";
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}

if(isset($_GET['no_ingenieros'])){
    $no_ingenieros = $_GET['no_ingenieros'];
    //$duracion = $_GET['duracion'];
    $conexion = connect();
    $query="SELECT T.Id_ticket, U.Nombre AS 'NombreI', U.Apellido1 AS 'Apellido1I', U.Apellido2 AS 'Apellido2I',  I.Id_ingeniero

    FROM Tickets T, Ingenieros I, Ingenieros_Tickets IT, Usuarios U

    WHERE T.Id_ticket = IT.Id_ticket AND IT.Id_ingeniero = I.Id_ingeniero AND I.Id_usuario = U.Id_usuario AND

    T.Id_ticket = '".$_GET['id']."' ORDER BY U.Nombre ASC;";
    $results = mysqli_query($conexion, $query);
    $ticket_actual = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $ticket_actual [$i]['Id_ingeniero'] = $row['Id_ingeniero'];
        $ticket_actual [$i]['NombreI'] = $row['NombreI'];
        $ticket_actual [$i]['Apellido1I'] = $row['Apellido1I'];
        $ticket_actual [$i]['Apellido2I'] = $row['Apellido2I'];
        $i++;
    }
    $Numero_ingenieros_actual = $i;
    if($no_ingenieros < $Numero_ingenieros_actual){
        $ingenieros_a_eliminar = $Numero_ingenieros_actual - $no_ingenieros;

        for($x = 1; $x <= $ingenieros_a_eliminar; $x++){
            unset($ticket_actual [$Numero_ingenieros_actual - $x]);
            unset($ticket_actual [$Numero_ingenieros_actual - $x]);
            unset($ticket_actual [$Numero_ingenieros_actual - $x]);
        }
    } else{
        $ingenieros_a_eliminar = 0;
    }

    for ($a = 0; $a <= $Numero_ingenieros_actual - $ingenieros_a_eliminar - 1; $a++){
        $Id_ingenieros_ticket_actual[$a] = $ticket_actual [$a]['Id_ingeniero'];
    }


    $query = "SELECT I.Id_ingeniero, U.Nombre, U.Apellido1, U.Apellido2 FROM Usuarios U, Ingenieros I WHERE I.Id_usuario = U.Id_usuario AND I.Visible = 1 GROUP BY I.Id_ingeniero ORDER BY U.Nombre ASC";
    $results = mysqli_query($conexion, $query);
    $ingenieros = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $ingenieros_todos['Nombre'][$i] = $row['Nombre'].' '.$row['Apellido1'].' '.$row['Apellido2'];
        $ingenieros_todos['Nombre1'][$i] = $row['Nombre'];
        $ingenieros_todos['Apellido1'][$i] = $row['Apellido1'];
        $ingenieros_todos['Apellido2'][$i] = $row['Apellido2'];
        $ingenieros_todos['Id_ingeniero'][$i] = $row['Id_ingeniero'];
        $i++;
    }
    mysqli_free_result($results);


    $ingenieros = "";

    for($x = $Numero_ingenieros_actual;$x < (int)$no_ingenieros; $x++) {
        $b=0;
        $i=0;
        foreach($ingenieros_todos ['Id_ingeniero'] as $a){
            if (in_array($a,$Id_ingenieros_ticket_actual) == FALSE && $b==0){  
                array_push($Id_ingenieros_ticket_actual, $a);
                $b=1;
                $ticket_actual [$x]['Id_ingeniero'] = $a;
                $ticket_actual [$x]['NombreI'] = $ingenieros_todos['Nombre1'][$i];
                $ticket_actual [$x]['Apellido1I'] = $ingenieros_todos['Apellido1'][$i];
                $ticket_actual [$x]['Apellido2I'] = $ingenieros_todos['Apellido2'][$i];

            }
            $i++;
        }
    }

    // no sirve lo siguiente pero lo dejo por si acaso.
    /*for($x = 1; $x <= $no_ingenieros; $x++){
        $y = 0;
        $resto_ingenieros [$x] = "";
        $ingenieros_en_resto = [];  

        foreach ($ingenieros_todos ['Id_ingeniero'] as $a){   
            if (in_array($a,$Id_ingenieros_ticket_actual) == FALSE){
                $id_resto_ingenieros [$x][$y] = $a;
                $nombre_resto_ingenieros [$x][$y] = $ingenieros_todos ['Nombre'][$y];
                array_push($ingenieros_en_resto, $a);
            }
            $y++;       
        }
       array_push($Id_ingenieros_ticket_actual, $ingenieros_en_resto[0]); 
    }*/



    for($x = 1; $x <= $no_ingenieros; $x++){
        $y = 0;
        $resto_ingenieros = "";
        //$ingenieros_en_resto = [];
        foreach ($ingenieros_todos ['Id_ingeniero'] as $a){   
            if (in_array($a,$Id_ingenieros_ticket_actual) == FALSE){
                $resto_ingenieros .= '<option id="'.$a.'">'.$ingenieros_todos ['Nombre'][$y].'</option>';
                //array_push($ingenieros_en_resto, $a);
            }
            $y++;       
        }
        if(isset($ticket_actual [$x - 1]['Id_ingeniero'])){
            $ingenieros .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="ingenieros">
                <label for="inge'.$x.'" class="form-group">Ingeniero asignado # '.$x.': </label>
                <select class="form-control" id="inge'.$x.'" name="inge'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_ingeniero('.$x.')">
                    <option id="'.$ticket_actual [$x - 1]['Id_ingeniero'].'">'.$ticket_actual [$x - 1]['NombreI'].' '.$ticket_actual [$x - 1]['Apellido1I'].' '.$ticket_actual [$x - 1]['Apellido2I'].'</option>'.$resto_ingenieros.'

                    </select>
            </div>
          ';
        } else {
            $ingenieros .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="ingenieros">
                <label for="inge'.$x.'" class="form-group">Ingeniero asignado # '.$x.': </label>
                <select class="form-control" id="inge'.$x.'" name="inge'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_ingeniero('.$x.')">
                    '.$resto_ingenieros.'

                    </select>
            </div>
          ';
        }
        //array_push($Id_ingenieros_ticket_actual, $ingenieros_en_resto[0]);
    }
    $ingenieros .= '<input type="text" hidden id="no_listas" value="'.($x - 1).'">';
    disconnect($conexion);
    $seleccion = $ingenieros;
    echo $seleccion;
}

if(isset($_GET['no_servicios'])){
    $no_servicios = $_GET['no_servicios'];
    $id = $_GET['id'];
    //$duracion = $_GET['duracion'];
    $conexion = connect();

    $query="SELECT T.Id_ticket, S.Nombre AS 'NombreS', CS.Nombre AS 'NombreCS', S.Id_trabajo, CS.Id_categoria
    FROM Tickets T, Catalogo_de_servicios S, Catalogo_de_servicios_Tickets ST, Categoria_de_servicios CS
    WHERE T.Id_ticket = ST.Id_ticket AND ST.Id_trabajo = S.Id_trabajo AND S.Id_categoria = CS.Id_categoria AND
    T.Id_ticket = '".$id."'; ORDER BY S.Nombre ASC";
    $results = mysqli_query($conexion, $query);
    $filas = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $servicios_ticket_actual ['NombreCS'] [$i]= $row['NombreCS'];
        $servicios_ticket_actual ['NombreS'][$i] = $row['NombreS'];        
        $servicios_ticket_actual ['Id_trabajo'] [$i]= $row['Id_trabajo'];
        $servicios_ticket_actual ['Id_categoria'][$i] = $row['Id_categoria'];
        $i++;
    }
    mysqli_free_result($results);
    $Numero_servicios_actual = $i;
    if($no_servicios < $Numero_servicios_actual){
        $servicios_a_eliminar = $Numero_servicios_actual - $no_servicios;

        for($x = 1; $x <= $servicios_a_eliminar; $x++){
            unset($servicios_ticket_actual [$Numero_servicios_actual - $x]);
            unset($servicios_ticket_actual [$Numero_servicios_actual - $x]);
            unset($servicios_ticket_actual [$Numero_servicios_actual - $x]);
        }
    } else{
        $servicios_a_eliminar = 0;
    }

    for ($a = 0; $a <= $Numero_servicios_actual - $servicios_a_eliminar - 1; $a++){
        $Id_servicios_ticket_actual[$a] =  $servicios_ticket_actual ['Id_trabajo'] [$a];
        $Id_categoria_ticket_actual[$a] =  $servicios_ticket_actual ['Id_categoria'] [$a];
    }


    $query = "SELECT S.Id_trabajo, S.Nombre as 'NombreS', CS.Id_categoria, CS.Nombre as 'NombreCS' FROM Catalogo_de_servicios S, Categoria_de_servicios CS WHERE S.Id_categoria = CS.Id_categoria AND S.Visible = 1 AND CS.Visible = 1 ORDER BY S.Nombre ASC";
    $results = mysqli_query($conexion, $query);

    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $servicios_todos['NombreS'][$i] = $row['NombreS'];
        $servicios_todos['Id_trabajo'][$i] = $row['Id_trabajo'];        
        $servicios_todos['NombreCS'][$i] = $row['NombreCS'];
        $servicios_todos['Id_categoria'][$i] = $row['Id_categoria'];
        $i++;
    }
    mysqli_free_result($results);


    $servicios = "";

    for($x = $Numero_servicios_actual;$x < (int)$no_servicios; $x++) {
        $b=0;
        $i=0;
        foreach($servicios_todos ['Id_trabajo'] as $a){
            if (in_array($a,$Id_servicios_ticket_actual) == FALSE && $b==0){  
                array_push($Id_servicios_ticket_actual, $a);
                $b=1;
                $servicios_ticket_actual ['Id_trabajo'][$x] = $a;
                $servicios_ticket_actual ['NombreS'][$x] = $servicios_todos['NombreS'][$i];
                $servicios_ticket_actual ['NombreCS'][$x] = $servicios_todos['NombreCS'][$i];
                $servicios_ticket_actual ['Id_categoria'][$x] = $servicios_todos['Id_categoria'][$i];

            }
            $i++;
        }
    }

    // no sirve lo siguiente pero lo dejo por si acaso.
    /*for($x = 1; $x <= $no_ingenieros; $x++){
        $y = 0;
        $resto_ingenieros [$x] = "";
        $ingenieros_en_resto = [];  

        foreach ($ingenieros_todos ['Id_ingeniero'] as $a){   
            if (in_array($a,$Id_ingenieros_ticket_actual) == FALSE){
                $id_resto_ingenieros [$x][$y] = $a;
                $nombre_resto_ingenieros [$x][$y] = $ingenieros_todos ['Nombre'][$y];
                array_push($ingenieros_en_resto, $a);
            }
            $y++;       
        }
       array_push($Id_ingenieros_ticket_actual, $ingenieros_en_resto[0]); 
    }*/

    $servicios= "";
    $categorias_todos = $servicios_todos ['Id_categoria'];
    $servicios_todos ['Id_categoria'] = array_values( array_filter( array_unique ($servicios_todos ['Id_categoria'])));
    $servicios_todos ['NombreCS'] = array_values( array_filter( array_unique($servicios_todos ['NombreCS'])));

    for($x = 1; $x <= $no_servicios; $x++){
        $y = 0;
        $resto_servicios = "";
        $resto_categorias = "";
        //$ingenieros_en_resto = [];
        foreach ($servicios_todos ['Id_trabajo'] as $a){   
            if (in_array($a,$Id_servicios_ticket_actual) == FALSE && $servicios_ticket_actual['Id_categoria'][$x-1] == $categorias_todos[$y]){
                $resto_servicios .= '<option id="'.$a.'">'.$servicios_todos ['NombreS'][$y].'</option>';
                //array_push($ingenieros_en_resto, $a);
            }
            $y++;       
        }
        $y = 0;

        foreach ($servicios_todos ['Id_categoria'] as $a){   
            if (empty($Id_categoria_ticket_actual[$x-1]) || $a != $Id_categoria_ticket_actual[$x-1]){
                $resto_categorias .= '<option id="'.$a.'">'.$servicios_todos ['NombreCS'][$y].'</option>';
            }
            $y++;    
        }

        if(isset($servicios_ticket_actual ['Id_trabajo'][$x-1])){
            if($x<=$Numero_servicios_actual){


                $servicios .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Categorias">
                <label for="Catservicio'.$x.'" class="form-group">Categoría de servicio # '.$x.': </label>
                <select class="form-control" id="Catservicio'.$x.'" name="Catservicio'.$x.'" onchange = "servicios_modificar_ticket('.$x.')">
                        <option id="'.$servicios_ticket_actual ['Id_categoria'] [$x-1].'">'.$servicios_ticket_actual ['NombreCS'] [$x-1].'</option>'.$resto_categorias.'
                    </select>

            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Servicios">
                <label for="servicio'.$x.'" class="form-group">Servicio # '.$x.': </label>
                <select class="form-control" id="servicio'.$x.'" name="servicio'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_servicio('.$x.',0)">
                    <option id="'.$servicios_ticket_actual ['Id_trabajo'] [$x-1].'">'.$servicios_ticket_actual ['NombreS'] [$x-1].'</option>'.$resto_servicios.'
                    </select>
            </div>
          ';
            } else{
                $servicios .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Categorias">
                <label for="Catservicio'.$x.'" class="form-group">Categoría de servicio # '.$x.': </label>
                <select class="form-control" id="Catservicio'.$x.'" name="Catservicio'.$x.'" onchange = "servicios_modificar_ticket('.$x.')">
                       '.$resto_categorias.'
                    </select>

            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Servicios">
                <label for="servicio'.$x.'" class="form-group">Servicio # '.$x.': </label>
                <select class="form-control" id="servicio'.$x.'" name="servicio'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_servicio('.$x.',0)">
                    <option id="'.$servicios_ticket_actual ['Id_trabajo'] [$x-1].'">'.$servicios_ticket_actual ['NombreS'] [$x-1].'</option>'.$resto_servicios.'
                    </select>
            </div>
          '; 
            }
        } else {
            $servicios .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Categorias">
                <label for="Catservicio'.$x.'" class="form-group">Categoría de servicio # '.$x.': </label>
                <select class="form-control" id="Catservicio'.$x.'" name="Catservicio'.$x.'" onchange = "servicios_modificar_ticket('.$x.')">
                    '.$resto_categorias.'
            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Servicios">
                <label for="servicio'.$x.'" class="form-group">Servicio # '.$x.': </label>
                <select class="form-control" id="servicio'.$x.'" name="servicio'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_servicio('.$x.',0)">
                    '.$resto_servicios.'

                    </select>
            </div>
          ';
            //array_push($Id_ingenieros_ticket_actual, $ingenieros_en_resto[0]);
        }
    }
    $servicios .= '<input type="text" hidden id="no_listas_servicio" value="'.($x - 1).'">';
    disconnect($conexion);
    $seleccion = $servicios;
    echo $seleccion;
}

if(isset($_GET['listainge'])){
    $conexion = connect();
    $no_listas = $_GET['no_listas'];
    $ingenieros = "";
    for ($x = 1; $x <= $no_listas; $x++){
        $id_ingenieros_seleccionados = [];
        for ($y = 1; $y <= $no_listas; $y++){
            $id_ingenieros_seleccionados [$y] = $_GET['id_ingeniero'.$y];
            $nombre_ingenieros_seleccionados [$y] = $_GET['nombre_ingeniero'.$y];
        }
        $query = "SELECT I.Id_ingeniero, U.Nombre, U.Apellido1, U.Apellido2 FROM Usuarios U, Ingenieros I WHERE I.Id_usuario = U.Id_usuario AND I.Visible = 1 GROUP BY I.Id_ingeniero ORDER BY U.Nombre ASC";
        $results = mysqli_query($conexion, $query);
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $ingenieros_todos['Nombre'][$i] = $row['Nombre'].' '.$row['Apellido1'].' '.$row['Apellido2'];
            $ingenieros_todos['Nombre1'][$i] = $row['Nombre'];
            $ingenieros_todos['Apellido1'][$i] = $row['Apellido1'];
            $ingenieros_todos['Apellido2'][$i] = $row['Apellido2'];
            $ingenieros_todos['Id_ingeniero'][$i] = $row['Id_ingeniero'];
            $i++;
        }
        //var_dump($ingenieros_todos);
        mysqli_free_result($results);
        $resto_ingenieros = "";
        $y=0;
        foreach ($ingenieros_todos['Id_ingeniero'] as $a){
            if(in_array($a,$id_ingenieros_seleccionados) == FALSE){
                $resto_ingenieros .= '<option id="'.$a.'">'.$ingenieros_todos ['Nombre'][$y].'</option>';
            }
            $y++;
        }

        $ingenieros .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="ingenieros">
                <label for="inge'.$x.'" class="form-group">Ingeniero asignado # '.$x.': </label>
                <select class="form-control" id="inge'.$x.'" name="inge'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_ingeniero('.$x.')">
                    <option id="'.$id_ingenieros_seleccionados[$x].'">'.$nombre_ingenieros_seleccionados[$x].'</option>'.$resto_ingenieros.'

                    </select>
            </div>
          ';
    }

    $ingenieros .= '<input type="text" hidden id="no_listas" value="'.($x - 1).'">';
    disconnect($conexion);
    $seleccion = $ingenieros;
    echo $seleccion;
}

if(isset($_GET['listaserv'])){
    $conexion = connect();
    $no_listas = $_GET['no_listas'];
    $servicios = "";
    if(isset($_GET['cambio_categoria']) && $_GET['cambio_categoria'] == 1){
        
        for ($y = 1; $y <= $no_listas; $y++){
            $id_servicios_seleccionados [$y] = $_GET['id_servicio'.$y];
            $nombre_servicios_seleccionados [$y] = $_GET['nombre_servicio'.$y];
            $id_categorias_seleccionadas [$y] = $_GET['id_categoria'.$y];
            $nombre_categorias_seleccionadas [$y] = $_GET['nombre_categoria'.$y];
        }
        
        
        $id_categorias_seleccionadas [$_GET['listaserv']] = $_GET['id_categoria'.$_GET['listaserv']];
        $nombre_categorias_seleccionadas [$_GET['listaserv']] = $_GET['nombre_categoria'.$_GET['listaserv']];
        
        $query = "SELECT S.Id_trabajo, S.Nombre as 'NombreS', CS.Id_categoria, CS.Nombre as 'NombreCS' FROM Catalogo_de_servicios S, Categoria_de_servicios CS WHERE S.Id_categoria = CS.Id_categoria AND S.Visible = 1 AND CS.Visible = 1 ORDER BY S.Nombre ASC";
        $results = mysqli_query($conexion, $query);
    
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $servicios_todos['NombreS'][$i] = $row['NombreS'];
            $servicios_todos['Id_trabajo'][$i] = $row['Id_trabajo'];        
            $servicios_todos['NombreCS'][$i] = $row['NombreCS'];
            $servicios_todos['Id_categoria'][$i] = $row['Id_categoria'];
            $i++;
        }
        mysqli_free_result($results);
        
        $y=0;
        $categorias_todos = $servicios_todos ['Id_categoria'];
        
        foreach ($servicios_todos['Id_trabajo'] as $a){
            if(in_array($a,$id_servicios_seleccionados) == FALSE  && $id_categorias_seleccionadas [$_GET['listaserv']] == $categorias_todos[$y]){
                $servicio_elegido_id[$y] = $a;
                $servicio_elegido_nombre [$y] = $servicios_todos ['NombreS'][$y];
            }
            $y++;
        }
        $servicio_elegido_id = array_values( array_filter( array_unique ($servicio_elegido_id)));
        
        $servicio_elegido_nombre = array_values( array_filter( array_unique($servicio_elegido_nombre)));
        $id_servicios_seleccionados [$_GET['listaserv']] = $servicio_elegido_id[0];
        $nombre_servicios_seleccionados [$_GET['listaserv']] = $servicio_elegido_nombre[0];
    }
    
    
    
    for ($x = 1; $x <= $no_listas; $x++){
        
        
        for ($y = 1; $y <= $no_listas; $y++){
            if(isset($_GET['cambio_categoria']) && $_GET['cambio_categoria'] == 1){
                if ($y != $_GET['listaserv']){
                    $id_servicios_seleccionados [$y] = $_GET['id_servicio'.$y];
                    $nombre_servicios_seleccionados [$y] = $_GET['nombre_servicio'.$y];
                    $id_categorias_seleccionadas [$y] = $_GET['id_categoria'.$y];
                    $nombre_categorias_seleccionadas [$y] = $_GET['nombre_categoria'.$y];
                    
                }
            } else {
                $id_servicios_seleccionados [$y] = $_GET['id_servicio'.$y];
                $nombre_servicios_seleccionados [$y] = $_GET['nombre_servicio'.$y];
                $id_categorias_seleccionadas [$y] = $_GET['id_categoria'.$y];
                $nombre_categorias_seleccionadas [$y] = $_GET['nombre_categoria'.$y];  
            }
        }
        
       
        $query = "SELECT S.Id_trabajo, S.Nombre as 'NombreS', CS.Id_categoria, CS.Nombre as 'NombreCS' FROM Catalogo_de_servicios S, Categoria_de_servicios CS WHERE S.Id_categoria = CS.Id_categoria AND S.Visible = 1 AND CS.Visible = 1 ORDER BY S.Nombre ASC";
        $results = mysqli_query($conexion, $query);
    
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $servicios_todos['NombreS'][$i] = $row['NombreS'];
            $servicios_todos['Id_trabajo'][$i] = $row['Id_trabajo'];        
            $servicios_todos['NombreCS'][$i] = $row['NombreCS'];
            $servicios_todos['Id_categoria'][$i] = $row['Id_categoria'];
            $i++;
        }
        mysqli_free_result($results);
        
        $resto_servicios = "";
        $y=0;
        $categorias_todos = $servicios_todos ['Id_categoria'];
        
        
        foreach ($servicios_todos['Id_trabajo'] as $a){
            if(in_array($a,$id_servicios_seleccionados) == FALSE  && $id_categorias_seleccionadas[$x] == $categorias_todos[$y]){
                $resto_servicios .= '<option id="'.$a.'">'.$servicios_todos ['NombreS'][$y].'</option>';
            }
            $y++;
        }
        $y = 0;
        $resto_categorias = "";
        $servicios_todos ['Id_categoria'] = array_values( array_filter( array_unique ($servicios_todos ['Id_categoria'])));
        $servicios_todos ['NombreCS'] = array_values( array_filter( array_unique($servicios_todos ['NombreCS'])));
        foreach ($servicios_todos ['Id_categoria'] as $a){   
            if (empty($id_categorias_seleccionadas[$x]) || $a != $id_categorias_seleccionadas[$x]){
                $resto_categorias .= '<option id="'.$a.'">'.$servicios_todos ['NombreCS'][$y].'</option>';
            }
            $y++;    
        }
        
        $servicios .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="Categorias">
                <label for="Catservicio'.$x.'" class="form-group">Categoría de servicio # '.$x.': </label>
                <select class="form-control" id="Catservicio'.$x.'" name="Catservicio'.$x.'" onchange = "servicios_modificar_ticket('.$x.')">
                        <option id="'.$id_categorias_seleccionadas [$x].'">'.$nombre_categorias_seleccionadas [$x].'</option>'.$resto_categorias.'
                    </select>

            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="servicios">
                <label for="servicio'.$x.'" class="form-group">Servicio # '.$x.': </label>
                <select class="form-control" id="servicio'.$x.'" name="servicio'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_servicio('.$x.',0)">';
            $servicios .= '<option id="'.$id_servicios_seleccionados[$x].'">'.$nombre_servicios_seleccionados[$x].'</option>'.$resto_servicios.'

                    </select>
            </div>
          ';
        
    }
    
    $servicios .= '<input type="text" hidden id="no_listas_servicio" value="'.($x - 1).'">';
    disconnect($conexion);
    $seleccion = $servicios;
    echo $seleccion;
}

if(isset($_GET['categoria_de_servicio_modificar_ticket'])){
    $categoria_de_servicio = $_GET['categoria_de_servicio_modificar_ticket'];
    $conexion = connect();
    $query = "SELECT S.Nombre as NombreS FROM Categoria_de_servicios CS, Catalogo_de_servicios S WHERE S.Id_categoria = CS.Id_categoria AND CS.Nombre = '".$categoria_de_servicio."' ORDER BY S.Nombre ASC";
    $results = mysqli_query($conexion, $query);
    $rows = "<select class='form-control' id='dependencia' name='dependencia'>";
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows .= "<option>".$row['NombreS']."</option>";
    }
    mysqli_free_result($results);
    $rows .= "</select>";
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}

if(isset($_GET['mesa'])){
    $mesa = $_GET['mesa'];
    $conexion = connect();
    $query = "SELECT DE.Razon_social FROM Destino DE, Dependencias D, NombresMesas M WHERE M.Id_mesa = D.Id_mesa AND D.Id_destino = DE.Id_destino AND M.NombreM = '".$mesa."' ORDER BY DE.Razon_social ASC";
    $results = mysqli_query($conexion, $query);
    $rows = "<select class='form-control' id='dependencia' name='dependencia'>";
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows .= "<option>".$row['Razon_social']."</option>";
    }
    mysqli_free_result($results);
    $rows .= "</select>";
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}

if(isset($_GET['relacionar_ticket'])){
    $estatus = $_GET['relacionar_ticket'];
    $conexion = connect();
    if($estatus == "on"){
        $query="SELECT T.Id_ticket, T.No_ticket FROM Tickets T, Estatus_de_tickets E, Tickets_Estatus_de_tickets TE WHERE T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND (E.Nombre = 'En atencion' OR E.Nombre = 'Por llegar' OR E.Nombre = 'Confirmado') ORDER BY T.Id_ticket";
        $results = mysqli_query($conexion, $query);
        $rows = '<br>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label for="ticket">Ticket #:</label>
                    <select type="list" class="form-control" id="ticket" name="ticket">';
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $rows .= '<option>'.$row["No_ticket"].'</option>';
        }
        $rows .= '</select></div>';
        mysqli_free_result($results);   
    } else if ($estatus == "off"){
        $rows = "";
    }
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}


//OBTIENE UNA DEPENDENCIA DEPENDIENDO DE LA MESA SELECCIONADA
if(isset($_GET['SelMesa'])){
    $mesa = $_GET['SelMesa'];
    //$mesa = "mesa2";
    $conexion = connect();
    $query="SELECT Razon_social FROM Destino DE, Dependencias D, NombresMesas M WHERE M.Id_mesa = D.Id_mesa AND D.Id_destino = DE.Id_destino AND M.NombreM = '".$mesa."'";
    $results = mysqli_query($conexion, $query);
    $rows = '

                        <label for="dependencia">Dependencia: </label>
                            <select type="list" class="form-control" id="dependencia" name="dependencia" onchange = "categoriade_servicio_muestra()">
                                <option>Sleccione una dependencia...</option>
            ';
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows .= '<option>'.$row["Razon_social"].'</option>';
    }
    $rows .= '</select>';
    mysqli_free_result($results);   
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}


//OBTIENE UN SERVICIO DEPENDIENDO DE LA CATEGORIA DE SERVICIO
if(isset($_GET['cateSer'])){
    $cat_ser = $_GET['cateSer'];
    //$mesa = "mesa2";
    $conexion = connect();
    $query="SELECT CATT.Nombre AS 'CATTNOM' FROM Catalogo_de_servicios CATT, Categoria_de_servicios CATS WHERE CATS.Id_categoria = CATT.Id_categoria AND CATS.Nombre = '".$cat_ser."'";
    $results = mysqli_query($conexion, $query);
    $rows = '

                    <label for="cataSer">Servicio: </label>
                        <select class="form-control" id="Ser" name="Ser" onchange = "duracion_estimada()">
                            <option>Seleccione un servicio...</option>
            ';
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows .= '<option value="'.$row["CATTNOM"].'">'.$row["CATTNOM"].'</option>';
    }
    $rows .= '</select>';
    mysqli_free_result($results);   
    disconnect($conexion);
    $seleccion = $rows;
    echo $seleccion;
}

//OBTIENE LOS INGENIEROS DEPENDIENDO DEL TRABAJO SELECCIONADO
if(isset($_GET['Ser'])){
    $ser = $_GET['Ser'];


    if(isset($_GET['num_inges'])){
        $num_inges = $_GET['num_inges'];

        if(!isset($_GET['Ing'])){
            $conexion = connect();

            if($num_inges == 1){

                $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."'";
                $results = mysqli_query($conexion, $query);
                $rows = '
                        <div class="form-group">
                            <label for="Ing">Ingeniero: </label>
                                <select class="form-control" id="Ing" name="Ing" onchange="ing_transporte()">
                                    <option>Seleccione un ingeniero...</option>
                    ';
                $i=0;
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    $rows .= '<option value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                }
                $rows .= '</select></div> ';

                mysqli_free_result($results);
                disconnect($conexion);

            } else if ($num_inges >= 2){

                $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."'";
                $results = mysqli_query($conexion, $query);
                $rows = '
                        <div class="form-group">
                            <label for="Ing">Ingeniero: </label>
                                <select class="form-control" id="Ing" name="Ing" onchange="ingeniero_elegible2()">
                                    <option>Seleccione un ingeniero...</option>
                    ';
                $i=0;
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    $rows .= '<option value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                }
                $rows .= '</select></div> ';

                mysqli_free_result($results);
                disconnect($conexion);


            }

            $seleccion = $rows;
            echo $seleccion;
        }
    }


}
//INGENIERO 2
if(isset($_GET['Ser'])){
    $ser = $_GET['Ser'];


    if(isset($_GET['num_inges'])){
        $num_inges = $_GET['num_inges'];

        if(isset($_GET['Ing'])){
            $ing1 = $_GET['Ing'];


            if(!isset($_GET['Ing2'])){

                $conexion = connect();

                if($num_inges == 2){

                    $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."' AND USR.Nombre != '".$ing1."'";
                    $results = mysqli_query($conexion, $query);
                    $rows = '
                            <div class="form-group">
                                <label for="Ing">Ingeniero 2: </label>
                                    <select class="form-control" id="Ing2" name="Ing2" onchange="ing_transporte()">
                                        <option>Seleccione un ingeniero...</option>
                        ';
                    $i=0;
                    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                        $rows .= '<option value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                    }
                    $rows .= '</select></div> ';

                    mysqli_free_result($results);
                    disconnect($conexion);

                } else if ($num_inges >= 3){

                    $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."' AND USR.Nombre != '".$ing1."'";
                    $results = mysqli_query($conexion, $query);
                    $rows = '
                            <div class="form-group">
                                <label for="Ing">Ingeniero2: </label>
                                    <select class="form-control" id="Ing2" name="Ing2" onchange="ingeniero_elegible3()">
                                        <option>Seleccione un ingeniero...</option>
                        ';
                    $i=0;
                    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                        $rows .= '<option value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                    }
                    $rows .= '</select></div> ';

                    mysqli_free_result($results);
                    disconnect($conexion);

                }

                $seleccion = $rows;
                echo $seleccion;
            }
            /*$seleccion = $rows;
                echo $seleccion;*/
        }


    }
}

//INGENIERO 3
if(isset($_GET['Ser'])){
    $ser = $_GET['Ser'];


    if(isset($_GET['num_inges'])){
        $num_inges = $_GET['num_inges'];

        if(isset($_GET['Ing'])){
            $ing1 = $_GET['Ing'];


            if(isset($_GET['Ing2'])){
                $ing2 = $_GET['Ing2'];

                if(!isset($_GET['Ing3'])){
                    $conexion = connect();

                    if($num_inges == 3){

                        $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."' AND USR.Nombre != '".$ing1."' AND USR.Nombre != '".$ing2."'";
                        $results = mysqli_query($conexion, $query);
                        $rows = '
                                <div class="form-group">
                                    <label for="Ing">Ingeniero 3: </label>
                                        <select class="form-control" id="Ing2" name="Ing2" onchange="ing_transporte()">
                                            <option>Seleccione un ingeniero...</option>
                            ';
                        $i=0;
                        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                            $rows .= '<option value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                        }
                        $rows .= '</select></div> ';

                        mysqli_free_result($results);
                        disconnect($conexion);

                    } else if ($num_inges >= 3){

                        $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."' AND USR.Nombre != '".$ing1."' AND USR.Nombre != '".$ing2."'";
                        $results = mysqli_query($conexion, $query);
                        $rows = '
                                <div class="form-group">
                                    <label for="Ing">Ingeniero 3: </label>
                                        <select class="form-control" id="Ing3" name="Ing3" onchange="ingeniero_elegible4()">
                                            <option>Seleccione un ingeniero...</option>
                            ';
                        $i=0;
                        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                            $rows .= '<option value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                        }
                        $rows .= '</select></div> ';

                        mysqli_free_result($results);
                        disconnect($conexion);

                    }
                    $seleccion = $rows;
                    echo $seleccion;
                }
                //$seleccion = $rows;
                //echo $seleccion;
            }

        }


    }
}                        

//INGENIERO 4

if(isset($_GET['Ser'])){
    $ser = $_GET['Ser'];


    if(isset($_GET['num_inges'])){
        $num_inges = $_GET['num_inges'];

        if(isset($_GET['Ing'])){
            $ing1 = $_GET['Ing'];


            if(isset($_GET['Ing2'])){
                $ing2 = $_GET['Ing2'];

                if(isset($_GET['Ing3'])){
                    $ing3 = $_GET['Ing3'];

                    if(!isset($_GET['Ing4'])){
                        $conexion = connect();

                        if($num_inges == 4){

                            $query="SELECT USR.Nombre AS 'NombreUSR' FROM Catalogo_de_servicios CATT, Grados_de_complejidad_Ingenieros GCI, Ingenieros ING, Usuarios USR WHERE CATT.Id_grado = GCI.Id_grado AND GCI.Id_ingeniero = ING.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND CATT.Nombre = '".$ser."' AND USR.Nombre != '".$ing1."' AND USR.Nombre != '".$ing2."' AND USR.Nombre != '".$ing3."'";
                            $results = mysqli_query($conexion, $query);
                            $rows = '
                                    <div class="form-group">
                                        <label for="Ing">Ingeniero 4: </label>
                                            <select class="form-control" id="Ing4" name="Ing4" onchange="ing_transporte()">
                                                <option>Seleccione un ingeniero...</option>
                                ';
                            $i=0;
                            while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                                $rows .= '<option                   value="'.$row["NombreUSR"].'">'.$row["NombreUSR"].'</option>';
                            }
                            $rows .= '</select></div> ';

                            mysqli_free_result($results);
                            disconnect($conexion);

                        }
                        $seleccion = $rows;
                        echo $seleccion;
                    }
                    //$seleccion = $rows;
                    //echo $seleccion;
                }

            }


        }
    } 
}

?>