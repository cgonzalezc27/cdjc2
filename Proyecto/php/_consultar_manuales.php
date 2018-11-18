<?php
$menu = "manuales";
session_start();
require_once("./model.php");
if (isset($_SESSION["Usuario"])){
    if($_SESSION["Permisos"][4] == TRUE){
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_POST['buscar'])){
            $buscar = $_POST['buscar'];
            
            $tabla = buscar_manual($buscar);
            include('../html/Ajustes/Manuales/_consultar_manuales.html');
        } else if (isset($_GET['buscar'])){
            $buscar = $_GET['buscar'];
            
            $tabla = buscar_manual($buscar);
            include('../html/Ajustes/Manuales/_consultar_manuales.html');
        
        }else {
            include('../html/Ajustes/Manuales/_consultar_manuales.html');
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