<?php
    $menu = "tickets";
    require_once("./model.php");
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        $Id_usuario = $_SESSION["Id_usuario"];
        
        if ($_SESSION ['Permisos'][1] == TRUE){
            $desplegar_todos = 1;
        } else if ($_SESSION ['Permisos'][17] == TRUE){
            // cuando $desplegar_todos = 2, solo despliega los del ingeniero.
            $desplegar_todos = 2;
        } else {
            $desplegar_todos = 0;
        }   
        if(isset($_POST['buscar'])){
            $buscar = $_POST['buscar'];
            $tickets = buscar_tickets($buscar,$Id_usuario,  $desplegar_todos);
        } else if (isset($_GET['buscar'])){
            $buscar = $_GET['buscar'];
            $tickets = buscar_tickets($buscar,$Id_usuario,  $desplegar_todos);
        } else {
            $tickets = consultar_tickets_abiertos($Id_usuario,$desplegar_todos);
        }
        if(isset($_GET['ticket_creado']) && $_GET['ticket_creado'] == 1){
          echo '<div id="notify" class="alert alert-success" role="alert">
                Tu Ticket ha sido creado
                </div>';

          echo '<script>
              setTimeout(function(){$("#notify").remove();}, 3000);
          </script>';
        }
        include('../html/Tickets/_tickets_abiertos_v2.html');

        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
