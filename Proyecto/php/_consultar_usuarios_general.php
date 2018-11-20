<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])&&$_SESSION['Permisos'][6] == TRUE){
        require_once('./model.php');
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_POST['buscar'])){
            $buscar = $_POST['buscar'];
            
            $tabla = buscar_usuario($buscar);
            include('../html/Ajustes/Usuarios/_consultar_usuarios_general.html');
        } else if (isset($_GET['buscar'])){
            $buscar = $_GET['buscar'];
            
            $tabla = buscar_usuario($buscar);
            include('../html/Ajustes/Usuarios/_consultar_usuarios_general.html');
            
        } else {
           include('../html/Ajustes/Usuarios/_consultar_usuarios_general.html');
        
        }
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>            
