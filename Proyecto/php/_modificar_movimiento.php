<?php
    $menu = "inventario";
    session_start();
    if (isset($_SESSION["Usuario"])){
        require_once('./model.php');
        include('../html/_header.html');
        include('../html/_menu.html');
        $serie = $_GET['serie']; 
        $datos = buscar_serie($serie);
        include('../html/Ajustes/Dispositivos/_modificar_movimiento.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>