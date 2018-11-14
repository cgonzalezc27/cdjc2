<?php
    $menu = "inventario";
    session_start();


    if (isset($_SESSION["Usuario"])){
      require_once('accessDataBase.php');
      include('../html/_header.html');
      include('../html/_menu.html');

      function getclase(){
        $options='<option value="">seleccione una clase dispositivo...</option>';
        $query = 'SELECT Id_clase_dispositivo, Nombre FROM Clases_de_dispositivos';
        $clases= get_data($query);

        foreach($clases as $clase){
          $options.='<option value="'.$clase['Id_clase_dispositivo'].'">'.$clase['Nombre'].'</option>';
        }
        return $options;
      }

      function getmarca(){
        $options='<option  value="">selecciona la marca...</option>';
        $query = 'SELECT Id_marca_dispositivo, Nombre FROM Marca_de_dispositivos';
        $marcas= get_data($query);

        foreach($marcas as $marca){
          $options.= '<option value="'.$marca['Id_marca_dispositivo'].'">'.$marca['Nombre'].'</option>';
        }
        return $options;
      }

      function getmesapropietaria(){
        $options='<option value="">seleccione mesa propietaria...</option>';
        $query = 'SELECT Id_mesa, Razon_social FROM Mesas m INNER JOIN Destino d on m.Id_destino = d.Id_destino';
        $mesas= get_data($query);

        foreach ($mesas as $mesa) {
          $options.='<option value="'.$mesa['Id_mesa'].'">'.$mesa['Razon_social'].'</option>';
        }
        return $options;
      }

      if(isset($_GET["result"]) &&  $_GET["result"]==1){
        echo '<div id="notify" class="alert alert-success" role="alert"> ¡El dispositivo se ha sido registrado de manera exitosa! </div>';
        echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
      }else if(isset($_GET["result"]) &&  $_GET["result"]==0){
        echo '<div id="notify" class="alert alert-danger" role="alert"> ¡El dispositivo NO se ha registrado por falta de campos! </div>';
        echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
      }else{

      }

        }else{
      header("location:/index.php");
    }
    include('../html/Ajustes/Dispositivos/_registrar_dispositivo.html');
    include('../html/_footer.html');


    /*
    function getdispositivo(){
      $options='<option>selecciona tipo de dispositivo</option>';
        return $options;
    }
    */
    /*
    function getmodelo() {
      $options='<option value="">seleccione el modelo...</option>';
      $query = 'SELECT Id_modelo_dispositivo, Nombre FROM Modelos_de_dispositivos';
      $modelos= get_data($query);
      foreach ($modelos as $modelo) {
        $options.='<option value="'.$modelo['Id_modelo_dispositivo'].'">'.$modelo['Nombre'].'</option>';
       }
       return $options;
    }
    */
?>
