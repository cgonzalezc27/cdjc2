<?php
    $menu = "tickets";
    session_start();
    if (isset($_SESSION["Usuario"])){
        require_once('./model.php');
        include '../html/_header.html';
        include '../html/_menu.html';
        if ($_SESSION['Permisos'][2] == TRUE){
            if (isset($_POST['Id_ticket'])){
                $Id_ticket = $_POST['Id_ticket'];
                $NombreE = $_POST['Estatus'];
                $ComentarioD = $_POST['Diagnostico'];                
                $datos = consulta_ticket($Id_ticket);
                $resultado = enviar_diagnostico($Id_ticket,$NombreE,$ComentarioD);
                
                if($resultado[1] == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El diagnóstico ha sido enviado de manera exitosa!
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                  </script>';
                    if ($NombreE != $datos[0]['NombreE']){
                    $mgClient = connect_correo();
                    $mgClient->sendMessage('ssynsupport.com', array(
                        'from'    => 'SS&N Soporte <actualizaciones_tickets@ssynsupport.com>',
                        'to'      => $datos[0]['NombreM'].' <'. $datos[0]['Correo_mesa'].'>',
                        'subject' => 'Actualización Ticket No. '.$datos[0]['No_ticket'],
                        'html'    => '<html><p> Buen día,</p><p>Les informamos que el ticket de número <b>'.$datos[0]['No_ticket'].'</b> ha cambiado su estatus a "<b>'.$NombreE.'</b>". La datos de este ticket son los siguientes:</p><p>Número de ticket: <b>'.$datos[0]['No_ticket'].'<br></b>Mesa de ayuda: <b>'.$datos[0]['NombreM'].'</b><br>Dependencia: <b>'.$datos[0]['NombreD'].'</b><br>Fecha y hora de creación: <b>'.$datos[0]['Fecha_de_creacion'].'</b><br>Estatus anterior: <b>'.$datos[0]['NombreE'].'</b><br>Estatus actual: <b>'.$NombreE.'</b><br>Fecha y hora de última actualización: <b>'.$resultado['Fecha_hora_actualizacion'].'</b></p><p>En caso de tener alguna duda o de no reconocer esta actualización, les pedimos por favor ponerse en contacto directamente al correo <a href = "ejemplo@ejemplo.com">ejemplo@ejemplo.com</a> o al teléfono 123-45678.</p><p>Es un placer atenderlos,<br><br><img src="cid:logo_SSyN2.png" alt="logo SSyN" style="width:98px;height:64px;"><br><b>Support, Systems & Net</b> </p><p><i>* Este correo se generó de manera automática. Por favor <b>NO</b> no responder este correo.</i></p></html>'
                    ), array(
                        'inline' => array('../img/logo_SSyN2.png')
                    ));
                    }
                } else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al enviar el diagnóstico
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                    </script>'; 
                }
                $datos = consulta_ticket($Id_ticket);
                include '../html/Tickets/_consultar_ticket.html';
            } else {
                $datos = consulta_ticket($_GET['id']);
                $datos[0]['Estatus'] = consultar_estatus($_GET['id']);
                include '../html/Tickets/_modificar_estatus_ticket.html';
            }
        } else if ($_SESSION['Permisos'][3] == TRUE){
            if (isset($_POST['Id_ticket'])){
                
                $Id_ticket = $_POST['Id_ticket'];
                $No_ticket = $_POST['noticket'];
                $NombreE = $_POST['estatus'];
                $NombreM = $_POST['mesa'];
                $NombreD = $_POST['dependencia'];
                $NombreCS = $_POST['categoria_de_servicio'];
                $NombreS = $_POST['servicio'];
                $Duracion = $_POST['duracion'];
                $Fecha_y_hora_de_inicio_programada = $_POST['fechaini'];
                $NombreMT = $_POST['medio'];
                $Comentario = $_POST['comentario'];
                $datos = consulta_ticket($Id_ticket);
                $Estatus_anterior = $datos[0]['NombreE'];
                $resultado = modificar_ticket($Id_ticket,$No_ticket,$NombreE,$NombreM,$NombreD,$NombreCS,$NombreS,$Duracion,$Fecha_y_hora_de_inicio_programada,$NombreMT,$Comentario);
                $datos = consulta_ticket($Id_ticket);
                if($resultado [1] == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El ticket ha sido modificado de manera exitosa!
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                  </script>';
                    if ($NombreE != $Estatus_anterior){
                        $mgClient = connect_correo();
                        $mgClient->sendMessage('ssynsupport.com', array(
                        'from'    => 'SS&N Soporte <actualizaciones_tickets@ssynsupport.com>',
                        'to'      => $datos[0]['NombreM'].' <'. $datos[0]['Correo_mesa'].'>',
                        'subject' => 'Actualización Ticket No. '.$datos[0]['No_ticket'],
                        'html'    => '<html><p> Buen día,</p><p>Les informamos que el ticket de número <b>'.$datos[0]['No_ticket'].'</b> ha cambiado su estatus a "<b>'.$NombreE.'</b>". La datos de este ticket son los siguientes:</p><p>Número de ticket: <b>'.$datos[0]['No_ticket'].'<br></b>Mesa de ayuda: <b>'.$datos[0]['NombreM'].'</b><br>Dependencia: <b>'.$datos[0]['NombreD'].'</b><br>Fecha y hora de creación: <b>'.$datos[0]['Fecha_de_creacion'].'</b><br>Estatus anterior: <b>'.$Estatus_anterior.'</b><br>Estatus actual: <b>'.$NombreE.'</b><br>Fecha y hora de última actualización: <b>'.$resultado['Fecha_hora_actualizacion'].'</b></p><p>En caso de tener alguna duda o de no reconocer esta actualización, les pedimos por favor ponerse en contacto directamente al correo <a href = "ejemplo@ejemplo.com">ejemplo@ejemplo.com</a> o al teléfono 123-45678.</p><p>Es un placer atenderlos,<br><br><img src="cid:logo_SSyN2.png" alt="logo SSyN" style="width:98px;height:64px;"><br><b>Support, Systems & Net</b> </p><p><i>* Este correo se generó de manera automática. Por favor <b>NO</b> no responder este correo.</i></p></html>'
                    ), array(
                        'inline' => array('../img/logo_SSyN2.png')
                    ));
                    }
                } else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al modificar el ticket.
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                    </script>'; 
                }

                include '../html/Tickets/_consultar_ticket.html';
            } else {
                $datos = consulta_modificar_ticket($_GET['id']);
                include '../html/Tickets/_modificar_Ticket.html';
            }
        }

        include '../html/_footer.html';
    } else {
        header("location:/index.php");
    }
?>
