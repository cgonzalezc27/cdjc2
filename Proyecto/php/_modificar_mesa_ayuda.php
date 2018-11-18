<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
      require_once 'accessDataBase.php';
        include('../html/_header.html');
        include('../html/_menu.html');

        function getconsultaMesa(){
          $query='SELECT m.Id_mesa,d.Razon_social, d.RFC, d.Calle,d.Numero_exterior,d.Numero_interior,d.CP,d.Estado,d.Ciudad, m.Correo_electronico, d.Descripcion 
                  FROM Mesas m
                  INNER JOIN NombresMesas nm ON m.Id_mesa=nm.Id_mesa
                  INNER JOIN Destino d ON m.Id_destino=d.Id_destino
                  WHERE m.Id_mesa="'.$_GET["id"].'" AND RFC="'.$_GET["rfc"].'"
                  ';

                  $obtener=get_data($query);
                  return $obtener;
        }

        $mesas=getconsultaMesa();

        $mesa=$mesas[0];

        if(isset($_GET["result"]) &&  $_GET["result"]==1){
          echo '<div id="notify" class="alert alert-success" role="alert"> ¡La mesa se ha modificado de manera exitosa! </div>';
          echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
        }else if(isset($_GET["result"]) &&  $_GET["result"]==0){
          echo '<div id="notify" class="alert alert-danger" role="alert"> Hubo un error al modificar la mesa </div>';
          echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
        }else{

        }

        include('../html/Ajustes/Mesas/_modificar_mesa_ayuda.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
