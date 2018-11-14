<?php

  require_once 'accessDataBase.php';

  $query= 'insert into Dispositivos (No_serie_equipo,Id_marca_dispositivo, Id_modelo_dispositivo,Id_tipo_dispositivo,Id_mesa,comentario) values(?, ?, ?, ?, ?, ?)';
  $values= [
    //name
    $_POST['NoSerie'],
    $_POST['marca'],
    $_POST['modelo_dis'],
    $_POST['tipo_dis'],
    $_POST['mesa'],
    $_POST['comentario'],
  ];

  $result=inserts($query, $values);
    if($result !== false){
            header('Location: ./_registrar_dispositivo.php?result=1'); //cambiar a donde enviar despues de crear dispositivo
            //aquí poner notificación
    }else{
    header('Location: ./_registrar_dispositivo.php?result=0');
    }


?>
