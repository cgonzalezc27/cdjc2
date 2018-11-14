<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])&&$_SESSION['Permisos'][6] == TRUE){
        require_once('./model.php');
        include('../html/_header.html');
        include('../html/_menu.html');
       $tabla = mostrar_todos_usuarios();
           include('../html/Ajustes/Usuarios/_consultar_usuarios_general.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>            
