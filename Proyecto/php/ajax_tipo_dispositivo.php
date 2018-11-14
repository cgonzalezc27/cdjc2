<?php
    require_once 'accessDataBase.php';

    $id_clase = $_POST["Id_clase_dispositivo"];
    $query = 'SELECT Id_tipo_dispositivo, Nombre FROM Tipo_de_dispositivos WHERE Id_clase_dispositivo='.$id_clase;

    $tipo_dis = get_data($query);

    $res= '<option value="">selecciona tipo de dispositivo...</option>';

    //var_dump($_POST['Id_dispositivo']);

    foreach ($tipo_dis as $value) {
      $res.= '<option value="'.$value["Id_tipo_dispositivo"].'"';

      $res .= (isset($_POST['Id_dispositivo']) && $_POST['Id_dispositivo'] == $value['Id_tipo_dispositivo']) ? ' selected>'.$value["Nombre"].'</option>' : '>'.$value["Nombre"].'</option>';
    }

    echo $res;
?>
