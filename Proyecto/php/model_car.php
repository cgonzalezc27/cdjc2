<?php
 require_once('globals.php');

require_once ($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
//require_once ($_SERVER["DOCUMENT_ROOT"].'./cdjc/Proyecto/vendor/autoload.php');
require_once ('correo.php');
use Mailgun\Mailgun;

 function connect(){
        $conexion = mysqli_connect($GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"],$GLOBALS["db"]);
        if($conexion == NULL) {
            die("Error al conectarse con la base de datos/ error de conexion". mysqli_connect_errno() . PHP_EOL);
        }
        $conexion->set_charset("utf8");
        return $conexion;
    }

function fecha_from_output_to_input($fecha){
    $fecha = str_replace("T"," ",$fecha);
    return $fecha;
}
function fecha_from_input_to_output($fecha){
    $fecha = str_replace(" ","T",$fecha);
    return $fecha;
}
function convertir_arreglo_fecha($x){
    $fecha['year'] = (int)substr($x,0,4);
    $fecha['month'] = (int)substr($x,5,2);
    $fecha['day'] = (int)substr($x,8,2);
    $fecha['hour'] = (int)substr($x,11,2);
    $fecha['minute'] = (int)substr($x,14,2);
    $fecha['second'] = (int)substr($x,17,2);
    return $fecha;
}
function convertir_arreglo_a_output_string_fecha($x){
    if (strlen($x['month']) == 1){
        $x['month'] = "0".$x['month'];
    }
    if (strlen($x['day']) == 1){
        $x['day'] = "0".$x['day'];
    }
    if (strlen($x['hour']) == 1){
        $x['hour'] = "0".$x['hour'];
    }
    if (strlen($x['minute']) == 1){
        $x['minute'] = "0".$x['minute'];
    }
    if (strlen($x['second']) == 1){
        $x['second'] = "0".$x['second'];
    }
    $fecha = $x['year']."-".$x['month']."-".$x['day']."T".$x['hour'].":".$x['minute'].":".$x['second'];
    return $fecha;
}
function convertir_arreglo_a_input_string_fecha($x){
    if (strlen($x['month']) == 1){
        $x['month'] = "0".$x['month'];
    }
    if (strlen($x['day']) == 1){
        $x['day'] = "0".$x['day'];
    }
    if (strlen($x['hour']) == 1){
        $x['hour'] = "0".$x['hour'];
    }
    if (strlen($x['minute']) == 1){
        $x['minute'] = "0".$x['minute'];
    }
    if (strlen($x['second']) == 1){
        $x['second'] = "0".$x['second'];
    }
    $fecha = $x['year']."-".$x['month']."-".$x['day']." ".$x['hour'].":".$x['minute'].":".$x['second'];
    return $fecha;
}


function connect_correo(){

    $mgClient = new Mailgun(clave());
    return $mgClient;
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


//Funcion para btener los tiempos estimados de duracion del tique
function obtener_tiempos(){
    $asignador = 0;
    $duracion = "";
    for ($c=1;$c <= 20; $c++){
        if ($asignador == 0){
            $duracion .=  "<option value=''>Seleccione el tiempo esperado...</option>";
        } else {
            $duracion .=  "<option value".$asignador.">".$asignador." horas</option>";
        }
        $asignador = $asignador + 0.5;
        
    }
    echo $duracion;
}


    function registrar_ticket($opc){
        $conexion = connect();
        switch($opc){
            //CONSULTA LAS MESAS
            case 0:
                /*
                                    SELECT NombreM, NM.Id_mesa AS 'Id_destino'
                    FROM Mesas MES
                    INNER JOIN Destino DES ON MES.Id_destino = DES.Id_destino
                    INNER JOIN NombresMesas NM ON MES.Id_mesa = NM.Id_mesa
                    GROUP BY NombreM
                    ORDER BY NombreM
                    
                */
                $query = ("
                    
                    SELECT NombreM, NM.Id_mesa AS 'Id_destino'
                    FROM NombresMesas NM
                    INNER JOIN Dependencias DEP ON NM.Id_mesa = DEP.Id_mesa 
                    GROUP BY NombreM
                    ORDER BY NombreM
                    
                    
                    ");

                $results = mysqli_query($conexion, $query);


                $i = 0;
                $rows = [];
                echo '<option value="Seleccione una Mesa...">Seleccione una Mesa...</option>';
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    echo '<option value="'.$row['Id_destino'].'">'.$row['NombreM'].'</option>';
                }
                mysqli_free_result($results);
                break;
            //CONSULTA DEPENDENCIA
            case 1:

                break;


            //CONSULTA CATEGORIAS DE SERVICIO
            case 2:

                break;
            //CONSULTAR SERVICIO
            case 3:
                break;

            //CONSULTAR INGENIERO
            case 4:

                break;
            //CONSULTAR TRNSPORTES
            case 5:
                $query = ("SELECT Nombre AS 'NombreTRA'FROM Medios_de_transporte;");

                $results = mysqli_query($conexion, $query);


                $i = 1;
                $rows = [];
                echo '<option value="">Seleccione un transporte...</option>';
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    echo '<option value="'.$i.'">'.$row['NombreTRA'].'</option>';
                    $i++;
                }
                mysqli_free_result($results);
                break;

        }



        disconnect($conexion);
    }


    function registrar_ticket2($no_ticket, $mesa, $dependencia, $cat_servicio, $servicio, $duracion_est, $fecha_inicial, $ingeniero, $transporte, $comentario, $ingeniero2, $ingeniero3, $ingeniero4, $Ser2, $Ser3, $Ser4){
        $conexion = connect();


//Registra en la tabla de tickets
        $query= "SELECT Id_dependencia FROM Dependencias WHERE Id_destino = ".$dependencia."";
        $resultados = mysqli_query($conexion, $query);
        $i = 0;
        $rows = [];
        while($row=mysqli_fetch_array($resultados, MYSQLI_BOTH))
            {
                $Id_dependencia = $row['Id_dependencia'];
            }
        

        
        if($transporte = "Autobús"){
            $id_transporte = 1;
        }else if($transporte = "Automovil"){
            $id_transporte = 2;
        }


        $query = "INSERT INTO Tickets (No_ticket, Fecha_y_hora_de_inicio_programada, Duracion_estimada_por_admin, Comentario_inicial, Id_medio, Id_dependencia, Id_destino)
        VALUES ('".$no_ticket."', '".$fecha_inicial."', '".$duracion_est."', '".$comentario."', ".$id_transporte.", ".$Id_dependencia.", ".$dependencia.")";

        $resultado = mysqli_query($conexion, $query);        
        
        
//Establece el estatus del tique en 1
        
        $query= "SELECT Id_ticket FROM Tickets ORDER BY Id_ticket DESC LIMIT 1";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
            {
                $rows = $row;
            }
        $id_tique = $rows['Id_ticket'];
        
        
        $query = "INSERT INTO Tickets_Estatus_de_tickets (Id_estatus, Id_ticket)
        VALUES (1, ".$id_tique.")";
        
        $resultado = mysqli_query($conexion, $query);
        $resultado = TRUE;
        
//Registra la mesa y dependencia
        
        
        
//Registra el tique a un servicio        
        $query = "INSERT INTO Catalogo_de_servicios_Tickets (Id_trabajo, Id_ticket)
        VALUES (".$servicio.", ".$id_tique.")";
        
        $resultado = mysqli_query($conexion, $query);
        $resultado = TRUE;
        
        
        //Servicio 2
        if($Ser2 != "NULL"){

            $query = "INSERT INTO Catalogo_de_servicios_Tickets (Id_trabajo, Id_ticket)
            VALUES (".$Ser2.", ".$id_tique.")";
            
            $resultado = mysqli_query($conexion, $query);
            $resultado = TRUE;
            
        }
        
        //Servicio 3
        if($Ser3 != "NULL"){

            $query = "INSERT INTO Catalogo_de_servicios_Tickets (Id_trabajo, Id_ticket)
            VALUES (".$Ser3.", ".$id_tique.")";
            
            $resultado = mysqli_query($conexion, $query);
            $resultado = TRUE;
        }
        
        //Servicio 4
        if($Ser4 != "NULL"){

            $query = "INSERT INTO Catalogo_de_servicios_Tickets (Id_trabajo, Id_ticket)
            VALUES (".$Ser4.", ".$id_tique.")";
            
            $resultado = mysqli_query($conexion, $query);
            $resultado = TRUE;
        }
        
//Asigna los ingenieros a un ticket

        $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
        VALUES (".$id_tique.", ".$ingeniero.")";
        
        $resultado = mysqli_query($conexion, $query);
        $resultado = TRUE;
        
        //Ingeniero 2
        if($ingeniero2 != "NULL"){

            $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
            VALUES (".$id_tique.", ".$ingeniero2.")";
            
            $resultado = mysqli_query($conexion, $query);
            $resultado = TRUE;
        }
        
        //Ingeniero 3
        if($ingeniero3 != NULL){

            $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
            VALUES (".$id_tique.", ".$ingeniero3.")";
            
            $resultado = mysqli_query($conexion, $query);
            $resultado = TRUE;
        }

        //Ingeniero 4
        if($ingeniero4 != NULL){

            $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
            VALUES (".$id_tique.", ".$ingeniero4.")";
            
            $resultado = mysqli_query($conexion, $query);
            $resultado = TRUE;
        }
        
        return $resultado;
        disconnect($conexion);
    }

function obtenId(){
    $conexion = connect();
        $query= "SELECT Id_ticket FROM Tickets ORDER BY Id_ticket DESC LIMIT 1";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
            {
                $rows = $row;
            }
        $id_tique = $rows['Id_ticket'];
        return $id_tique;
}

function consulta_ticket($id){
    $conexion = connect();

    $query="SELECT T.Id_ticket, T.No_ticket, T.Fecha_de_creacion, DE.Razon_social AS 'NombreD', ME.NombreM, ME.Correo_electronico AS 'Correo_mesa', T.Duracion_estimada_por_admin AS 'Duracion', U.Nombre AS 'NombreI', U.Apellido1 AS 'Apellido1I', U.Apellido2 AS 'Apellido2I', MT.Nombre AS 'NombreMT', T.Fecha_y_hora_de_inicio_programada AS 'FechaI', T.Fecha_y_hora_de_finalizacion_real AS 'FechaF', T.Comentario_inicial AS 'Comentario', E.Nombre as 'NombreE', UE.Fecha_hora AS 'Fecha_hora_actualizacion', T.Comentario_diagnostico, T.Comentario_final


    FROM Dependencias D, Destino DE, NombresMesas ME, Tickets T, Ingenieros I, Ingenieros_Tickets IT, Usuarios U, Medios_de_transporte MT, Estatus_de_tickets E, Ultimo_estatus_ticket UE

    WHERE T.Id_dependencia = D.Id_dependencia AND DE.Id_destino = D.Id_destino AND D.Id_mesa = ME.Id_mesa AND T.Id_ticket = IT.Id_ticket AND IT.Id_ingeniero = I.Id_ingeniero AND I.Id_usuario = U.Id_usuario AND T.Id_medio = MT.Id_medio AND T.Id_ticket = UE.Id_ticket AND UE.Id_estatus = E.Id_estatus AND

    T.Id_ticket ='".$id."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows [$i]['Id_ticket'] = $row['Id_ticket'];
        $rows [$i]['No_ticket'] = $row['No_ticket'];
        $rows [$i]['NombreD'] = $row['NombreD'];
        $rows [$i]['NombreM'] = $row['NombreM'];
        $rows [$i]['Correo_mesa'] = $row['Correo_mesa'];
        $rows [$i]['Duracion'] = $row['Duracion'];
        $rows [$i]['NombreI'] = $row['NombreI'];
        $rows [$i]['Apellido1I'] = $row['Apellido1I'];
        $rows [$i]['Apellido2I'] = $row['Apellido2I'];
        $rows [$i]['FechaI'] = fecha_from_input_to_output($row['FechaI']) ;
        $rows [$i]['Comentario'] = $row['Comentario'];
        $rows [$i]['NombreMT'] = $row['NombreMT'];
        $rows [$i]['NombreE'] = $row['NombreE'];
        $rows [$i]['Fecha_de_creacion'] = $row['Fecha_de_creacion'];
        $rows [$i]['Fecha_hora_actualizacion'] = $row['Fecha_hora_actualizacion'];
        $rows [$i]['Comentario_diagnostico'] = $row['Comentario_diagnostico'];
        $rows [$i]['Comentario_final'] = $row['Comentario_final'];
        $i++;
    }
    mysqli_free_result($results);
    $ingenieros = "";
    for($x = 1; $x <= $i; $x++){
        $ingenieros .= '
        <div class="form-group col-lg-6 col-md-6 col-sm-12">
            <label for="ingeniero'.$x.'" class="form-group">Ingeniero asignado # '.$x.': </label>
            <select class="form-control" id="ingeniero'.$x.'" name="ingeniero'.$x.'" disabled>
                <option>'.$rows [$x - 1]['NombreI'].' '.$rows [$x - 1]['Apellido1I'].' '.$rows [$x - 1]['Apellido2I'].'</option></select>
        </div>
      ';
    }
    
    $asignador = 0;
    $duracion = "";
    for ($c=1;$c <= 20; $c++){
        $asignador = $asignador + 0.5;
        if ($asignador == $rows [0]['Duracion']){
            $duracion .=  "<option selected>".$asignador." horas</option>";
        } else {
            $duracion .=  "<option>".$asignador." horas</option>";
        }
        
    }
    
    $query="SELECT T.Id_ticket, S.Nombre AS 'NombreS', CS.Nombre AS 'NombreCS'


    FROM Tickets T, Catalogo_de_servicios S, Catalogo_de_servicios_Tickets ST, Categoria_de_servicios CS

    WHERE T.Id_ticket = ST.Id_ticket AND ST.Id_trabajo = S.Id_trabajo AND S.Id_categoria = CS.Id_categoria AND

    T.Id_ticket = '".$id."';";
    $results = mysqli_query($conexion, $query);
    $filas = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $filas [$i]['NombreCS'] = $row['NombreCS'];
        $filas [$i]['NombreS'] = $row['NombreS'];
        $i++;
    }
    mysqli_free_result($results);

    $servicios = "";
    for($x = 1; $x <= $i; $x++){
        $servicios .= '
        <div class="form-group col-lg-6 col-md-6 col-sm-12">
            <label for="cs'.$x.'" class="form-group">Categoría de servicio # '.$x.': </label>
            <select class="form-control" id="cs'.$x.'" name="cs'.$x.'" disabled>
                <option>'.$filas [$x - 1]['NombreCS'].'</option></select>
        </div>
        <div class="form-group col-lg-6 col-md-6 col-sm-12">
            <label for="cs'.$x.'" class="form-group">Servicio # '.$x.': </label>
            <select class="form-control" id="servicio'.$x.'" name="servicio'.$x.'" disabled>
                <option>'.$filas [$x - 1]['NombreS'].'</option></select>
        </div>
      ';
    }
    
    
    $FechaI = convertir_arreglo_fecha($rows [0]['FechaI']);
    $hora_duracion = floor($rows [0]['Duracion']);
    $minutos_duracion = ($rows [0]['Duracion'] - $hora_duracion)*60;
    $FechaF =$FechaI;
    $FechaF['hour'] = $FechaI['hour'] + $hora_duracion;
    $FechaF['minute'] = $FechaI['minute'] + $minutos_duracion;
    $FechaF = convertir_arreglo_a_output_string_fecha ($FechaF);
    
    $rows [0]['Ingenieros'] = $ingenieros;
    $rows [0]['Servicios'] = $servicios;
    $rows [0]['Duracion'] = $duracion;
    $rows [0]['FechaF'] = $FechaF;
    disconnect($conexion);

    return $rows;
}
?>
