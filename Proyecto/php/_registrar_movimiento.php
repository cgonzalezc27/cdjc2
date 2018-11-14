<?php
    $menu = "inventario";
    require_once("./model.php");
    session_start();
    if (isset($_SESSION["Usuario"])){
        if ($_SESSION['Permisos'][13] == TRUE){
                include('../html/_header.html');
                include('../html/_menu.html');

            if (isset($_POST["destino"])) {

                    $destino = $_POST["destino"];
                    if (isset($_POST["ticket"])){
                        $ticket = $_POST["ticket"];
                    } else{
                        $ticket = NULL;
                    }
                    $dispositivo = $_POST["serie"];
                    $resultado = registrar_movimiento($destino,$ticket,$dispositivo);
                    $_POST["destino"] = NULL;
                    $_POST["serie"] = NULL;
                    if($resultado == TRUE){
                        echo '<div id="notify" class="alert alert-success" role="alert">
                            ¡El movimiento ha sido registrado de manera exitosa!
                            </div>';
                        echo '<script>
                          setTimeout(function(){$("#notify").remove();}, 3000);
                      </script>';
                    } else{
                        echo '<div id="notify" class="alert alert-danger"    role="alert">
                            Hubo un error al crear el movimiento
                            </div>';
                        echo '<script>
                          setTimeout(function(){$("#notify").remove();}, 3000);
                        </script>';
                    }
                    include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
                } else if (empty($_POST["destino"]) && isset($_POST["serie"]))
                {
                    $error = '<div class ="error">*Debes seleccionar un destino.</div>';
                    $serie = $_POST["serie"];
                    $datos = buscar_serie($serie);
                    $_POST["serie1"] = $_POST["serie"];
                    include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
                } else if (isset($_POST["serie1"]) && $_POST["serie1"] != ""){

                        $serie = $_POST["serie1"];
                        $datos = buscar_serie($serie);
                        if ($datos == NULL){
                            $_POST["serie1"] = "";
                            $error = '<div class ="error">*¡Lo sentimos! No encontramos ese número de serie.</div>';
                            include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
                        } else {
                            include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
                        }

                } else if (isset($_POST["serie2"])&&$_POST["serie2"] != ""){

                    $serie = $_POST["serie2"];
                    $_POST["serie1"] = $_POST["serie2"];
                    $datos = buscar_serie($serie);
                    if ($datos == NULL){
                        $_POST["serie1"] = "";
                        $error = '<div class ="error">*¡Lo sentimos! No encontramos ese número de serie.</div>';
                        include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
                    } else {
                        include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
                    }

                } else if ((isset($_POST["serie1"])&&$_POST["serie1"] == "") || (isset($_POST["serie2"])&&$_POST["serie2"] == "")) {
                    $error = '<div class ="error">*Debes digitar un número de serie.</div>';
                    include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');

                } else {
                    include('../html/Ajustes/Dispositivos/_registrar_movimiento.html');
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
