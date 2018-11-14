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


    function registrar_ticket($opc){
        $conexion = connect();
        switch($opc){
            //CONSULTA LAS MESAS
            case 0:
                $query = ("SELECT NombreM FROM NombresMesas;");

                $results = mysqli_query($conexion, $query);


                $i = 0;
                $rows = [];
                echo '<option value="Seleccione una Mesa...">Seleccione una Mesa...</option>';
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    echo '<option value="'.$row['NombreM'].'">'.$row['NombreM'].'</option>';
                }
                mysqli_free_result($results);
                break;
            //CONSULTA DEPENDENCIA
            case 1:
                $conexion = connect();
                if(isset($_POST['SelMesa'])){
                    $select1 = $_POST['numTicket'];
                }
                echo $select1;
                if($dependencia == "Seleccione una Mesa..."){
                    echo '<option value="">Por favor seleccione antes una Mesa</option>';
                }else{
                    $query = ("SELECT Razon_social AS 'RSOC' FROM Destino DE, Dependencias D, NombresMesas M WHERE M.Id_mesa = D.Id_mesa AND D.Id_destino = DE.Id_destino AND M.NombreM = '".$dependencia."'");
                    $results = mysqli_query($conexion, $query);
                    $i = 0;
                    $rows = [];
                    echo '<option value="">Seleccione una Dependencia...</option>';
                    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                        echo '<option value="'.$row['RSOC'].'">'.$row['RSOC'].'</option>';
                    }
                    mysqli_free_result($results);
                }


                /*
                if(isset($_GET['SelMesa'])){
                    $mesa = $_GET['SelMesa'];
                    //$mesa = "mesa2";
                    $conexion = connect();
                        $query="SELECT Razon_social FROM Destino DE, Dependencias D, NombresMesas M WHERE M.Id_mesa = D.Id_mesa AND D.Id_destino = DE.Id_destino AND M.NombreM = '".$mesa."'";
                        $results = mysqli_query($conexion, $query);
                        $i = 0;
                        $rows = '';
                        while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                            $rows .= '<option value="'.$row['Razon_social'].'">'.$row['Razon_social'].'</option>';
                        }
                        mysqli_free_result($results);
                    disconnect($conexion);
                    $seleccion = $rows;
                    echo $seleccion;
                }
        }*/





                break;


            //CONSULTA CATEGORIAS DE SERVICIO
            case 2:
                $query_cat_ser = ("SELECT Nombre as 'NombreCDS'
                FROM Categoria_de_servicios CDS;") ;
                $results = mysqli_query($conexion, $query_cat_ser);

                $i = 0;
                $rows = [];
                echo '<option value="">Seleccione una categoría de servicio...</option>';
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    echo '<option value="'.$row['NombreCDS'].'">'.$row['NombreCDS'].'</option>';
                }
                mysqli_free_result($results);
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


                $i = 0;
                $rows = [];
                echo '<option value="">Seleccione un transporte...</option>';
                while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
                    echo '<option value="'.$i.'">'.$row['NombreTRA'].'</option>';
                }
                mysqli_free_result($results);
                break;

        }



        disconnect($conexion);
    }


    function registrar_ticket2($no_ticket, $mesa, $dependencia, $cat_servicio, $servicio, $duracion_est, $fecha_inicial, $ingeniero, $transporte, $comentario, $ingeniero2, $ingeniero3, $ingeniero4){
        $conexion = connect();


//Registra en la tabla de tickets
        $query= "SELECT Id_destino FROM Destino WHERE Razon_social = '".$dependencia."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
            {
                $rows = $row;
            }
        $Id_destino = $rows['Id_destino'];

        $query= "SELECT Id_dependencia FROM Dependencias WHERE Id_destino = '".$Id_destino."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
            {
                $rows = $row;
            }
        $Id_dependencia = $rows['Id_dependencia'];

        if($transporte = "Autobús"){
            $id_transporte = 1;
        }else if($transporte = "Automovil"){
            $id_transporte = 2;
        }


        $query = "INSERT INTO Tickets (No_ticket, Fecha_y_hora_de_inicio_programada, Duracion_estimada_por_admin, Comentario_inicial, Id_medio, Id_dependencia, Id_destino)
        VALUES ('".$no_ticket."', '".$fecha_inicial."', '".$duracion_est."', '".$comentario."', ".$id_transporte.", ".$Id_dependencia.", ".$Id_destino.")";

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
        
//Registra el tique a un servicio
        $query = "SELECT Id_trabajo FROM Catalogo_de_servicios CAS WHERE CAS.Nombre = '".$servicio."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
            {
                $rows = $row;
            }
        $id_servicio = $rows['Id_trabajo'];
        
        $query = "INSERT INTO Catalogo_de_servicios_Tickets (Id_trabajo, Id_ticket)
        VALUES (".$id_servicio.", ".$id_tique.")";
        
        $resultado = mysqli_query($conexion, $query);
        
//Asigna los ingenieros a un ticket
        
        $query= "SELECT DISTINCT ING.Id_ingeniero AS 'IDING' FROM Usuarios USR, Ingenieros_Tickets IT, Ingenieros ING WHERE ING.Id_ingeniero = IT.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND USR.Nombre = '".$ingeniero."'";
        $results = mysqli_query($conexion, $query);
        $rows = [];
        while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
            {
                $rows = $row;
            }
        $id_inge = $rows['IDING'];
        
        $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
        VALUES (".$id_tique.", ".$id_inge.")";
        
        $resultado = mysqli_query($conexion, $query);
        
        //Ingeniero 2
        if($ingeniero2 != "NULL"){
            echo $ingeniero2;
            $query= "SELECT DISTINCT ING.Id_ingeniero AS 'IDING2' FROM Usuarios USR, Ingenieros_Tickets IT, Ingenieros ING WHERE ING.Id_ingeniero = IT.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND USR.Nombre = '".$ingeniero2."'";
            $results = mysqli_query($conexion, $query);
            $rows = [];
            while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
                {
                    $rows = $row;
                }
            $id_inge2 = $rows['IDING2'];

            $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
            VALUES (".$id_tique.", ".$id_inge2.")";
            
            $resultado = mysqli_query($conexion, $query);
        }
        
        //Ingeniero 3
        if($ingeniero3 != NULL){
            $query= "SELECT DISTINCT ING.Id_ingeniero AS 'IDING3' FROM Usuarios USR, Ingenieros_Tickets IT, Ingenieros ING WHERE ING.Id_ingeniero = IT.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND USR.Nombre = '".$ingeniero3."'";
            $results = mysqli_query($conexion, $query);
            $rows = [];
            while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
                {
                    $rows = $row;
                }
            $id_inge3 = $rows['IDING3'];

            $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
            VALUES (".$id_tique.", ".$id_inge3.")";
            
            $resultado = mysqli_query($conexion, $query);
        }

        //Ingeniero 4
        if($ingeniero4 != NULL){
            $query= "SELECT DISTINCT ING.Id_ingeniero AS 'IDING4' FROM Usuarios USR, Ingenieros_Tickets IT, Ingenieros ING WHERE ING.Id_ingeniero = IT.Id_ingeniero AND ING.Id_usuario = USR.Id_usuario AND USR.Nombre = '".$ingeniero4."'";
            $results = mysqli_query($conexion, $query);
            $rows = [];
            while($row = mysqli_fetch_array($results,MYSQLI_BOTH))
                {
                    $rows = $row;
                }
            $id_inge4 = $rows['IDING4'];

            $query = "INSERT INTO Ingenieros_Tickets (Id_ticket, Id_ingeniero)
            VALUES (".$id_tique.", ".$id_inge4.")";
            
            $resultado = mysqli_query($conexion, $query);
        }
        
        return $resultado;
        disconnect($conexion);
    }
?>
