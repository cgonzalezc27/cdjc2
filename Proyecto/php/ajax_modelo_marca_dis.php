<?php

  require_once 'accessDataBase.php';

  $query='SELECT Id_modelo_dispositivo,Nombre
          FROM Modelos_de_dispositivos
          WHERE Id_marca_dispositivo="'.$_POST['Id_marca_dispositivo'].'"';

 $modelos=get_data($query);

 $options='<option value="">seleccione modelo dispositivo...</option>';
 foreach ($modelos as $modelo) {
   $options.='<option value="'.$modelo['Id_modelo_dispositivo'].'"';

   $options.= (isset($_POST["Id_modelo_dispositivo"]) && $_POST["Id_modelo_dispositivo"]==$modelo['Id_modelo_dispositivo'])? ' selected>'.$modelo['Nombre'].'</option>' : '>'.$modelo['Nombre'].'</option>';
 }
 echo $options;
 ?>
