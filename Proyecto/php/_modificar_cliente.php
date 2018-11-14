<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
      require_once 'accessDataBase.php';
        include('../html/_header.html');
        include('../html/_menu.html');

        function getconsultaDependencia(){
          $query='  SELECT Destino.Id_destino,Razon_social,RFC,Estado,Ciudad,Calle,Numero_exterior,Numero_interior,CP,Descripcion,d.Nombre_contacto AS Persona,d.Exigencia_tiempo_respuesta AS Tiempo
                    FROM Destino
                    INNER JOIN Dependencias d on Destino.Id_destino = d.Id_destino
                    WHERE Destino.Id_destino="'.$_GET["id"].'" AND RFC="'.$_GET["rfc"].'"';

          $obtener=get_data($query);
          return $obtener;
        }


          $dependencias=getconsultaDependencia();


          $dependencia=$dependencias[0];

          if(isset($_GET["result"]) &&  $_GET["result"]==1){
            echo '<div id="notify" class="alert alert-success" role="alert"> ¡El dispositivo se ha sido registrado de manera exitosa! </div>';
            echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
          }else if(isset($_GET["result"]) &&  $_GET["result"]==0){
            echo '<div id="notify" class="alert alert-danger" role="alert"> Hubo un error al crear el dispositivo </div>';
            echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
          }else{

          }

        include('../html/Ajustes/Clientes/_modificar_cliente.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
