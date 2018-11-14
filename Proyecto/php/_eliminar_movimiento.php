<?php
session_start();
require_once("./model.php");
if (isset($_SESSION["Usuario"])){
    if($_SESSION["Permisos"][6] == TRUE){
        if(isset($_GET['Id_destino']) && isset($_GET['eliminar_usuario']) && isset($_GET['Id_dispositivo'])&& isset($_GET['Fecha_hora']) && $_GET['eliminar_usuario'] == 1){
            $Id_destino = $_GET['Id_destino'];
            $Id_dispositivo = $_GET['Id_dispositivo'];
            $Fecha_hora = $_GET['Fecha_hora'];
            $resultado = eliminar_movimiento($Id_destino, $Id_dispositivo, $Fecha_hora);
        } else{
            $resultado = FALSE;
        }
        
        $Fecha_hora = fecha_hora_eliminar_movimiento($Id_dispositivo);
        header("location:./_modificar_dispositivo.php?eliminado=".$resultado."&id=".$Id_dispositivo."&fecha=".$Fecha_hora);  
        
    } else {
        session_unset();
        session_destroy();
        header("location:/index.php");
    }
} else {
    header("location:/index.php");
}

?>