<?php
    $menu = "inventario";
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        include('../html/Ajustes/Dispositivos/_buscar_dispositivo_resultado.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
