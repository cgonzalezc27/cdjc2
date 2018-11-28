<?php
    $menu = "tickets";
    session_start();
    require_once('./model_car.php');
    if (isset($_SESSION["Usuario"])){
        if($_SESSION["Permisos"][5] == TRUE){
            include '../html/_header.html';
            include '../html/_menu.html';       
        
        



    

            if (isset($_POST["transporte"])) {

                $no_ticket = $_POST["numTicket"];
                $mesa = $_POST["SelMesa"];
                $dependencia = $_POST["dependencia"];
                $cat_ser = $_POST["cateSer"];
                $servicio = $_POST["Ser"];
                $duracion_est = $_POST["duracion"];
                $fecha_inicial = $_POST["fecha_inicio"];
                $ingeniero = $_POST["Ing"];
                $transporte = $_POST["transporte"];
                if(isset($_POST["coment"])){
                    $comentario = $_POST["coment"];
                }else{
                    $comentario = NULL;
                }
                if(isset($_POST["Ing2"])){
                    $ingeniero2 = $_POST["Ing2"];
                }else{
                    $ingeniero2 = NULL;
                }
                if(isset($_POST["Ing3"])){
                    $ingeniero3 = $_POST["Ing3"];
                }else{
                    $ingeniero3 = NULL;
                }
                if(isset($_POST["Ing4"])){
                    $ingeniero4 = $_POST["Ing4"];
                }else{
                    $ingeniero4 = NULL;
                }
                if(isset($_POST["cateSer2"])){
                    $cateSer2 = $_POST["cateSer2"];
                }else{
                    $cateSer2 = NULL;
                }
                if(isset($_POST["cateSer3"])){
                    $cateSer3 = $_POST["cateSer3"];
                }else{
                    $cateSer3 = NULL;
                }
                if(isset($_POST["cateSer4"])){
                    $cateSer4 = $_POST["cateSer4"];
                }else{
                    $cateSer4 = NULL;
                }
                if(isset($_POST["Ser2"])){
                    $Ser2 = $_POST["Ser2"];
                }else{
                    $Ser2 = NULL;
                }
                if(isset($_POST["Ser3"])){
                    $Ser3 = $_POST["Ser3"];
                }else{
                    $Ser3 = NULL;
                }
                if(isset($_POST["Ser4"])){
                    $Ser4 = $_POST["Ser4"];
                }else{
                    $Ser4 = NULL;
                }



                $resultado = registrar_ticket2($no_ticket, $mesa, $dependencia, $cat_ser, $servicio, $duracion_est, $fecha_inicial, $ingeniero, $transporte, $comentario, $ingeniero2, $ingeniero3, $ingeniero4, $Ser2, $Ser3, $Ser4);

                $Id_ticket = obtenId();
                $datos = consulta_ticket($Id_ticket);

                if($resultado == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El ticket se ha sido registrado de manera exitosa!
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                  </script>';
            //ENVIA CORREO

                    $mgClient = connect_correo();
                    $mgClient->sendMessage('ssynsupport.com', array(
                        'from'    => 'SS&N Soporte <actualizaciones_tickets@ssynsupport.com>',
                        'to'      => $datos[0]['NombreM'].' <'. $datos[0]['Correo_mesa'].'>',
                        'subject' => 'Actualización Ticket No. '.$datos[0]['No_ticket'],
                        'html'    => '<html><p> Buen día,</p><p>Les informamos que el ticket de número <b>'.$datos[0]['No_ticket'].'</b> ha sido creado con el estatus "<b>En atención</b>". La datos de este ticket son los siguientes:</p><p>Número de ticket: <b>'.$datos[0]['No_ticket'].'<br></b>Mesa de ayuda: <b>'.$datos[0]['NombreM'].'</b><br>Dependencia: <b>'.$datos[0]['NombreD'].'</b><br>Fecha y hora de creación: <b>'.$datos[0]['Fecha_de_creacion'].'</b><br>Estatus actual: <b>En atención</b></p><p>En caso de tener alguna duda o de no reconocer esta acción, les pedimos por favor ponerse en contacto directamente al correo <a href = "ejemplo@ejemplo.com">ejemplo@ejemplo.com</a> o al teléfono 123-45678.</p><p>Es un placer atenderlos,<br><br><img src="cid:logo_SSyN2.png" alt="logo SSyN" style="width:98px;height:64px;"><br><b>Support, Systems & Net</b> </p><p><i>* Este correo se generó de manera automática. Por favor <b>NO</b> no responder este correo.</i></p></html>'
                    ), array(
                        'inline' => array('../img/logo_SSyN2.png')
                    ));

                } else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear el ticket
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                    </script>';


                    }

                    include '../html/_crear_ticket.html';
                }else {
                    include '../html/_crear_ticket.html';
                }

                include '../html/_footer.html';
            } else {
                session_unset();
                session_destroy();
                header("location:/index.php");
            }
    }else {
        header("location:/index.php");
    }
    
?>
