<?php
    $menu = "ajustes";
    session_start();
    require_once("./model_modificar_servicio.php");
    if (isset($_SESSION["Usuario"])){
        if($_SESSION["Permisos"][6] == TRUE){
            include('../html/_header.html');
            include('../html/_menu.html');
            if(isset($_POST['nombreCatSerBus'])){
                $nombreCatSerBus = $_POST['nombreCatSerBus'];
                $tabla = buscar_cat_ser($nombreCatSerBus);
                include('../html/Ajustes/Servicios/_consultar_servicios.html');
            }else{
                include('../html/Ajustes/Servicios/_consultar_servicios.html');
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