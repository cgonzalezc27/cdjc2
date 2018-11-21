<?php
$menu = "ajustes";
session_start();
if (isset($_SESSION["Usuario"])){
    //if($_SESSION["Id_rol"] == 3){
    if($_SESSION["Permisos"][6] == TRUE){
        require_once('accessDataBase.php');
        require_once("./model_camilo_ajustes.php");
        require_once("./model_registrar_dependencia.php");
        require_once("./model_registrar_modeloDis.php");
        require_once("./model_registrar_tipoDis.php");
        require_once("./model_registrar_usuario.php");
        //require_once("./_registrar_servicio.php");



        include('../html/_header.html');
        include('../html/_menu.html');

        $lista_servicios_manuales = lista_servicios_manuales();
        
        if(isset($_POST['NombreManual'])){
            $NoServicios = $_POST['no_servicios'];
            $x = 0;
            $Id_trabajo = [];
            for ($i = 0; $i < $NoServicios; $i++){
                if (isset($_POST['s'.$i])){
                    $Id_trabajo[$x] = $_POST['s'.$i];
                    $x++;
                }
            }
            
            if(isset($_FILES["ManualArchivo"]) && $_FILES["ManualArchivo"]["name"] != ""){
                if ($Id_trabajo == []){
                    $resultado = "falta_permiso";
                } else {
                    $ManualArchivo = $_FILES['ManualArchivo'];
                    $target_dir = "../manuales/".$_POST['NombreManual'].$_POST['VersionManual']."_";
                    $target_file = str_replace(" ","_",$target_dir .basename($_FILES["ManualArchivo"]["name"]));
                    $uploadOk = 1;
                    $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                    $check = filesize($_FILES["ManualArchivo"]["tmp_name"]);
                    if($check != false) {
                        $uploadOk = 1;
                        $error_archivo_manual = "";
                    } else {
                        $error_archivo_manual = " / El archivo subido no es un manual.";
                        $uploadOk = 0;
                        $ManualArchivo = "no_modificar";
                    }
                    if (file_exists($target_file)) {
                        $error_archivo_manual =  " / Ya existe un manual con el nombre del archivo que intentaste subir.";
                        $uploadOk = 0;
                        $ManualArchivo = "no_modificar";
                    }
                    if ($_FILES["ManualArchivo"]["size"] > 10000000) {
                        $error_archivo_manual =  " / El tamaño del manual que subiste es muy grande.";
                        $uploadOk = 0;
                        $ManualArchivo = "no_modificar";
                    }
                    if($FileType != "pdf") {
                        $error_archivo_manual =  " / El manual debe ser PDF.";
                        $uploadOk = 0;
                        $ManualArchivo = "no_modificar";
                    } else {
                        if ($uploadOk == 1){
                            if (move_uploaded_file($_FILES["ManualArchivo"]["tmp_name"], $target_file)) {
                                $ManualArchivo = $target_file;
                            } 
                        }
                    }
                    if($uploadOk == 1){
                        $NombreManual = $_POST['NombreManual'];
                        $VersionManual = $_POST['VersionManual'];
                        $DescripcionManual = $_POST['DescripcionManual'];
                        
                        $resultado = registrar_manual($NombreManual, $VersionManual, $DescripcionManual, $Id_trabajo,$ManualArchivo);
                    }
                }
                
                if(isset ($uploadOk) && $uploadOk == 1 && $resultado == 1){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El manual se creó de manera exitosa!
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 3000);
                        </script>';
                } else if ($resultado == "falta_permiso"){
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear el manual. Debes elegir por lo menos un servicio para relacionar al manual.
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 6000);
                    </script>'; 
                } else if (isset($error_archivo_manual)){
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear el manual. '.$error_archivo_manual.'
                        </div>';
                    echo '<script>
                      setTimeout(function(){$("#notify").remove();}, 6000);
                    </script>'; 
                } else {
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        No se pudo crear el manual, hubo un error.
                        </div>';
                echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }
            } else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                        No se pudo crear el manual, faltó agregar el archivo.
                        </div>';
                echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
            }
        }

        if(isset($_POST['nombreMarca'])){
            $nombreM = $_POST['nombreMarca'];
            $descripcionMarca = $_POST['descripcionMarca'];
            $resultado = registrar_marca($nombreM, $descripcionMarca);
            if($resultado == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡La marca ha sido registrada de manera exitosa!
                        </div>';
                echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
            }else{
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear la marca.
                        </div>';
                echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
            }

        }


//Obtiene y registra dentro de la tabla de Dependencia
            if(isset($_POST['mesadep']) && $_POST['mesadep'] != ""){


                if(!isset($_POST['razonSdep']) ||  $_POST['razonSdep'] == ""){
                    $errorrazon = '<a class ="error">*</a>';
                }else{
                    $razonSdep = $_POST['razonSdep'];
                }
                if(!isset($_POST['rfcdep']) ||  $_POST['rfcdep'] == ""){
                    $errorrfc = '<a class ="error">*</a>';
                }else{
                    $rfcdep = $_POST['rfcdep'];
                }
                /*if(!isset($_POST['coloniadep']) ||  $_POST['coloniadep'] == ""){
                    $errorcolonia = '<a class ="error">*</a>';
                }else{
                    $coloniadep = $_POST['coloniadep'];
                }*/
                if(!isset($_POST['calledep']) ||  $_POST['calledep'] == ""){
                    $errorcalle = '<a class ="error">*</a>';
                }else{
                    $calledep = $_POST['calledep'];
                }
                if(!isset($_POST['num1dep']) ||  $_POST['num1dep'] == ""){
                    $errornum = '<a class ="error">*</a>';
                }else{
                    $num1dep = $_POST['num1dep'];
                }
                if(!isset($_POST['num2dep']) ||  $_POST['num2dep'] == ""){
                    $num2dep = NULL;
                }else{
                    $num2dep = $_POST['num2dep'];
                }
                if(!isset($_POST['ciudaddep']) ||  $_POST['ciudaddep'] == ""){
                    $errorciudad = '<a class ="error">*</a>';
                }else{
                    $ciudaddep = $_POST['ciudaddep'];
                }
                if(!isset($_POST['estadodep']) ||  $_POST['estadodep'] == ""){
                    $errorestado = '<a class ="error">*</a>';
                }else{
                    $estadodep = $_POST['estadodep'];
                }
                if(!isset($_POST['cpdep']) ||  $_POST['cpdep'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $cpdep = $_POST['cpdep'];
                }
                if(!isset($_POST['encargadodep']) ||  $_POST['encargadodep'] == ""){
                    $encargadodep = NULL;
                }else{
                    $encargadodep = $_POST['encargadodep'];
                }
                if(!isset($_POST['tesperadep']) ||  $_POST['tesperadep'] == ""){
                    $tesperadep = NULL;
                }else{
                    $tesperadep = $_POST['tesperadep'];
                }
                if(!isset($_POST['mesadep']) ||  $_POST['mesadep'] == ""){
                    $mesadep = $_POST['mesadep'];
                }else{
                    $mesadep = $_POST['mesadep'];
                }
                if(!isset($_POST['comentariodep']) ||  $_POST['comentariodep'] == ""){
                    $comentariodep = NULL;
                }else{
                    $comentariodep = $_POST['comentariodep'];
                }

                $resultdep = registrar_dependencia($razonSdep, $rfcdep, $calledep, $num1dep, $num2dep, $ciudaddep, $estadodep, $cpdep, $encargadodep, $tesperadep, $mesadep, $comentariodep);
                //echo "CACA";


                //MENSAJE DE RETROALIMENTACION
                if($resultdep == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡La dependencia ha sido registrada de manera exitosa!
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear la dependencia
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }

            }



//obtiene los valores de la forma del documento html para modelo de dispositivo
            if(isset($_POST['marcaDisSelect']) && $_POST['marcaDisSelect'] != ""){

                //echo "SI ESTA ENTRANDO";
                if(!isset($_POST['nombreModDis']) ||  $_POST['nombreModDis'] == ""){
                    $errorrazon = '<a class ="error">*</a>';
                }else{
                    $nombreModDis = $_POST['nombreModDis'];
                }

                if(!isset($_POST['marcaDisSelect']) ||  $_POST['marcaDisSelect'] == ""){
                    $errorrazon = '<a class ="error">*</a>';
                }else{
                    $marcaDisSelect = $_POST['marcaDisSelect'];
                }
                if(!isset($_POST['descripcionModDis']) ||  $_POST['descripcionModDis'] == ""){
                    $descripcionModDis = NULL;
                }else{
                    $descripcionModDis = $_POST['descripcionModDis'];
                }

                $resultadomod = registrar_modeloDispositivo($nombreModDis, $marcaDisSelect, $descripcionModDis);
                //echo "CACA";


                //MENSAJE DE RETROALIMENTACION
                if($resultadomod == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El modelo de dispositivo ha sido registrado de manera exitosa!
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear el modelo de dispositivo :c
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }

            }


//obtiene los valores de la forma del registro de usuario
            if(isset($_POST['rol_usr']) && $_POST['rol_usr'] != ""){

                //echo "SI ESTA ENTRANDO";
                if(!isset($_POST['nombre_usr']) ||  $_POST['nombre_usr'] == ""){
                    $errorrazon = '<a class ="error">*</a>';
                }else{
                    $nombre_usr = $_POST['nombre_usr'];
                }
                if(!isset($_POST['apellido1_usr']) ||  $_POST['apellido1_usr'] == ""){
                    $errorrfc = '<a class ="error">*</a>';
                }else{
                    $apellido1_usr = $_POST['apellido1_usr'];
                }
                if(!isset($_POST['apellido2_usr']) ||  $_POST['apellido2_usr'] == ""){
                    $errorcolonia = '<a class ="error">*</a>';
                }else{
                    $apellido2_usr = $_POST['apellido2_usr'];
                }
                if(!isset($_POST['rfc_usr']) ||  $_POST['rfc_usr'] == ""){
                    $errorcalle = '<a class ="error">*</a>';
                }else{
                    $rfc_usr = $_POST['rfc_usr'];
                }
                if(!isset($_POST['mote_usr']) ||  $_POST['mote_usr'] == ""){
                    $errornum = '<a class ="error">*</a>';
                }else{
                    $mote_usr = $_POST['mote_usr'];
                }
                if(!isset($_POST['pass_usr']) ||  $_POST['pass_usr'] == ""){
                    $errornum = '<a class ="error">*</a>';
                }else{
                    $pass_usr = $_POST['pass_usr'];
                    $pass_usr = hash("sha256",$pass_usr);
                }
                /*if(!isset($_POST['colonia_usr']) ||  $_POST['colonia_usr'] == ""){
                    $errorciudad = '<a class ="error">*</a>';
                }else{
                    $colonia_usr = $_POST['colonia_usr'];
                }*/
                if(!isset($_POST['calle_usr']) ||  $_POST['calle_usr'] == ""){
                    $errorestado = '<a class ="error">*</a>';
                }else{
                    $calle_usr = $_POST['calle_usr'];
                }
                if(!isset($_POST['num1_usr']) ||  $_POST['num1_usr'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $num1_usr = $_POST['num1_usr'];
                }
                if(!isset($_POST['num2_usr']) ||  $_POST['num2_usr'] == ""){
                    $num2_usr = NULL;
                }else{
                    $num2_usr = $_POST['num2_usr'];
                }
                if(!isset($_POST['ciudad_usr']) ||  $_POST['ciudad_usr'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $ciudad_usr = $_POST['ciudad_usr'];
                }
                if(!isset($_POST['estado_usr']) ||  $_POST['estado_usr'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $estado_usr = $_POST['estado_usr'];
                }
                if(!isset($_POST['cp_usr']) ||  $_POST['cp_usr'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $cp_usr = $_POST['cp_usr'];
                }
                if(!isset($_POST['mail_usr']) ||  $_POST['mail_usr'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $mail_usr = $_POST['mail_usr'];
                }
                if(!isset($_POST['rol_usr']) ||  $_POST['rol_usr'] == ""){
                    $errorcp = '<a class ="error">*</a>';
                }else{
                    $rol_usr = $_POST['rol_usr'];
                }
                if (isset($_FILES["Foto"]) && $_FILES["Foto"]["name"] != ""){
                $id_usuario = obten_id();
                $id_usuario++;
                $Foto = $_FILES['Foto'];
                $target_dir = "../img_usuarios/".$id_usuario;
                $target_file = $target_dir . basename($_FILES["Foto"]["name"]);
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
                $Foto = "https://www.dpreview.com/files/p/articles/7395606096/Google-Photos.jpeg";
                $error_foto = "";
                $uploadOk = 1;
            }




                $resultadousr = registrar_usuario($nombre_usr, $apellido1_usr, $apellido2_usr, $rfc_usr, $mote_usr, $pass_usr, $calle_usr, $num1_usr, $num2_usr, $ciudad_usr, $estado_usr, $cp_usr, $mail_usr, $rol_usr, $Foto);
                //echo $foto_usr;




                //MENSAJE DE RETROALIMENTACION
                if($resultadousr == TRUE || $uploadOk == 1){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El usuario ha sido registrado de manera exitosa!
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear un nuevo usuario :c '.$error_foto.'
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }

            }

//Registra un tipo de dispositivo en la base de datos
        if(isset($_POST['claseTipoDis']) && $_POST['claseTipoDis'] != ""){

            //echo "SI ESTA ENTRANDO";
            if(!isset($_POST['nombreTipoDis']) ||  $_POST['nombreTipoDis'] == ""){
                $errorrazon = '<a class ="error">*</a>';
            }else{
                $nombreTipoDis = $_POST['nombreTipoDis'];
            }

            if(!isset($_POST['claseTipoDis']) ||  $_POST['claseTipoDis'] == ""){
                $errorrazon = '<a class ="error">*</a>';
            }else{
                $claseTipoDis = $_POST['claseTipoDis'];
            }
            if(!isset($_POST['comentarioTipoDis']) ||  $_POST['comentarioTipoDis'] == ""){
                $comentarioTipoDis = NULL;
            }else{
                $comentarioTipoDis = $_POST['comentarioTipoDis'];
            }

            $resultadomod = registrar_tipoDispositivo($nombreTipoDis, $claseTipoDis, $comentarioTipoDis);
            //echo "CACA";


            //MENSAJE DE RETROALIMENTACION
            if($resultadomod == TRUE){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡El tipo de dispositivo ha sido registrado de manera exitosa!
                    </div>';
                echo '<script>
                    setTimeout(function(){$("#notify").remove();}, 4000);
                    </script>';
            }else{
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al crear el modelo de dispositivo :c
                    </div>';
                echo '<script>
                    setTimeout(function(){$("#notify").remove();}, 4000);
                    </script>';
            }

        }



//COSAS DE JAVIER. NO TOCAR.
                if(isset($_POST['categoriaSer']) && $_POST['categoriaSer'] != ""){


                if(!isset($_POST['nombreSer']) ||  $_POST['nombreSer'] == ""){
                    $errorn = '<a class ="error">*</a>';
                }else{
                    $nombreSer = $_POST['nombreSer'];
                }
                if(!isset($_POST['tiempEdeT']) ||  $_POST['tiempEdeT'] == ""){
                    $errort = '<a class ="error">*</a>';
                }else{
                    $tiempEdeT = $_POST['tiempEdeT'];
                }
                if(!isset($_POST['categoriaSer']) ||  $_POST['categoriaSer'] == ""){
                    $errorc = '<a class ="error">*</a>';
                }else{
                    $categoriaSer = $_POST['categoriaSer'];
                }
                if(!isset($_POST['esc']) ||  $_POST['esc'] == ""){
                    $errord = '<a class ="error">*</a>';
                }else{
                    $esc = $_POST['esc'];
                }

                $resultdep = registrar_servcio($nombreSer, $tiempEdeT, $categoriaSer, $esc);

                //MENSAJE DE RETROALIMENTACION
                if($resultdep == TRUE){
                    echo '<div id="notify" class="alert alert-success" role="alert">
                        ¡El servicio ha sido registrada de manera exitosa!
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }else{
                    echo '<div id="notify" class="alert alert-danger"    role="alert">
                        Hubo un error al crear el servicio
                        </div>';
                    echo '<script>
                        setTimeout(function(){$("#notify").remove();}, 4000);
                        </script>';
                }

            }


//COSAS DE DORIS NO TOCAR
            if(isset($_GET["result"]) &&  $_GET["result"]==1){
                switch($_GET["creo"]){
                    case 'mesa':
                        echo '<div id="notify" class="alert alert-success" role="alert">
                            ¡La mesa se ha sido registrado de manera exitosa!
                            </div>';
                        echo '<script>
                            setTimeout(function(){$("#notify").remove();}, 3000);
                            </script>';
                break;
            }
          } else if(isset($_GET["result"]) &&  $_GET["result"]==0){
            switch($_GET["creo"]){
              case 'mesa':   echo '<div id="notify" class="alert alert-success" role="alert">
                            ¡hubo un error al crear la mesa!
                            </div>';
                            echo '<script>
                            setTimeout(function(){$("#notify").remove();}, 3000);
                            </script>';
                            break;
            }
          }
          include('../html/Ajustes/_ajustes.html');
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
