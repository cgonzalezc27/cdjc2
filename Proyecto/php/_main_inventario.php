<?php
    $menu = "inventario";
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        include('../html/_main_inventario.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
