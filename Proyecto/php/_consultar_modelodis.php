<?php
    $menu = "ajustes";
    session_start();
    require_once("./model_modificar_modeloDis.php");
    if (isset($_SESSION["Usuario"])){
        if($_SESSION["Permisos"][6] == TRUE){
            include('../html/_header.html');
            include('../html/_menu.html');
            if(isset($_POST['nombreModeloBus'])){
                $nombreModeloBus = $_POST['nombreModeloBus'];
                $tabla = buscar_modelo($nombreModeloBus);
                include('../html/Ajustes/Dispositivos/_consultar_modelodis.html');
            }else{
                include('../html/Ajustes/Dispositivos/_consultar_modelodis.html');
            }
            
            include('../html/_footer.html');
        }else{
            session_unset();
            session_destroy();
            header("location:/index.php");
        }
    } else {
        header("location:/index.php");
    }
?>
