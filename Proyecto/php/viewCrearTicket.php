<?php
    $menu = "tickets";
    session_start();
    if (isset($_SESSION["Usuario"])){
    require_once('./model_car.php');
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
        
        $resultado = registrar_ticket2($no_ticket, $mesa, $dependencia, $cat_ser, $servicio, $duracion_est, $fecha_inicial, $ingeniero, $transporte, $comentario, $ingeniero2, $ingeniero3, $ingeniero4);

        if($resultado == TRUE){
            echo '<div id="notify" class="alert alert-success" role="alert">
                ¡El ticket se ha sido registrado de manera exitosa!
                </div>';
            echo '<script>
              setTimeout(function(){$("#notify").remove();}, 3000);
          </script>';
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
        header("location:/index.php");
    }
?>
