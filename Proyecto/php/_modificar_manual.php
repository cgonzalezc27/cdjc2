<?php
$menu = "ajustes";
session_start();
require_once('./model.php');
if (isset($_SESSION["Usuario"])){
    if($_SESSION["Permisos"][6] == TRUE || $_SESSION["Permisos"][4] == TRUE){ 
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_POST['NombreManual']) && $_SESSION["Permisos"][6] == TRUE){
            $Id_manual = $_POST['$Id_manual'];
            $NombreManual = $_POST['NombreManual'];
            $DescripcionManual = $_POST['DescripcionManual'];
            
            
            $resultado = modificar_manual($Id_manual, $NombreManual,$DescripcionManual);
            if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡El manual ha sido modificado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            }else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al modificar el manual
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 6000);
                </script>'; 
            }
            $data =consultar_manual($Id_manual);
            $disabled = 1;
            include('../html/Ajustes/Manuales/_modificar_manual.html');
            
        } else if (isset($_GET["eliminar_manual"]) && $_GET["eliminar_manual"]==1 && $_SESSION["Permisos"][6] == TRUE ){
            $Id_manual = $_GET['Id_manual'];
                $resultado = eliminar_manual($Id_manual);
               if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡El manual ha sido eliminado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            } else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al eliminar el manual
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
                </script>'; 
            } 
            include('../html/Ajustes/Manuales/_consultar_manual.html');
            
        } else  if( isset($_GET['Id_manual']) && isset($_GET['editar']) && $_GET['editar']==1 && $_SESSION["Permisos"][6] == TRUE ){
            $Id_manual = $_GET['Id_manual'];
            $disabled = 0;
            $data =consultar_manual($Id_manual);
            include('../html/Ajustes/Manuales/_modificar_manual.html');
        } else if(isset($_GET['Id_manual'])){
            $Id_manual = $_GET['Id_manual'];
            $disabled = 1;
            $data = consultar_manual($Id_manual);
            include('../html/Ajustes/Manuales/_modificar_manual.html');
            
        }
        include('../html/_footer.html');
    } else {
        session_unset();
        session_destroy();
        header("location:/index.php");
    }
} else {
    header("location:/index.php");
}
?>