<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        include('../html/Ajustes/Roles/_consultar_roles.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>