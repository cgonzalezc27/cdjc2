<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        include('../html/Ajustes/Servicios/_consultar_catservicio.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>