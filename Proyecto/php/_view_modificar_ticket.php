<?php
    $menu = "tickets";
    require_once('./model.php');
    session_start();
    if (isset($_SESSION["Usuario"])){
        include '../html/_header.html';
        include '../html/_menu.html';
        $datos = consulta_ticket($_GET["id"]);
        
        include '../html/Tickets/_consultar_ticket.html';
        include '../html/_footer.html';
    } else {
        header("location:/index.php");
    }
?>
