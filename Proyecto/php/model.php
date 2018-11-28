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

function consultar_tickets_abiertos($Id_usuario,$desplegar_todos){
    $conexion = connect();
    if ($desplegar_todos == 2){
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S, Usuarios U, Ingenieros I, Ingenieros_Tickets IT WHERE U.Id_usuario = I.Id_usuario AND I.Id_ingeniero = IT.Id_ingeniero AND IT.Id_ticket = T.Id_ticket AND T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre = 'En atencion' OR E.Nombre = 'Por llegar' OR E.Nombre = 'Confirmado') AND U.Id_usuario ='".$Id_usuario."' ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else if ($desplegar_todos == 1) {
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada

        FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S


        WHERE T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre = 'En atencion' OR E.Nombre = 'Por llegar' OR E.Nombre = 'Confirmado') ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else {
        $tickets = 'No tienes permiso para ver tickets';
        return $tickets;
    }
}

function buscar_tickets($buscar,$Id_usuario,  $desplegar_todos){
    $conexion = connect();
    if ($desplegar_todos == 2){
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S, Usuarios U, Ingenieros I, Ingenieros_Tickets IT WHERE U.Id_usuario = I.Id_usuario AND I.Id_ingeniero = IT.Id_ingeniero AND IT.Id_ticket = T.Id_ticket AND T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre = 'En atencion' OR E.Nombre = 'Por llegar' OR E.Nombre = 'Confirmado') AND U.Id_usuario ='".$Id_usuario."' AND (T.No_ticket LIKE '%".$buscar."%' OR E.Nombre LIKE '%".$buscar."%' OR DE.Razon_social LIKE '%".$buscar."%' OR S.Nombre LIKE '%".$buscar."%' OR U.Nombre LIKE '%".$buscar."%' OR U.Apellido1 LIKE '%".$buscar."%' OR U.Apellido2 LIKE '%".$buscar."%' OR T.Comentario_inicial LIKE '%".$buscar."%' OR T.Comentario_diagnostico LIKE '%".$buscar."%' OR T.Comentario_final LIKE '%".$buscar."%') ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'&buscar='.$buscar.'&h=_tickets_abiertos_v2.php">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        if(empty($dependencia) || $dependencia == "" || $dependencia == NULL){
            $tickets = '<div class = "error"> *Sin resultados en la base de datos </div>';
        }
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else if ($desplegar_todos == 1) {
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada

        FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S, Usuarios U, Ingenieros I, Ingenieros_Tickets IT WHERE U.Id_usuario = I.Id_usuario AND I.Id_ingeniero = IT.Id_ingeniero AND IT.Id_ticket = T.Id_ticket AND T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre = 'En atencion' OR E.Nombre = 'Por llegar' OR E.Nombre = 'Confirmado') AND (T.No_ticket LIKE '%".$buscar."%' OR E.Nombre LIKE '%".$buscar."%' OR DE.Razon_social LIKE '%".$buscar."%' OR S.Nombre LIKE '%".$buscar."%' OR U.Nombre LIKE '%".$buscar."%' OR U.Apellido1 LIKE '%".$buscar."%' OR U.Apellido2 LIKE '%".$buscar."%' OR T.Comentario_inicial LIKE '%".$buscar."%' OR T.Comentario_diagnostico LIKE '%".$buscar."%' OR T.Comentario_final LIKE '%".$buscar."%') ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'&buscar='.$buscar.'&h=_tickets_abiertos_v2.php">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        if(empty($dependencia) || $dependencia == "" || $dependencia == NULL){
            $tickets = '<div class = "error"> *Sin resultados en la base de datos </div>';
        }
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else {
        $tickets = 'No tienes permiso para ver tickets';
        return $tickets;
    }
}

function buscar_historial_tickets($buscar,$Id_usuario,  $desplegar_todos){
    $conexion = connect();
    if ($desplegar_todos == 2){
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S, Usuarios U, Ingenieros I, Ingenieros_Tickets IT WHERE U.Id_usuario = I.Id_usuario AND I.Id_ingeniero = IT.Id_ingeniero AND IT.Id_ticket = T.Id_ticket AND T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre != 'En atencion' AND E.Nombre != 'Por llegar' AND E.Nombre != 'Confirmado') AND U.Id_usuario ='".$Id_usuario."' AND (T.No_ticket LIKE '%".$buscar."%' OR E.Nombre LIKE '%".$buscar."%' OR DE.Razon_social LIKE '%".$buscar."%' OR S.Nombre LIKE '%".$buscar."%' OR U.Nombre LIKE '%".$buscar."%' OR U.Apellido1 LIKE '%".$buscar."%' OR U.Apellido2 LIKE '%".$buscar."%' OR T.Comentario_inicial LIKE '%".$buscar."%' OR T.Comentario_diagnostico LIKE '%".$buscar."%' OR T.Comentario_final LIKE '%".$buscar."%') ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'&buscar='.$buscar.'&h=_historial_tickets.php">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        if(empty($dependencia) || $dependencia == "" || $dependencia == NULL){
            $tickets = '<div class = "error"> *Sin resultados en la base de datos </div>';
        }
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else if ($desplegar_todos == 1) {
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada

        FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S, Usuarios U, Ingenieros I, Ingenieros_Tickets IT WHERE U.Id_usuario = I.Id_usuario AND I.Id_ingeniero = IT.Id_ingeniero AND IT.Id_ticket = T.Id_ticket AND T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre != 'En atencion' AND E.Nombre != 'Por llegar' AND E.Nombre != 'Confirmado') AND (T.No_ticket LIKE '%".$buscar."%' OR E.Nombre LIKE '%".$buscar."%' OR DE.Razon_social LIKE '%".$buscar."%' OR S.Nombre LIKE '%".$buscar."%' OR U.Nombre LIKE '%".$buscar."%' OR U.Apellido1 LIKE '%".$buscar."%' OR U.Apellido2 LIKE '%".$buscar."%' OR T.Comentario_inicial LIKE '%".$buscar."%' OR T.Comentario_diagnostico LIKE '%".$buscar."%' OR T.Comentario_final LIKE '%".$buscar."%') ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'&buscar='.$buscar.'&h=_historial_tickets.php">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        if(empty($dependencia) || $dependencia == "" || $dependencia == NULL){
            $tickets = '<div class = "error"> *Sin resultados en la base de datos </div>';
        }
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else {
        $tickets = 'No tienes permiso para ver tickets';
        return $tickets;
    }
}


function consultar_historial_tickets($Id_usuario,$desplegar_todos){
    $conexion = connect();
    if ($desplegar_todos == 2){
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S, Usuarios U, Ingenieros I, Ingenieros_Tickets IT WHERE U.Id_usuario = I.Id_usuario AND I.Id_ingeniero = IT.Id_ingeniero AND IT.Id_ticket = T.Id_ticket AND T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre != 'En atencion' AND E.Nombre != 'Por llegar' AND E.Nombre != 'Confirmado') AND U.Id_usuario ='".$Id_usuario."' ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else if ($desplegar_todos == 1) {
        $query="SELECT T.Id_ticket, T.No_ticket, E.Nombre as 'NombreE', DE.Razon_social,S.Nombre as 'NombreS', T.Fecha_y_hora_de_inicio_programada

        FROM Tickets T, Estatus_de_tickets E, Ultimo_estatus_ticket TE, Dependencias D, Destino DE, Catalogo_de_servicios_Tickets TS, Catalogo_de_servicios S


        WHERE T.Id_ticket = TE.Id_ticket AND TE.Id_estatus = E.Id_estatus AND T.Id_dependencia = D.Id_dependencia AND D.Id_destino = DE.Id_destino AND T.Id_ticket = TS.Id_ticket AND TS.Id_trabajo = S.Id_trabajo AND (E.Nombre != 'En atencion' AND E.Nombre != 'Por llegar' AND E.Nombre != 'Confirmado') ORDER BY T.Fecha_y_hora_de_inicio_programada";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        $tickets = '<div class="row justify-content-md-center justify-content-lg-start justify-content-sm-center" id="a">';
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            if(strlen($row['Razon_social'])>35){
                $dependencia = substr($row['Razon_social'],0,35).'...';
            }else{
                $dependencia = $row['Razon_social'];
            }
            if(strlen($row['No_ticket'])>13){
                $row['No_ticket'] = substr($row['No_ticket'],0,13).'...';
            }else{
                $row['No_ticket'] = $row['No_ticket'];
            }
            if(strlen($row['NombreS'])>15){
                $servicio = substr($row['NombreS'],0,15).'...';
            }else{
                $servicio = $row['NombreS'];
            }
            $tickets .='
                 <div class = "col-lg-4 col-md-6 col-sm-7">
                    <a href="/php/_view_modificar_ticket.php?id='.$row['Id_ticket'].'">
                        <div class="card window-card">
                            <div class="row">
                                <div class = "col-3">
                                    <div class ="'.str_replace(" ","_",$row['NombreE']).'"></div>
                                </div>
                                <div class="col-9 ticket">
                                    <h6 class="control">Ticket No. '.$row['No_ticket'].'</h6>
                                    <ul class="list-unstyled">
                                        <li>Dependencia: '.$dependencia.'</li>
                                        <li>Trabajo: '.$servicio.'</li>
                                        <li>Inicio: '.$row['Fecha_y_hora_de_inicio_programada'].'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
            $prueba = str_replace(" ","_",$row['NombreE']);
        }
        $tickets .= '</div>';
        mysqli_free_result($results);
        disconnect($conexion);
        return $tickets;
    } else {
        $tickets = 'No tienes permiso para ver tickets';
        return $tickets;
    }
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

function consulta_modificar_ticket($id){
    $conexion = connect();
    $query="SELECT T.Id_ticket, T.No_ticket, DE.Razon_social AS 'NombreD', ME.NombreM, ME.Id_mesa, T.Duracion_estimada_por_admin AS 'Duracion', U.Nombre AS 'NombreI', U.Apellido1 AS 'Apellido1I', U.Apellido2 AS 'Apellido2I', MT.Nombre AS 'NombreMT', T.Fecha_y_hora_de_inicio_programada AS 'FechaI', T.Fecha_y_hora_de_finalizacion_real AS 'FechaF', T.Comentario_inicial AS 'Comentario', E.Nombre as 'NombreE', T.Comentario_final, I.Id_ingeniero

    FROM Dependencias D, Destino DE, NombresMesas ME, Tickets T, Ingenieros I, Ingenieros_Tickets IT, Usuarios U, Medios_de_transporte MT, Estatus_de_tickets E, Ultimo_estatus_ticket UE

    WHERE T.Id_dependencia = D.Id_dependencia AND DE.Id_destino = D.Id_destino AND D.Id_mesa = ME.Id_mesa AND T.Id_ticket = IT.Id_ticket AND IT.Id_ingeniero = I.Id_ingeniero AND I.Id_usuario = U.Id_usuario AND T.Id_medio = MT.Id_medio AND T.Id_ticket = UE.Id_ticket AND UE.Id_estatus = E.Id_estatus AND

    T.Id_ticket = '".$id."';";
    $results = mysqli_query($conexion, $query);
    $ticket_actual = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $ticket_actual [$i]['Id_ticket'] = $row['Id_ticket'];
        $ticket_actual [$i]['No_ticket'] = $row['No_ticket'];
        $ticket_actual [$i]['NombreD'] = $row['NombreD'];
        $ticket_actual [$i]['NombreM'] = $row['NombreM'];
        $ticket_actual [$i]['Id_mesa'] = $row['Id_mesa'];
        $ticket_actual [$i]['Duracion'] = $row['Duracion'];
        $ticket_actual [$i]['NombreI'] = $row['NombreI'];
        $ticket_actual [$i]['Apellido1I'] = $row['Apellido1I'];
        $ticket_actual [$i]['Apellido2I'] = $row['Apellido2I'];
        $ticket_actual [$i]['Id_ingeniero'] = $row['Id_ingeniero'];
        $ticket_actual [$i]['FechaI'] = fecha_from_input_to_output($row['FechaI']);
        $ticket_actual [$i]['FechaF'] = $row['FechaF'];
        $ticket_actual [$i]['Comentario'] = $row['Comentario'];
        $ticket_actual [$i]['NombreMT'] = $row['NombreMT'];
        $ticket_actual [$i]['NombreE'] = $row['NombreE'];
        $ticket_actual [$i]['Comentario_final'] = $row['Comentario_final'];
        $i++;
    }
    mysqli_free_result($results);
    
    $no_ingenieros = $i;
    
    for ($a = 0; $a <= $i-1; $a++){
        $Id_ingenieros_ticket_actual[$a] = $ticket_actual [$a]['Id_ingeniero'];
    }
    
    $asignador = 0;
    $duracion = "";
    for ($c=1;$c <= 20; $c++){
        $asignador = $asignador + 0.5;
        if ($asignador == $ticket_actual [0]['Duracion']){
            $duracion .=  "<option selected>".$asignador." horas</option>";
        } else {
            $duracion .=  "<option>".$asignador." horas</option>";
        }
        
    }
    
    $mesas = "<option>".$ticket_actual [0]['NombreM']."</option>";
    $query = "SELECT ME.NombreM FROM NombresMesas ME WHERE ME.Id_mesa != ".$ticket_actual [0]['Id_mesa'];
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $mesas .= "<option>".$row['NombreM']."</option>";
    }
    mysqli_free_result($results);

    $dependencias = "<option>".$ticket_actual [0]['NombreD']."</option>";
    $query = "SELECT DE.Razon_social AS NombreD FROM Destino DE, Dependencias D WHERE DE.Id_destino = D.Id_destino AND D.Id_mesa = ".$ticket_actual [0]['Id_mesa']." AND DE.Razon_social !='".$ticket_actual [0]['NombreD']."'";
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $dependencias .= "<option>".$row['NombreD']."</option>";
    }
    mysqli_free_result($results);


    $medios = "<option>".$ticket_actual [0]['NombreMT']."</option>";
    $query = "SELECT M.Nombre AS NombreMT FROM Medios_de_transporte M WHERE M.Nombre != '".$ticket_actual [0]['NombreMT']."'";
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $medios .= "<option>".$row['NombreMT']."</option>";
    }
    mysqli_free_result($results);

    $estatus = "<option>".$ticket_actual [0]['NombreE']."</option>";
    $query = "SELECT E.Nombre AS NombreE FROM Estatus_de_tickets E WHERE E.Nombre != '".$ticket_actual [0]['NombreE']."'";
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $estatus .= "<option>".$row['NombreE']."</option>";
    }
    mysqli_free_result($results);
    
    $query = "SELECT I.Id_ingeniero, U.Nombre, U.Apellido1, U.Apellido2 FROM Usuarios U, Ingenieros I WHERE I.Id_usuario = U.Id_usuario AND I.Visible = 1 GROUP BY I.Id_ingeniero";
    $results = mysqli_query($conexion, $query);
    
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $ingenieros_todos['Nombre'][$i] = $row['Nombre'].' '.$row['Apellido1'].' '.$row['Apellido2'];
        $ingenieros_todos['Id_ingeniero'][$i] = $row['Id_ingeniero'];
        $i++;
    }
    
    for($x = 1; $x <= $no_ingenieros; $x++){
        $y = 0;
        $resto_ingenieros [$x] = "";
        $ingenieros_en_resto = [];  
        
        foreach ($ingenieros_todos ['Id_ingeniero'] as $a){   
            if (in_array($a,$Id_ingenieros_ticket_actual) == FALSE){
                $id_resto_ingenieros [$x][$y] = $a;
                $nombre_resto_ingenieros [$x][$y] = $ingenieros_todos ['Nombre'][$y];
            }
            $y++;       
        }
    }
    $ingenieros= "";
    for($x = 1; $x <= $no_ingenieros; $x++){
        $y = 0;
        $resto_ingenieros = "";
        
        $ingenieros_en_resto = [];
        foreach ($ingenieros_todos ['Id_ingeniero'] as $a){   
            if (in_array($a,$Id_ingenieros_ticket_actual) == FALSE){
                $resto_ingenieros .= '<option id="'.$a.'">'.$ingenieros_todos ['Nombre'][$y].'</option>';
            }
            $y++;       
        }
        if(isset($ticket_actual [$x - 1]['Id_ingeniero'])){
            $ingenieros .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="ingenieros">
                <label for="inge'.$x.'" class="form-group">Ingeniero asignado # '.$x.': </label>
                <select class="form-control" id="inge'.$x.'" name="inge'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_ingeniero('.$x.')">
                    <option id="'.$ticket_actual [$x - 1]['Id_ingeniero'].'">'.$ticket_actual [$x - 1]['NombreI'].' '.$ticket_actual [$x - 1]['Apellido1I'].' '.$ticket_actual [$x - 1]['Apellido2I'].'</option>'
                
                .$resto_ingenieros.'

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

    }
    $ingenieros .= '<input type="text" hidden id="no_listas" value="'.($x - 1).'">';
    
    $i = $x - 1;
    $NoIngenieros = "";
    for($x = 1; $x <= 4; $x++){
        if($x==$no_ingenieros){
            $NoIngenieros .= '<option value = "'.$x.'"  selected>'.$x.'</option>';
        } else {
            $NoIngenieros .= '<option value = "'.$x.'" >'.$x.'</option>';
        }
    }
    
    $query = "SELECT S.Id_trabajo, S.Nombre as 'NombreS', CS.Id_categoria, CS.Nombre as 'NombreCS' FROM Catalogo_de_servicios S, Categoria_de_servicios CS WHERE S.Id_categoria = CS.Id_categoria AND S.Visible = 1 AND CS.Visible = 1";
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
    $query="SELECT T.Id_ticket, S.Nombre AS 'NombreS', CS.Nombre AS 'NombreCS', S.Id_trabajo, CS.Id_categoria
    FROM Tickets T, Catalogo_de_servicios S, Catalogo_de_servicios_Tickets ST, Categoria_de_servicios CS
    WHERE T.Id_ticket = ST.Id_ticket AND ST.Id_trabajo = S.Id_trabajo AND S.Id_categoria = CS.Id_categoria AND
    T.Id_ticket = '".$id."';";
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
    $no_servicios = $i;
    for($x = 1; $x <= $no_servicios; $x++){
        $y = 0;
        $resto_servicios [$x] = "";
        $servicios_en_resto = [];  
        
        foreach ($servicios_todos ['Id_trabajo'] as $a){   
            if (in_array($a,$servicios_ticket_actual['Id_trabajo']) == FALSE){
                $id_resto_servicios [$x][$y] = $a;
                $nombre_resto_servicios [$x][$y] = $servicios_todos ['NombreS'][$y];
                $id_resto_categorias [$x][$y] = $servicios_todos ['Id_categoria'][$y];
                $nombre_resto_categorias [$x][$y] = $servicios_todos ['NombreCS'][$y];
            }
            $y++;       
        }
    }
    $servicios= "";
    $categorias_todos = $servicios_todos ['Id_categoria'];
    $servicios_todos ['Id_categoria'] = array_values( array_filter( array_unique ($servicios_todos ['Id_categoria'])));
    $servicios_todos ['NombreCS'] = array_values( array_filter( array_unique($servicios_todos ['NombreCS'])));
    
    for($x = 1; $x <= $no_servicios; $x++){
        $y = 0;
        $resto_servicios = "";
        $resto_categorias = "";
       
        $servicios_en_resto = [];
        foreach ($servicios_todos ['Id_trabajo'] as $a){   
            if (in_array($a,$servicios_ticket_actual['Id_trabajo']) == FALSE && $servicios_ticket_actual['Id_categoria'][$x-1] == $categorias_todos[$y]){
                $resto_servicios .= '<option id="'.$a.'">'.$servicios_todos ['NombreS'][$y].'</option>';
            }
            $y++;       
        }
        $y = 0;

        foreach ($servicios_todos ['Id_categoria'] as $a){   
            if ($a != $servicios_ticket_actual['Id_categoria'][$x-1]){
                $resto_categorias .= '<option id="'.$a.'">'.$servicios_todos ['NombreCS'][$y].'</option>';
            }
            $y++;    
        }
        
        if(isset($servicios_ticket_actual ['Id_trabajo'] [$x-1])){
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
        } else {
            $servicios .= '
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="ingenieros">
                <label for="Catservicio'.$x.'" class="form-group">Categoría de servicio # '.$x.': </label>
                <select class="form-control" id="Catservicio'.$x.'" name="Catservicio'.$x.'" onchange = "servicios_modificar_ticket('.$x.')">
                    '.$resto_categorias.'
            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="ingenieros">
                <label for="servicio'.$x.'" class="form-group">Servicio # '.$x.': </label>
                <select class="form-control" id="servicio'.$x.'" name="servicio'.$x.'" onchange = "no_ingenieros_modificar_ticket_cambio_servicio('.$x.',0)">
                    '.$resto_servicios.'

                    </select>
            </div>
          ';
        }

    }
    $servicios .= '<input type="text" hidden id="no_listas_servicio" value="'.($x - 1).'">';
    
    $i = $x - 1;
    $NoServicios = "";
    for($x = 1; $x <= 4; $x++){
        if($x==$no_servicios){
            $NoServicios .= '<option value = "'.$x.'"  selected>'.$x.'</option>';
        } else {
            $NoServicios .= '<option value = "'.$x.'" >'.$x.'</option>';
        }
    }
        
    $ticket_actual [0]['NoIngenieros'] =$NoIngenieros;
    $ticket_actual [0]['NoServicios'] =$NoServicios;
    $ticket_actual [0]['Ingenieros'] = $ingenieros;
    $ticket_actual [0]['Mesas'] = $mesas;
    $ticket_actual [0]['Dependencias'] = $dependencias;
    $ticket_actual [0]['Servicios'] = $servicios;
    $ticket_actual [0]['Medios'] = $medios;
    $ticket_actual [0]['Estatus'] = $estatus;
    $ticket_actual [0]['Duracion'] = $duracion;
    disconnect($conexion);

    return $ticket_actual;
}

function buscar_serie($serie){
    $conexion = connect();
    $query="SELECT D.Id_dispositivo, D.No_serie_equipo, C.Nombre as 'NombreC', T.Nombre as 'NombreT', M.Nombre as 'NombreM', MO.Nombre as 'NombreMO', DE.Razon_social as 'NombreME' FROM Dispositivos D, Clases_de_dispositivos C, Tipo_de_dispositivos T, Marca_de_dispositivos M, Modelos_de_dispositivos MO, Mesas ME, Destino DE  WHERE D.Id_tipo_dispositivo = T.Id_tipo_dispositivo AND T.Id_clase_dispositivo = C.Id_clase_dispositivo AND D.Id_marca_dispositivo = M.Id_marca_dispositivo AND D.Id_modelo_dispositivo = MO.Id_modelo_dispositivo AND D.Id_mesa = ME.Id_mesa AND ME.Id_destino = DE.Id_destino AND D.No_serie_equipo = '".$serie."';";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows = $row;
    }
    mysqli_free_result($results);
    if(isset($rows['Id_dispositivo'])){
        $query="SELECT D.Razon_social as 'NombreD' FROM Destinos_tickets_dispositivos DTD, Destino D WHERE DTD.Id_destino = D.Id_destino AND DTD.Id_dispositivo = '".$rows['Id_dispositivo']."' ORDER BY Fecha_hora DESC LIMIT 1";
        $results = mysqli_query($conexion, $query);
        $row = mysqli_fetch_array($results,MYSQLI_BOTH);

        $rows['NombreD']=$row['NombreD'];
        mysqli_free_result($results);
        return $rows;
    }
    disconnect($conexion);

}

function registrar_movimiento($destino,$ticket,$dispositivo){
    $conexion = connect();
    $query= "SELECT Id_destino FROM Destino WHERE Razon_social = '".$destino."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows = $row;
    }
    $Id_destino = $rows['Id_destino'];

    if ($ticket != NULL){
        $query= "SELECT Id_ticket FROM Tickets WHERE No_ticket = ".$ticket;
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            $rows = $row;
        }
        $Id_ticket = $rows['Id_ticket'];
    }
    $query= "SELECT Id_dispositivo FROM Dispositivos WHERE No_serie_equipo = '".$dispositivo."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows = $row;
    }
    $Id_dispositivo = $rows['Id_dispositivo'];


    if ($ticket != NULL){
        $query= "INSERT INTO Destinos_tickets_dispositivos (Id_destino, Id_ticket, Id_dispositivo)
        VALUES (".$Id_destino.", ".$Id_ticket.", ".$Id_dispositivo.")";
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
            return $resultado;
        } else {
            $resultado = "Error: " . $query . "<br>" . $conexion->error;
            return $resultado;
        }
    } else {
        $query= "INSERT INTO Destinos_tickets_dispositivos (Id_destino, Id_dispositivo)
        VALUES (".$Id_destino." , ".$Id_dispositivo.")";
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
            return $resultado;
        } else {
            $resultado = "Error: " . $query . "<br>" . $conexion->error;
            return $resultado;
        }
    }
    disconnect($conexion);
}

function buscar_usuario($buscar){
    $conexion = connect();
    $query="SELECT U.Id_usuario, U.Nombre_de_usuario AS 'Nombre_usuario', U.Nombre AS 'Nombre', U.Apellido1, U.RFC, R.Nombre AS 'ROL' FROM Usuarios U, Ultimo_rol_por_usuario UR, Roles R WHERE U.Id_usuario = UR.Id_usuario AND UR.Id_rol = R.Id_rol AND U.Visible = TRUE AND (U.Nombre LIKE '%".$buscar."%' OR U.Apellido1 LIKE '%".$buscar."%' OR U.Apellido2 LIKE '%".$buscar."%' OR concat(U.Nombre, ' ', U.Apellido1, ' ', U.Apellido2) LIKE '%".$buscar."%' OR U.Nombre_de_usuario LIKE '%".$buscar."%' OR U.RFC LIKE '%".$buscar."%' OR R.Nombre LIKE '%".$buscar."%')";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows [$i]['Nombre_usuario'] = $row['Nombre_usuario'];
        $rows [$i]['Id_usuario'] = $row['Id_usuario'];
        $rows [$i]['RFC'] = $row['RFC'];
        $rows [$i]['ROL'] = $row['ROL'];
        $rows [$i]['Nombre'] = $row['Nombre'];
        $rows [$i]['Apellido1'] = $row['Apellido1'];
        $i++;
    }
    mysqli_free_result($results);
    $tabla = '<div class="table-responsive col-12">
            <table class="table table-hover table-responsive-xl">
                <thead>
                    <tr class="text-center">
                        <th>Nombre de Usuario</th>
                        <th>Nombre Completo</th>
                        <th>RFC</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>';
    for($x = 0; $x < $i; $x++){
        $link = "'./_modificar_usuario.php?Id_usuario=".$rows [$x]['Id_usuario']."&buscar=".$buscar."'";
        $tabla .= '
            <tr class="row_buscar_dispositivo" onclick="window.location.href='.$link.'">
                <td>'.$rows [$x]['Nombre_usuario'].'</td>
                <td>'.$rows [$x]['Nombre'].' '.$rows [$x]['Apellido1'].'</td>
                <td>'.$rows [$x]['RFC'].'</td>
                <td>'.$rows [$x]['ROL'].'</td>
            </tr>
      ';
    }
    $tabla .= '</tbody> </table> </div>';
    disconnect($conexion);

    return $tabla;
}

function consultar_usuario($id,$disabled){
    $conexion = connect();
    $query="SELECT U.Nombre, U.Apellido1, U.Apellido2, U.RFC, U.Nombre_de_usuario, U.Calle, U.Numero_exterior, U.Numero_interior, U.Ciudad, U.Estado, U.CP, R.Nombre as 'Rol', R.Id_rol, U.Foto FROM Usuarios U, Ultimo_rol_por_usuario UR, Roles R WHERE U.Id_usuario = UR.Id_usuario AND UR.Id_rol = R.Id_rol AND U.Id_usuario = ".$id.";";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows ['Nombre'] = $row['Nombre'];
        $rows ['Apellido1'] = $row['Apellido1'];
        $rows ['Apellido2'] = $row['Apellido2'];
        $rows ['RFC'] = $row['RFC'];
        $rows ['Nombre_de_usuario'] = $row['Nombre_de_usuario'];
        $rows ['Calle'] = $row['Calle'];
        $rows ['Numero_exterior'] = $row['Numero_exterior'];
        $rows ['Numero_interior'] = $row['Numero_interior'];
        $rows ['Ciudad'] = $row['Ciudad'];
        $rows ['Estado'] = $row['Estado'];
        $rows ['CP'] = $row['CP'];
        $rows ['Rol'] = $row['Rol'];
        $rows ['Foto'] = $row['Foto'];
        $rows ['Id_rol'] = $row['Id_rol'];
        $i++;
    }
    mysqli_free_result($results);
    if($disabled == 1){$disabled ='disabled';}else{$disabled ='';};
    $rows ['Roles'] = '<select class="form-control" id="exampleFormControlSelect1" name = "Rol" '.$disabled.'> <option id =" '.$rows ['Id_rol'].'"> '.$rows ['Rol'].'</option>';
    $query="SELECT R.Nombre, R.Id_rol FROM Roles R WHERE R.Id_rol !='".$rows ['Id_rol']."'";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows ['Roles'] .='<option id =" '.$row ['Id_rol'].'"> '.$row ['Nombre'].'</option>';
    }
    $rows ['Roles'] .= '</select>';
    disconnect($conexion);
    return $rows;
}

function modificar_usuario($Id_usuario,$Nombre_de_usuario,$Nombre,$Apellido1,$Apellido2,$RFC,$Estado,$Ciudad,$Calle,$Numero_exterior,$Numero_interior,$CP,$Foto,$Rol){
    $conexion = connect();
    /*$query = "SELECT Nombre_de_usuario, Id_usuario FROM Usuarios WHERE Nombre_de_usuario ='".$Nombre_de_usuario."' AND Id_usuario != ".$Id_usuario;
    $results = mysqli_query($conexion, $query);
    $rows = [];
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows = $row;
    }
    mysqli_free_result($results);
    if(isset($rows['Nombre_de_usuario']) && $Nombre_de_usuario === $rows['Nombre_de_usuario']){
        $resultado = "Usuario ya existente";
        disconnect($conexion);
        return $resultado;
    }*/
    if ($Foto == "no_modificar"){
        // $fecha = $fecha->getTimestamp();
        $fecha = "";
        $query = 'UPDATE Usuarios SET Nombre = "'.$Nombre.'", Apellido1 = "'.$Apellido1.'", Apellido2 = "'.$Apellido2.'", RFC = "'.$RFC.'", Nombre_de_usuario = "'.$Nombre_de_usuario.'", Calle = "'.$Calle.'", Numero_exterior = "'.$Numero_exterior.'", Numero_interior = "'.$Numero_interior.'", Ciudad = "'.$Ciudad.'", Estado = "'.$Estado.'", CP = "'.$CP.'", Ultima_actualizacion = "CURRENT_TIMESTAMP" WHERE Id_usuario = '.$Id_usuario;
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
            return $resultado;
        }
        $query = "SELECT Id_rol FROM Ultimo_rol_por_usuario WHERE Id_usuario = '".$Id_usuario."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            $rows = $row;
        }
        mysqli_free_result($results);
        $Id_rol_anterior = $rows['Id_rol'];
        $query = "SELECT Id_rol FROM Roles WHERE Nombre = '".$Rol."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            $rows = $row;
        }
        mysqli_free_result($results);
        $Id_rol = $rows['Id_rol'];

        $query = "INSERT INTO Usuarios_Roles (Id_rol,Id_usuario) VALUES (".$Id_rol.",".$Id_usuario.")";
        if ($conexion->query($query) === TRUE) {
            $resultado = $resultado * TRUE;
        } else {
            $resultado = $resultado * FALSE;
            return $resultado;
        }

        $query = "SELECT Id_permiso FROM Permisos";
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $todos_los_permisos [$i]= $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);
        $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$Id_rol_anterior;
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $permisos_usuario_anterior [$i] = $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);

        foreach ($todos_los_permisos as $x){
            foreach ($permisos_usuario_anterior as $j){
                if ($x == $j ){
                    $permisos_anterior [$x] = TRUE;
                    break;
                } else {
                    $permisos_anterior[$x] = FALSE;
                }
            }

        }

        $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$Id_rol;
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $permisos_usuario_nuevo [$i] = $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);

        foreach ($todos_los_permisos as $x){
            foreach ($permisos_usuario_nuevo as $j){
                if ($x == $j ){
                    $permisos_nuevo [$x] = TRUE;
                    break;
                } else {
                    $permisos_nuevo [$x] = FALSE;
                }
            }

        }

        if ($permisos_anterior[18] == TRUE){
            $query = "UPDATE Ingenieros SET Visible = 0 WHERE Id_usuario = ".$Id_usuario;
            if ($conexion->query($query) === TRUE) {
                $resultado = $resultado * TRUE;
            } else {
                $resultado = $resultado * FALSE;
                return $resultado;
            }
        }
        if ($permisos_nuevo[18] == TRUE){
            $query = "SELECT Id_ingeniero FROM Ingenieros WHERE Id_usuario=".$Id_usuario;
            $results = mysqli_query($conexion, $query);
            while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                $existe = $row['Id_ingeniero'];
            }
            if (isset($existe) && $existe!="" && $existe!=NULL){
                $query = "UPDATE Ingenieros SET Visible = 1 WHERE Id_usuario = ".$Id_usuario;
                if ($conexion->query($query) === TRUE) {
                    $resultado = $resultado * TRUE;
                } else {
                    $resultado = $resultado * FALSE;
                    return $resultado;
                }
            } else {
                $query = "INSERT INTO Ingenieros (Id_usuario) VALUES (".$Id_usuario.")";
                if ($conexion->query($query) === TRUE) {
                    $resultado = $resultado * TRUE;
                } else {
                    $resultado = $resultado * FALSE;
                    return $resultado;
                }
            }
        }

    } else {
 
        // $fecha = $fecha->getTimestamp();
        $fecha = "";
        $query = 'UPDATE Usuarios SET Nombre = "'.$Nombre.'", Apellido1 = "'.$Apellido1.'", Apellido2 = "'.$Apellido2.'", RFC = "'.$RFC.'", Nombre_de_usuario = "'.$Nombre_de_usuario.'", Calle = "'.$Calle.'", Numero_exterior = "'.$Numero_exterior.'", Numero_interior = "'.$Numero_interior.'", Ciudad = "'.$Ciudad.'", Estado = "'.$Estado.'", CP = "'.$CP.'", Foto = "'.$Foto.'", Ultima_actualizacion = "CURRENT_TIMESTAMP" WHERE Id_usuario = '.$Id_usuario;
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
            return $resultado;
        }
        $query = "SELECT Id_rol FROM Ultimo_rol_por_usuario WHERE Id_usuario = '".$Id_usuario."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            $rows = $row;
        }
        mysqli_free_result($results);
        $Id_rol_anterior = $rows['Id_rol'];
        $query = "SELECT Id_rol FROM Roles WHERE Nombre = '".$Rol."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
        {
            $rows = $row;
        }
        mysqli_free_result($results);
        $Id_rol = $rows['Id_rol'];

        $query = "INSERT INTO Usuarios_Roles (Id_rol,Id_usuario) VALUES (".$Id_rol.",".$Id_usuario.")";
        if ($conexion->query($query) === TRUE) {
            $resultado = $resultado * TRUE;
        } else {
            $resultado = $resultado * FALSE;
            return $resultado;
        }

        $query = "SELECT Id_permiso FROM Permisos";
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $todos_los_permisos [$i]= $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);
        $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$Id_rol_anterior;
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $permisos_usuario_anterior [$i] = $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);

        foreach ($todos_los_permisos as $x){
            foreach ($permisos_usuario_anterior as $j){
                if ($x == $j ){
                    $permisos_anterior [$x] = TRUE;
                    break;
                } else {
                    $permisos_anterior[$x] = FALSE;
                }
            }

        }

        $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$Id_rol;
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $permisos_usuario_nuevo [$i] = $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);

        foreach ($todos_los_permisos as $x){
            foreach ($permisos_usuario_nuevo as $j){
                if ($x == $j ){
                    $permisos_nuevo [$x] = TRUE;
                    break;
                } else {
                    $permisos_nuevo [$x] = FALSE;
                }
            }

        }

        if ($permisos_anterior[18] == TRUE){
            $query = "UPDATE Ingenieros SET Visible = 0 WHERE Id_usuario = ".$Id_usuario;
            if ($conexion->query($query) === TRUE) {
                $resultado = $resultado * TRUE;
            } else {
                $resultado = $resultado * FALSE;
                return $resultado;
            }
        }
        if ($permisos_nuevo[18] == TRUE){
            $query = "SELECT Id_ingeniero FROM Ingenieros WHERE Id_usuario=".$Id_usuario;
            $results = mysqli_query($conexion, $query);
            while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                $existe = $row['Id_ingeniero'];
            }
            if (isset($existe) && $existe!="" && $existe!=NULL){
                $query = "UPDATE Ingenieros SET Visible = 1 WHERE Id_usuario = ".$Id_usuario;
                if ($conexion->query($query) === TRUE) {
                    $resultado = $resultado * TRUE;
                } else {
                    $resultado = $resultado * FALSE;
                    return $resultado;
                }
            } else {
                $query = "INSERT INTO Ingenieros (Id_usuario) VALUES (".$Id_usuario.")";
                if ($conexion->query($query) === TRUE) {
                    $resultado = $resultado * TRUE;
                } else {
                    $resultado = $resultado * FALSE;
                    return $resultado;
                }
            }
        }

        
    }
    disconnect($conexion);
    return $resultado;
}

function consultar_estatus($id){
    $conexion = connect();
    $query= "SELECT T.No_ticket, E.Nombre as 'NombreE' FROM Tickets T, Ultimo_estatus_ticket UE, Estatus_de_tickets E WHERE T.Id_ticket = UE.Id_ticket AND UE.Id_estatus = E.Id_estatus AND T.Id_ticket =".$id;
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows ['NombreE'] = $row['NombreE'];
    }
    mysqli_free_result($results);
    $estatus_actual = $rows ['NombreE'];
    $rows = '<select type="list" class="form-control" id="Estatus" name="Estatus"><option>'.$estatus_actual.'</option>';
    $query="SELECT E.Nombre as 'NombreE' FROM Estatus_de_tickets E WHERE E.Nombre != '".$estatus_actual."'";
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows .= '<option>'.$row['NombreE'].'</option>';
    }
    $rows .= '</select>';
    mysqli_free_result($results);
    return $rows;
    disconnect($conexion);
}

function enviar_diagnostico($Id_ticket,$NombreE,$ComentarioD){
    $conexion = connect();
    if ($ComentarioD != ""){
        $query= "UPDATE Tickets SET Comentario_diagnostico = '".$ComentarioD."', Fecha_y_hora_de_envio_de_diagnostico =  CURRENT_TIMESTAMP WHERE Id_ticket = ".$Id_ticket;
        if ($conexion->query($query) === TRUE) {
            $resultado [1] = TRUE;
        } else {
            $resultado [1] = FALSE;
            $resultado = "Error: " . $query . "<br>" . $conexion->error;
        }
    }

    $query = 'SELECT Id_estatus FROM Estatus_de_tickets WHERE Nombre = "'.$NombreE.'"';
    $results = mysqli_query($conexion, $query);
    $rows = [];
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows['Id_estatus'] = $row['Id_estatus'];
    }
    $Id_estatus = $rows['Id_estatus'];
    $query = 'INSERT INTO Tickets_Estatus_de_tickets (Id_estatus, Id_ticket) VALUES ('.$Id_estatus.','.$Id_ticket.')';
    if ($conexion->query($query) === TRUE) {
        $resultado [1] = TRUE;
    } else {
        $resultado [1]= FALSE;
        return $resultado;
    }
    $query = 'SELECT Fecha_hora FROM Ultimo_estatus_ticket WHERE Id_ticket = '.$Id_ticket;
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $resultado ['Fecha_hora_actualizacion'] = $row['Fecha_hora'];

    }
    mysqli_free_result($results);
    return $resultado;
    disconnect($conexion);
}

function modificar_ticket($Id_ticket,$No_ticket,$NombreE,$NombreM,$NombreD,$Duracion,$Fecha_y_hora_de_inicio_programada,$NombreMT,$Comentario, $Nombre_servicio, $Nombre_cat, $Nombre_inge){
    $conexion = connect();
    $query = "SELECT Id_estatus FROM Estatus_de_tickets WHERE Nombre = '".$NombreE."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $Id_estatus = $row['Id_estatus'];
    }
    mysqli_free_result($results);

    $query = "SELECT Id_medio FROM Medios_de_transporte WHERE Nombre = '".$NombreMT."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $Id_medio = $row['Id_medio'];
    }
    mysqli_free_result($results);

    

    $query = "SELECT D.Id_dependencia FROM Dependencias D, Destino DE WHERE D.Id_destino = DE.Id_destino AND DE.Razon_social = '".$NombreD."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $Id_dependencia = $row['Id_dependencia'];
    }
    mysqli_free_result($results);
    
    $Duracion = floatval(str_replace(" horas","",$Duracion));
    
    $query="UPDATE Tickets SET No_ticket ='".$No_ticket."', Fecha_y_hora_de_inicio_programada = '".$Fecha_y_hora_de_inicio_programada."', Duracion_estimada_por_admin = '".$Duracion."', Comentario_inicial = '".$Comentario."', Id_medio = ".$Id_medio.", Id_dependencia = ".$Id_dependencia." WHERE Id_ticket = ".$Id_ticket;
    if ($conexion->query($query) === TRUE) {
        $resultado[1] = TRUE;
    } else {
        $resultado[1] = FALSE;
    }
    $query = "INSERT INTO Tickets_Estatus_de_tickets (Id_estatus, Id_ticket) VALUES (".$Id_estatus.",".$Id_ticket.")";
    if ($conexion->query($query) === TRUE) {
        $resultado [1] = $resultado[1] * TRUE;
    } else {
        $resultado [1] = $resultado[1] * FALSE;
    }

    $query = 'SELECT Fecha_hora FROM Ultimo_estatus_ticket WHERE Id_ticket = '.$Id_ticket;
    $results = mysqli_query($conexion, $query);
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $resultado ['Fecha_hora_actualizacion'] = $row['Fecha_hora'];

    }
    mysqli_free_result($results);
    
    $query = "DELETE FROM Ingenieros_Tickets WHERE Id_ticket = ".$Id_ticket;
    if ($conexion->query($query) === TRUE) {
        $resultado [1] = $resultado[1] * TRUE;
    } else {
        $resultado [1] = $resultado[1] * FALSE;
    }
    
    foreach ($Nombre_inge as $a){
        $query = "SELECT I.Id_ingeniero FROM Usuarios U, Ingenieros I WHERE I.Id_usuario = U.Id_usuario AND concat(U.Nombre, ' ', U.Apellido1, ' ', U.Apellido2) = '".$a."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $Id_ingeniero = $row['Id_ingeniero'];
        }
        mysqli_free_result($results);
        $query = "INSERT INTO Ingenieros_Tickets (Id_ingeniero, Id_ticket) VALUES (".$Id_ingeniero.",".$Id_ticket.")";
        if ($conexion->query($query) === TRUE) {
            $resultado [1] = $resultado[1] * TRUE;
        } else {
            $resultado [1] = $resultado[1] * FALSE;
        }
    }
    $query = "DELETE FROM Catalogo_de_servicios_Tickets WHERE Id_ticket = ".$Id_ticket;
    if ($conexion->query($query) === TRUE) {
        $resultado [1] = $resultado[1] * TRUE;
    } else {
        $resultado [1] = $resultado[1] * FALSE;
    }
    foreach ($Nombre_servicio as $a){
        $query = "SELECT S.Id_trabajo FROM Catalogo_de_servicios S WHERE S.Nombre = '".$a."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        $i=0;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $Id_trabajo = $row['Id_trabajo'];
        }
        mysqli_free_result($results);
        $query = "INSERT INTO Catalogo_de_servicios_Tickets (Id_trabajo, Id_ticket) VALUES (".$Id_trabajo.",".$Id_ticket.")";
        if ($conexion->query($query) === TRUE) {
            $resultado [1] = $resultado[1] * TRUE;
        } else {
            $resultado [1] = $resultado[1] * FALSE;
        }
    }
    return $resultado;
    disconnect($conexion);
}

function cambiar_contrasena($contrasena_actual,$Id_usuario,$contrasena_nueva){
    $conexion = connect();
    $contrasena_actual = hash("sha256",$contrasena_actual);
    $contrasena_nueva = hash("sha256",$contrasena_nueva);
    $query="SELECT Contraseña FROM Usuarios WHERE Id_usuario =".$Id_usuario;
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $Contraseña = $row['Contraseña'];
    }
    mysqli_free_result($results);
    if ($contrasena_actual == $Contraseña){
        $query="UPDATE Usuarios SET Contraseña ='".$contrasena_nueva."' WHERE Id_usuario = ".$Id_usuario;
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
        } else {
            $resultado = 'Falló la conexion';
        }
    } else {
        $resultado = "Contraseña incorrecta";
    }
    return $resultado;
    disconnect($conexion);
}
function corroborar_contrasena($contrasena_actual,$usuario){
    $conexion = connect();
    $contrasena_actual = hash("sha256",$contrasena_actual);
    $query = "SELECT U.Id_usuario, U.Nombre as 'NombreU', U.Apellido1, U.Apellido2, R.Nombre as 'NombreR', R.Id_rol FROM Usuarios U, Ultimo_rol_por_usuario UR, Roles R WHERE U.Id_usuario = UR.Id_Usuario AND UR.Id_rol = R.Id_rol AND U.Visible = 1 AND U.Nombre_de_usuario = '".$usuario."'";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado[0]['Id_usuario'] = $row['Id_usuario'];
        $resultado[0]['NombreU'] = $row['NombreU'];
        $resultado[0]['Apellido1'] = $row['Apellido1'];
        $resultado[0]['Apellido2'] = $row['Apellido2'];
        $resultado[0]['NombreR'] = $row['NombreR'];
        $resultado[0]['Id_rol'] = $row['Id_rol'];
    }
    mysqli_free_result($results);
    if(isset($resultado[0]['Id_usuario'])){
        $query = "SELECT Id_permiso FROM Permisos";
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $todos_los_permisos [$i]= $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);
        $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$resultado[0]['Id_rol'];
        $results = mysqli_query($conexion, $query);
        $i=1;
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
            $permisos_usuario [$i] = $row ['Id_permiso'];
            $i++;
        }
        mysqli_free_result($results);

        foreach ($todos_los_permisos as $x){
            foreach ($permisos_usuario as $j){
                if ($x == $j ){
                    $resultado ['Permisos'] [$x] = TRUE;
                    break;
                } else {
                    $resultado ['Permisos'] [$x] = FALSE;
                }
            }

        }
        if(isset($resultado)){
            $query="SELECT Contraseña FROM Usuarios WHERE Id_usuario =".$resultado[0]['Id_usuario'];
            $results = mysqli_query($conexion, $query);
            $i=0;
            while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                $Contraseña = $row['Contraseña'];
            }
            mysqli_free_result($results);
            if ($contrasena_actual == $Contraseña){
                $resultado['validacion'] = TRUE;
            } else {
                $resultado['validacion'] = FALSE;
            }
        } else {
            $resultado['validacion'] = FALSE;
        }
    } else {
        $resultado['validacion'] = FALSE;
    }
    return $resultado;
    disconnect($conexion);
}

function eliminar_usuario($Id_usuario) {
    $conexion = connect();
    $query = "UPDATE Usuarios SET Visible = FALSE WHERE Id_Usuario = ".$Id_usuario;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    $query = "SELECT Id_rol FROM Ultimo_rol_por_usuario WHERE Id_usuario = '".$Id_usuario."'";
    $results = mysqli_query($conexion, $query);
    $rows = [];
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
    {
        $rows = $row;
    }
    mysqli_free_result($results);
    $Id_rol_anterior = $rows['Id_rol'];
    $query = "SELECT Id_permiso FROM Permisos";
    $results = mysqli_query($conexion, $query);
    $i=1;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $todos_los_permisos [$i]= $row ['Id_permiso'];
        $i++;
    }
    mysqli_free_result($results);
    $query = "SELECT Id_permiso FROM Roles_Permisos RP, Roles R WHERE R.Id_rol = RP.Id_rol AND R.Id_rol = ".$Id_rol_anterior;
    $results = mysqli_query($conexion, $query);
    $i=1;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $permisos_usuario_anterior [$i] = $row ['Id_permiso'];
        $i++;
    }
    mysqli_free_result($results);

    foreach ($todos_los_permisos as $x){
        foreach ($permisos_usuario_anterior as $j){
            if ($x == $j ){
                $permisos_anterior [$x] = TRUE;
                break;
            } else {
                $permisos_anterior[$x] = FALSE;
            }
        }

    }
    if ($permisos_anterior[18] === TRUE){
        $query = "UPDATE Ingenieros SET Visible = FALSE WHERE Id_Usuario = ".$Id_usuario;
        if ($conexion->query($query) === TRUE) {
            $resultado = $resultado * TRUE;
        } else {
            $resultado = $resultado * FALSE;
        }
    }
    disconnect($conexion);
    return $resultado;
    
}

function restablecer_contrasena($Id_usuario){
    $conexion = connect();
    $query = "UPDATE Usuarios SET Contraseña = '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918' WHERE Id_Usuario = ".$Id_usuario;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}

function buscar_marca($nombreM){
    $conexion = connect();
    $query = "SELECT M.Nombre, M.Descripcion, M.Id_marca_dispositivo FROM Marca_de_dispositivos M WHERE M.Visible = TRUE AND (M.Nombre LIKE '%".$nombreM."%' OR M.Descripcion LIKE '%".$nombreM."%') ORDER BY M.Nombre";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows['Id_marca_dispositivo'] [$i] = $row['Id_marca_dispositivo'];
        $rows['Nombre'] [$i] = $row['Nombre'];
        if(strlen($row['Descripcion'])>50){
            $rows['Descripcion'] [$i] = substr($row['Descripcion'],0,50)."...";
        }else{
        $rows['Descripcion'] [$i] = $row['Descripcion'];
        }
        $i++;
    }
    mysqli_free_result($results);
    if (isset($rows['Id_marca_dispositivo'] [0]) && $rows['Id_marca_dispositivo'] [0] != ""){
    $tabla = '
        <div class="form-text col">
            <h4>Marcas de Dispositivos</h4>
        </div><br>
        <div class="table-responsive col-12">
        <table class="table table-hover table-responsive-xl">
            <thead>
                <tr class="text-center">
                    <th>Nombre</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>';
    $link = 'window.location.href="./_modificar_marcadispositivos.php?buscar='.$nombreM.'&Id_marca_dispositivo=';
    $i=0;
    foreach ($rows['Nombre'] as $a){
        $tabla .= '
        <tr class="row_buscar_dispositivo text-center" onclick='.$link.$rows['Id_marca_dispositivo'][$i].'">
            <td>'.$rows['Nombre'][$i].'</td>
            <td>'.$rows['Descripcion'][$i].'</td>
        </tr>';
        $i++;
    }
    $tabla .= '</tbody></table></div>';
    } else {
        $tabla = '<div class = "error"> *Sin resultados en la base de datos';
    }
    disconnect($conexion);
    return $tabla;
}

function consultar_marca($Id_marca_dispositivo){
    $conexion = connect();
    $query = "SELECT M.Nombre, M.Descripcion FROM Marca_de_dispositivos M WHERE M.Id_marca_dispositivo = '".$Id_marca_dispositivo."' AND M.Visible = TRUE";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Descripcion'] [$i] = $row['Descripcion'];
        $i++;
    }
    mysqli_free_result($results);

    disconnect($conexion);
    return $resultado;
}

function modificar_marca($Id_marca_dispositivo, $NombreM, $DescripcionM){
    $conexion = connect();
    $query = "UPDATE Marca_de_dispositivos SET Nombre = '".$NombreM."', Descripcion = '".$DescripcionM."' WHERE Id_marca_dispositivo = ".$Id_marca_dispositivo;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
function eliminar_marca($Id_marca_dispositivo){
    $conexion = connect();
    $query = "UPDATE Marca_de_dispositivos SET Visible = 0 WHERE Id_marca_dispositivo = ".$Id_marca_dispositivo;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}

function eliminar_movimiento($Id_destino, $Id_dispositivo, $Fecha_hora){
    $conexion = connect();
    $query = "DELETE FROM Destinos_tickets_dispositivos WHERE Id_destino = ".$Id_destino." AND Id_dispositivo = ".$Id_dispositivo." AND Fecha_hora = '".$Fecha_hora."'";
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}

function fecha_hora_eliminar_movimiento($Id_dispositivo){
    $conexion = connect();
    $query="SELECT D.Razon_social as 'NombreD', DTD.Fecha_hora FROM Destinos_tickets_dispositivos DTD, Destino D WHERE DTD.Id_destino = D.Id_destino AND DTD.Id_dispositivo = '".$Id_dispositivo."' ORDER BY Fecha_hora DESC LIMIT 1";
    $results = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($results,MYSQLI_BOTH);

    $rows['Fecha_hora']=$row['Fecha_hora'];
    mysqli_free_result($results);
    disconnect($conexion);
    return $rows['Fecha_hora'];
}

function buscar_manual($buscar){
    $conexion = connect();
    $query = "SELECT M.Nombre, M.Id_manual, M.Version, M.Descripcion FROM Catalogo_de_servicios_Manuales SM, Catalogo_de_servicios S, Manuales M WHERE SM.Id_trabajo = S.Id_trabajo AND SM.Id_manual = M.Id_manual AND (M.Nombre LIKE '%".$buscar."%' OR S.Nombre LIKE '%".$buscar."%' OR S.Descripcion LIKE '%".$buscar."%' OR M.Version LIKE '%".$buscar."%' OR M.Descripcion LIKE '%".$buscar."%') GROUP BY M.Id_manual";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $rows['Id_manual'] [$i] = $row['Id_manual'];
        $rows['Nombre'] [$i] = $row['Nombre'];
        $rows['Version'] [$i] = $row['Version'];
        if(strlen($row['Descripcion'])>50){
            $rows['Descripcion'] [$i] = substr($row['Descripcion'],0,50)."...";
        }else{
        $rows['Descripcion'] [$i] = $row['Descripcion'];
        }
        $i++;
    }
    mysqli_free_result($results);
    if (isset($rows['Id_manual'] [0]) && $rows['Id_manual'] [0] != ""){
    $tabla = '
        <div class="form-text col">
            <h4>Manuales</h4>
        </div><br>
        <div class="table-responsive col-12">
        <table class="table table-hover table-responsive-xl">
            <thead>
                <tr class="text-center">
                    <th>Nombre</th>
                    <th>Versión</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>';
    $link = 'window.location.href="./_modificar_manual.php?buscar='.$buscar.'&Id_manual=';
    $i=0;
    foreach ($rows['Nombre'] as $a){
        $tabla .= '
        <tr class="row_buscar_dispositivo text-center" onclick='.$link.$rows['Id_manual'][$i].'">
            <td>'.$rows['Nombre'][$i].'</td>
            <td>'.$rows['Version'][$i].'</td>
            <td>'.$rows['Descripcion'][$i].'</td>
        </tr>';
        $i++;
    }
    $tabla .= '</tbody></table></div>';
    } else {
        $tabla = '<div class = "error"> *Sin resultados en la base de datos';
    }
    
    disconnect($conexion);
    return $tabla;
}

function consultar_manual($Id_manual){
    $conexion = connect();
    $query = "SELECT M.Nombre, M.Descripcion, M.Version, M.Archivo FROM Manuales M WHERE M.Id_manual = '".$Id_manual."'";
    $results = mysqli_query($conexion, $query);
    $i=0;
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        $resultado['Nombre'] [$i] = $row['Nombre'];
        $resultado['Descripcion'] [$i] = $row['Descripcion'];
        $resultado['Version'] [$i] = $row['Version'];
        $resultado['Archivo'] [$i] = $row['Archivo'];
        $resultado['Archivoonclick'] [$i] = "onclick='window.open(";
        $resultado['Archivoonclick'] [$i] .= '"'.$row['Archivo'].'") return true;';
        $resultado['Archivoonclick'] [$i] .= "'";
        $i++;
    }
    mysqli_free_result($results);

    disconnect($conexion);
    return $resultado;
}

function modificar_manual($Id_manual, $NombreManual,$DescripcionManual,$VersionManual, $ManualArchivo){
    $conexion = connect();
    if ($ManualArchivo == "no_modificar"){
        $query = "UPDATE Manuales SET Nombre ='".$NombreManual."', Descripcion ='".$DescripcionManual."', Version = '".$VersionManual."' WHERE Id_manual = ".$Id_manual;
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
        }   
    } else {
         $query = "UPDATE Manuales SET Nombre ='".$NombreManual."', Descripcion ='".$DescripcionManual. "', Version = '".$VersionManual."', Archivo = '".$ManualArchivo."' WHERE Id_manual = ".$Id_manual;
        if ($conexion->query($query) === TRUE) {
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
        }   
    }
    disconnect($conexion);
    return $resultado;
}

function eliminar_manual($Id_manual){
    
    $conexion = connect();
    
    $query = "DELETE FROM Catalogo_de_servicios_Manuales WHERE Id_manual= ".$Id_manual;
    if ($conexion->query($query) === TRUE) {
        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }
    $query = "DELETE FROM Manuales WHERE Id_manual= ".$Id_manual;
    if ($conexion->query($query) === TRUE) {
        $resultado = $resultado * TRUE;
    } else {
        $resultado = $resultado * FALSE;
    }
    
    disconnect($conexion);
    return $resultado;
}
?>
