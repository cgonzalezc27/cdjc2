<?php
    $menu = "ajustes";
    session_start();
    require_once('./model_modificar_catservicio.php');
    if (isset($_SESSION["Usuario"])){
       if($_SESSION["Permisos"][6] == TRUE){ 
            include('../html/_header.html');
            include('../html/_menu.html');
            if(isset($_POST['NombreCat'])){
                $Id_categoria = $_POST['Id_categoria'];
                $NombreCat = $_POST['NombreCat'];
                $DescripcionCat = $_POST['DescripcionCat'];
                
                $resultado = modificar_categoria($Id_categoria, $NombreCat,$DescripcionCat);
                if($resultado == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡La categoría de servicio ha sido modificado de manera exitosa!
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                  </script>';
                }else {
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al modificar la categoría de servicio
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 6000);
                    </script>'; 
                }
                
                $data =consultar_cat_ser($Id_categoria);
                $disabled = 1;
                include('../html/Ajustes/Servicios/_modificar_catservicios.html');

            } else if (isset($_GET["eliminar_categoria"]) && $_GET["eliminar_categoria"]==1){
                $Id_categoria = $_GET['Id_categoria'];
                    $resultado = eliminar_categoria($Id_categoria);
                   if($resultado == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡La categoría de servicio ha sido eliminado de manera exitosa!
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                  </script>';
                } else {
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al eliminar la categoría de servicio
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                    </script>'; 
                } 
                include('../html/Ajustes/Servicios/_consultar_catservicio.html');

            } else  if( isset($_GET['Id_categoria']) && isset($_GET['editar']) && $_GET['editar']==1){
                $Id_categoria = $_GET['Id_categoria'];
                $disabled = 0;
                $data =consultar_cat_ser($Id_categoria);
                include('../html/Ajustes/Servicios/_modificar_catservicios.html');
            } else if(isset($_GET['Id_categoria'])){
                $Id_categoria = $_GET['Id_categoria'];
                $disabled = 1;
                $data = consultar_cat_ser($Id_categoria);
                include('../html/Ajustes/Servicios/_modificar_catservicios.html');

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