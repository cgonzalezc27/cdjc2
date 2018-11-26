<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
      require_once 'accessDataBase.php';
        include('../html/_header.html');
        include('../html/_menu.html');

        function getconsultaDependencia(){
          $query='  SELECT nm.Id_mesa,Destino.Id_destino,Razon_social,RFC,Estado,Ciudad,Calle,Numero_exterior,Numero_interior,CP,Descripcion,d.Nombre_contacto AS Persona,d.Exigencia_tiempo_respuesta AS Tiempo, nm.NombreM AS mesa
                    FROM Destino
                    INNER JOIN Dependencias d on Destino.Id_destino = d.Id_destino
                    INNER JOIN NombresMesas nm ON d.Id_mesa=nm.Id_mesa
                    WHERE Destino.Id_destino="'.$_GET["id"].'" AND RFC="'.$_GET["rfc"].'"';

          $obtener=get_data($query);
          return $obtener;
        }
        function obtener_tiempos($tiempo){
            $asignador = 0;
            $duracion = "";
            for ($c=1;$c <= 20; $c++){
                if ($asignador == 0){
                    $duracion .=  '<option value="">Seleccione el tiempo esperado de respuesta...</option>';
                } else {
                  if($asignador.' horas'==$tiempo){
                    $duracion .=  '<option value"'.$asignador.' horas" selected>'.$asignador.' horas</option>';
                  }else{
                    $duracion .=  '<option value"'.$asignador.' horas">'.$asignador.' horas</option>';
                  }

                }
                $asignador = $asignador + 0.5;

            }
            return $duracion;
        }



          $dependencias=getconsultaDependencia();


          $dependencia=$dependencias[0];

          $tiempos=obtener_tiempos($dependencia['Tiempo']);

          if(isset($_GET["result"]) &&  $_GET["result"]==1){
            echo '<div id="notify" class="alert alert-success" role="alert"> ¡La dependencia ha sido modificado de manera exitosa! </div>';
            echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
          }else if(isset($_GET["result"]) &&  $_GET["result"]==0){
            echo '<div id="notify" class="alert alert-danger" role="alert"> Hubo un error al modificar la dependencia </div>';
            echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
          }else{

          }

        include('../html/Ajustes/Clientes/_modificar_cliente.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
