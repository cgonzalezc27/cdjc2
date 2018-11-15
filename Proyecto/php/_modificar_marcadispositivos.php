<?php
$menu = "ajustes";
session_start();
require_once('./model.php');
if (isset($_SESSION["Usuario"])){
    if($_SESSION["Permisos"][6] == TRUE){ 
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_POST['NombreM'])){
            $Id_marca_dispositivo = $_POST['Id_marca_dispositivo'];
            $NombreM = $_POST['NombreM'];
            $DescripcionM = $_POST['DescripcionM'];
            $resultado = modificar_marca($Id_marca_dispositivo, $NombreM,$DescripcionM);
            if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡La marca ha sido modificado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            }else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al modificar la marca
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 6000);
                </script>'; 
            }
            $data =consultar_marca($Id_marca_dispositivo);
            $disabled = 1;
            include('../html/Ajustes/Dispositivos/_modificar_marcadispositivos.html');
            
        } else if (isset($_GET["eliminar_marca"]) && $_GET["eliminar_marca"]==1){
            $Id_marca_dispositivo = $_GET['Id_marca_dispositivo'];
                $resultado = eliminar_marca($Id_marca_dispositivo);
               if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡La marca ha sido eliminado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            } else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al eliminar la marca
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
                </script>'; 
            } 
            include('../html/Ajustes/Dispositivos/_consultar_marcadis.html');
            
        } else  if( isset($_GET['Id_marca_dispositivo']) && isset($_GET['editar']) && $_GET['editar']==1){
            $Id_marca_dispositivo = $_GET['Id_marca_dispositivo'];
            $disabled = 0;
            $data =consultar_marca($Id_marca_dispositivo);
            include('../html/Ajustes/Dispositivos/_modificar_marcadispositivos.html');
        } else if(isset($_GET['Id_marca_dispositivo'])){
            $Id_marca_dispositivo = $_GET['Id_marca_dispositivo'];
            $disabled = 1;
            $data = consultar_marca($Id_marca_dispositivo);
            include('../html/Ajustes/Dispositivos/_modificar_marcadispositivos.html');
            
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