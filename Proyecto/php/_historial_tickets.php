<?php
    $menu = "tickets";
    require_once('./model.php');
    session_start();
    if (isset($_SESSION["Usuario"])&&$_SESSION['Permisos'][1] == TRUE){
        include('../html/_header.html');
        include('../html/_menu.html');
        $Id_usuario = $_SESSION ["Id_usuario"];
        $desplegar_todos = 1;
        if(isset($_POST['buscar'])){
            $buscar = $_POST['buscar'];
            $tickets = buscar_historial_tickets($buscar,$Id_usuario,  $desplegar_todos);
        } else if (isset($_GET['buscar'])){
            $buscar = $_GET['buscar'];
            $tickets = buscar_historial_tickets($buscar,$Id_usuario,  $desplegar_todos);
        } else {
            $tickets = consultar_historial_tickets($Id_usuario,$desplegar_todos);
        } include('../html/Tickets/_historial_tickets.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
