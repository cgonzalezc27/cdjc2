<?php
    $menu = "mesas";
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        include('../html/Ajustes/Clientes/_clientes_todos.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
