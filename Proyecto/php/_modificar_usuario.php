<?php
    $menu = "ajustes";
    require_once('./model.php');
    session_start();
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html'); 
        if (isset($_POST['Nombre'])&&$_SESSION['Permisos'][6] == TRUE){
            $Id_usuario = $_POST['Id_usuario'];
            $Nombre_de_usuario = $_POST['Nombre_de_usuario'];
            $Nombre = $_POST['Nombre'];
            $Apellido1 = $_POST['Apellido1'];
            $Apellido2 = $_POST['Apellido2'];
            $RFC = $_POST['RFC'];
            $Estado = $_POST['Estado'];
            $Ciudad = $_POST['Ciudad'];
            $Calle = $_POST['Calle'];        
            $Numero_exterior = $_POST['Numero_exterior'];        
            $Numero_interior = $_POST['Numero_interior'];        
            $CP = $_POST['CP'];  

            if (isset($_FILES["Foto"]) && $_FILES["Foto"]["name"] != ""){
                $Foto = $_FILES['Foto'];
                $target_dir = "../img_usuarios/".$Id_usuario;
                $target_file = str_replace(" ","_",$target_dir . basename($_FILES["Foto"]["name"]));
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["Foto"]["tmp_name"]);
                if($check != false) {
                    $uploadOk = 1;
                    $error_foto = "";
                } else {
                    $error_foto = " / El archivo subido no es una imagen.";
                    $uploadOk = 0;
                    $Foto = "no_modificar";
                }
                if (file_exists($target_file)) {
                    $error_foto =  " / Ya existe una imagen con el nombre del archivo que intentaste subir.";
                    $uploadOk = 0;
                    $Foto = "no_modificar";
                }
                if ($_FILES["Foto"]["size"] > 10000000) {
                    $error_foto =  " / El tamaño de la imagen que subiste es muy grande.";
                    $uploadOk = 0;
                    $Foto = "no_modificar";
                }
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $error_foto =  " / La imagen debe ser JPG, PNG o JPEG.";
                    $uploadOk = 0;
                    $Foto = "no_modificar";
                } else {
                    if ($uploadOk == 1){
                        if (move_uploaded_file($_FILES["Foto"]["tmp_name"], $target_file)) {
                            $Foto = $target_file;
                        } 
                    }
                }
            } else {
                $Foto = "no_modificar";
                $uploadOk = 1;
            }
            $Rol = $_POST['Rol'];

            $resultado = modificar_usuario($Id_usuario,$Nombre_de_usuario,$Nombre,$Apellido1,$Apellido2,$RFC,$Estado,$Ciudad,$Calle,$Numero_exterior,$Numero_interior,$CP,$Foto,$Rol);
            $disabled = 1;

            $datos = consultar_usuario($Id_usuario,$disabled);
            if($resultado == TRUE && $uploadOk == 1){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡El usuario ha sido modificado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            } else if ($resultado ==="Usuario ya existente" ){
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    El usuario no puede ser modificado, pues ya existe un usuario con el nombre de usuario seleccionado.
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 10000);
                </script>'; 
            } else if (isset($error_foto)){
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al modificar el usuario'.$error_foto.'
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 6000);
                </script>'; 
            } else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al modificar el usuario
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 6000);
                </script>'; 
            }
            include('../html/Ajustes/Usuarios/_modificar_usuario.html');
        } else if (isset($_POST['contrasena_actual'])){
            $contrasena_actual = $_POST['contrasena_actual'];
            $contrasena_nueva = $_POST['contrasena_nueva'];
            $contrasena_confirmar = $_POST['contrasena_confirmar'];
            $Id_usuario = $_POST['Id_usuario'];
            if ($contrasena_nueva === $contrasena_confirmar){
                if(strlen($contrasena_confirmar)>15){
                    $error_contraseña = "La contraseña no puede ser de más de 15 caracteres.";
                    include('../html/Ajustes/Usuarios/_cambiar_contrasena.html');
                } else {
                    $resultado = cambiar_contrasena($contrasena_actual,$Id_usuario,$contrasena_nueva);
                    if ($resultado === 'Contraseña incorrecta'){
                        $error_contraseña = "La contraseña actual es incorrecta.";
                        include('../html/Ajustes/Usuarios/_cambiar_contrasena.html');
                    } else if ($resultado === 'Falló la conexion') {
                         echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error de conexión al modificar la contraseña.
                        </div>';
                        echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 3000);
                        </script>'; 
                        $disabled = 1;
                        $datos = consultar_usuario($Id_usuario,$disabled);
                    include('../html/Ajustes/Usuarios/_modificar_usuario.html');
                    } else if ($resultado === TRUE){
                         echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡La contraseña ha sido modificado de manera exitosa!
                        </div>';
                        echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 3000);
                        </script>';
                        $disabled = 1;
                        $datos = consultar_usuario($Id_usuario,$disabled);
                        include('../html/Ajustes/Usuarios/_modificar_usuario.html');
                    }
                }
            } else {
                $error_contraseña = "Las contraseñas nuevas que ingrsaste no coinciden.";
                include('../html/Ajustes/Usuarios/_cambiar_contrasena.html');
            }
        } else if (isset($_POST['restablecer_contrasena'])&&$_SESSION['Permisos'][6] == TRUE) {
            $Id_usuario = $_POST['Id_usuario'];
            $resultado = restablecer_contrasena($Id_usuario);
            $disabled = 1;
            $datos = consultar_usuario($Id_usuario,$disabled);
            if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡La contraseña ha sido restablecida de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            } else{
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al restablecer la contraseña el usuario 
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 6000);
                </script>'; 
            }
            include('../html/Ajustes/Usuarios/_modificar_usuario.html');
        } else {
            if (isset($_GET["editar"])&&$_GET["editar"]==1&&$_SESSION['Permisos'][6] == TRUE){
                $disabled = 0;
                $Id_usuario = $_GET['Id_usuario'];
                $datos = consultar_usuario($Id_usuario,$disabled);
                include('../html/Ajustes/Usuarios/_modificar_usuario.html');
            } else if (isset($_GET["cambio_contrasena"])&&$_GET["cambio_contrasena"]==1){
                $Id_usuario = $_GET['Id_usuario'];
                $disabled = 0;
                $datos = consultar_usuario($Id_usuario,$disabled);
                include('../html/Ajustes/Usuarios/_cambiar_contrasena.html');
            } else if (isset($_GET["eliminar_usuario"])&&$_GET["eliminar_usuario"]==1&&$_SESSION['Permisos'][6] == TRUE){
                $Id_usuario = $_GET['Id_usuario'];
                $resultado = eliminar_usuario($Id_usuario);
               if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡El usuario ha sido eliminado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            } else{
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al eliminar el usuario
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
                </script>'; 
            } 
            $tabla = mostrar_todos_usuarios();
                include('../html/Ajustes/Usuarios/_consultar_usuarios_general.html');
            } else if (isset($_GET["cambio_contrasena"])&&$_GET["cambio_contrasena"]==1){
                include('../html/Ajustes/Usuarios/_cambiar_contrasena.html');
            } else  {
                $disabled = 1;
                if ($_SESSION['Permisos'][6] == TRUE){
                    $Id_usuario = $_GET['Id_usuario'];
                } else {
                    $Id_usuario = $_SESSION['Id_usuario'];
                }
                $datos = consultar_usuario($Id_usuario,$disabled);
                include('../html/Ajustes/Usuarios/_modificar_usuario.html');
            }
        }
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>    