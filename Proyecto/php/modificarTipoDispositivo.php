<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
      require_once 'accessDataBase.php';
        include('../html/_header.html');
        include('../html/_menu.html');
        function getTipoDis(){
          $query='SELECT *
                  FROM Tipo_de_dispositivos
                  WHERE Id_tipo_dispositivo="'.$_GET['id'].'"
                 ';

                 $tipoDis= get_data($query);
                 return $tipoDis[0];
        }
        function clase_de_dispositivo($id_clase){
          $query='SELECT Id_clase_dispositivo, Nombre
                  FROM Clases_de_dispositivos
                ';

                $clases= get_data($query);
                $options="";
                foreach ($clases as $clase) {
                  if($clase['Id_clase_dispositivo']==$id_clase){
                    $options.='<option value="'.$clase['Id_clase_dispositivo'].'" selected>'.$clase['Nombre'].'</option>';
                  }else{
                    $options.='<option value="'.$clase['Id_clase_dispositivo'].'">'.$clase['Nombre'].'</option>';
                  }
                }
                return $options;
        }
        $tipo_de_dispo=getTipoDis();
        $clases= clase_de_dispositivo($tipo_de_dispo['Id_clase_dispositivo']);

        

        include('../html/Ajustes/Dispositivos/modificarTipoDispositivo.html');
        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
