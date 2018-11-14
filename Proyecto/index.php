<?php
    require_once("./php/model.php");
    
    session_start();
    
    if (isset($_SESSION["Usuario"])){
        $menu = "tickets";
        include("html/_header_index.html");
        include('./html/_menu.html');
        include('./html/Tickets/_tickets_abiertos_v2.html');
        include('./html/_footer.html');
        
    } else if ($_SERVER['REQUEST_METHOD'] == "POST"){
        if ($_POST["Usuario"] == "" || $_POST["Pass"] == ""){
                $error_login = "<div class = 'required'>*Favor llenar los campos de usuario y contraseña</div><br>";
                include("./html/_header_index.html");
                include("./html/_login.html");
                include('./html/_footer_index.html');
            } else { 
                $usuario = clean($_POST["Usuario"]);
                $contrasena = clean($_POST["Pass"]);
                $resultado = corroborar_contrasena($contrasena, $usuario);
            if ($resultado['validacion'] === TRUE){
                    $_SESSION["Usuario"] = $usuario;
                    $_SESSION["Id_usuario"] = $resultado[0]['Id_usuario'];
                    $_SESSION["NombreU"] = $resultado[0]['NombreU'];
                    $_SESSION["Apellido1"] = $resultado[0]['Apellido1'];
                    $_SESSION["Apellido2"] = $resultado[0]['Apellido2'];
                    $_SESSION["NombreR"] = $resultado[0]['NombreR'];
                    $_SESSION["Id_rol"] = $resultado[0]['Id_rol'];
                    $_SESSION["Permisos"] = $resultado['Permisos'];
                    if($_SESSION["Permisos"][6] == TRUE){
                        $_SESSION["ajustes"] = '<a class="navbar_a" href="/php/_ajustes.php"><i class="material-icons">settings</i> Ajustes</a>';
                    } else {
                        $Id_usuario = $resultado['Id_usuario'];
                        $_SESSION["ajustes"] = '
                         <a class="navbar_a" href="/php/_modificar_usuario.php"><i class="material-icons">settings</i> Ajustes</a>';
                    }
                    $menu = "tickets";
                    header("location:/php/_tickets_abiertos_v2.php");
                    
                } else {
                    sleep(3);
                    $error_login = "<div class = 'required'>*El usuario o la contraseña son incorrectos</div><br>";
                    include("./html/_header_index.html");
                    include("./html/_login.html");
                    include('./html/_footer_index.html');
                    
                }
            }

    } else {
        if (isset($_GET['error']) && $_GET['error'] == 1){
            $error_login = "<div class = 'required'>*El usuario que estabas utilizando no tiene los privilegios para acceder a esta función. Por favor accede con uno que si los tenga.</div><br>";
        }
        include('./html/_header_index.html');
        include('./html/_login.html');
        include('./html/_footer_index.html');
    }

    
?>



