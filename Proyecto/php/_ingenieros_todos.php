<?php
    $menu = "ingenieros";
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');
        include('../html/Ingenieros/_ingenieros_todos.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
