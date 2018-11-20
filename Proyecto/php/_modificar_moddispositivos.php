<?php
$menu = "ajustes";
session_start();
require_once("./model_modificar_modeloDis.php");
if (isset($_SESSION["Usuario"])){
    if($_SESSION["Permisos"][6] == TRUE){
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_POST['NombreMod'])){
            $Id_modelo_dispositivo = $_POST['Id_modelo_dispositivo'];
            $NombreMod = $_POST['NombreMod'];
            $DescripcionMod = $_POST['DescripcionMod'];
            $NombreMarSel = $_POST['NombreMarSel'];
            
            $resultado = modificar_modelo($Id_modelo_dispositivo, $NombreMod,$DescripcionMod, $NombreMarSel);
            if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                            ¡El modelo ha sido modificado de manera exitosa!
                            </div>';
                echo '<script>
                          setTimeout(function(){$("#notify").remove();}, 3000);
                      </script>';
            }else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                            Hubo un error al modificar el modelo
                            </div>';
                echo '<script>
                          setTimeout(function(){$("#notify").remove();}, 6000);
                        </script>'; 
            }

            $data =consultar_modelo($Id_modelo_dispositivo);
            $disabled = 1;

            include('../html/Ajustes/Dispositivos/_modificar_moddispositivos.html');

        }else if (isset($_GET["eliminar_modelo"]) && $_GET["eliminar_modelo"]==1){
            $Id_modelo_dispositivo = $_GET['Id_modelo_dispositivo'];
            $resultado = eliminar_modelo($Id_modelo_dispositivo);
            if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El modelo ha sido modificado de manera exitosa!
                        </div>';
                echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                  </script>';
            } else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al eliminar el modelo
                        </div>';
                echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                    </script>'; 
            } 
            include('../html/Ajustes/Dispositivos/_consultar_marcadis.html');

        }else  if( isset($_GET['Id_modelo_dispositivo']) && isset($_GET['editar']) && $_GET['editar']==1){
            $Id_modelo_dispositivo = $_GET['Id_modelo_dispositivo'];
            $disabled = 0;
            $data =consultar_modelo($Id_modelo_dispositivo);
            include('../html/Ajustes/Dispositivos/_modificar_moddispositivos.html');
        } else if(isset($_GET['Id_modelo_dispositivo'])){
            $Id_modelo_dispositivo = $_GET['Id_modelo_dispositivo'];
            $disabled = 1;
            $data = consultar_modelo($Id_modelo_dispositivo);
            include('../html/Ajustes/Dispositivos/_modificar_moddispositivos.html');

        }
        

        include('../html/_footer.html');
    } else {
        session_unset();
        session_destroy();
        header("location:/index.php");
    }
    
}else{
    header("location:/index.php");
}
?>