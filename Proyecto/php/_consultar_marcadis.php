<?php
$menu = "ajustes";
session_start();
require_once("./model.php");
if (isset($_SESSION["Usuario"])){
    if($_SESSION["Permisos"][6] == TRUE){
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_POST['nombreM'])){
            $nombreM = $_POST['nombreM'];
            $tabla = buscar_marca($nombreM);
            include('../html/Ajustes/Dispositivos/_consultar_marcadis.html');
        } else {
            include('../html/Ajustes/Dispositivos/_consultar_marcadis.html');
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