<?php
    $menu = "inventario";
    session_start();
    if (isset($_SESSION["Usuario"])){

    require_once('accessDataBase.php');
    include('../html/_header.html');
    include('../html/_menu.html');

    function getdispositivo(){
      $options='<option  value="">selecciona clase de dispositivo...</option>';
      $query = 'SELECT Id_clase_dispositivo, Nombre FROM Clases_de_dispositivos';
        $clases= get_data($query);
          foreach ($clases as $clase) {
            $options.= '<option value="'.$clase['Id_clase_dispositivo'].'">'.$clase['Nombre'].'</option>';
          }
          return $options;
    }
    function getmarca(){
      $options='<option  value="">selecciona la marca...</option>';
      $query = 'SELECT Id_marca_dispositivo, Nombre FROM Marca_de_dispositivos';
      $marcas= get_data($query);
      foreach ($marcas as $marca) {
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
    function getdependencia() {
      $options='<option value="">seleccione la dependencia</option>';
      $query = 'SELECT DE.Id_destino,DE.Razon_social FROM Dependencias D, Destino DE WHERE D.Id_destino = DE.Id_destino GROUP BY Nombre_contacto';
      $dependencias= get_data($query);
      foreach ($dependencias as $dependencia) {
        $options.='<option value="'.$dependencia['Id_destino'].'">'.$dependencia['Razon_social'].'</option>';
      }
      return $options;
    }
    function getmesa() {
      $options='<option value="">seleccione la mesa</option>';
      $query = 'SELECT DE.Id_destino, DE.Razon_social FROM Mesas M, Destino DE WHERE M.Id_destino = DE.Id_destino';
      $mesas= get_data($query);
      foreach ($mesas as $mesa) {
        $options.='<option value="'.$mesa['Id_mesa'].'">'.$mesa['Razon_social'].'</option>';
      }
      return $options;
    }

    include('../html/Ajustes/Dispositivos/_buscar_dispositivo.html');
    include('../html/_footer.html');
     } else {
        header("location:/index.php");
    }


    /*
    function getmodelo() {
      $options='<option value="">seleccione el modelo</option>';
      $query = 'SELECT Id_modelo_dispositivo, Nombre FROM Modelos_de_dispositivos';
      $modelos= get_data($query);
      foreach ($modelos as $modelo) {
        $options.='<option value="'.$modelo['Id_modelo_dispositivo'].'">'.$modelo['Nombre'].'</option>';
       }
       return $options;
    }
    */
?>
