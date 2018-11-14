<?php
    require_once 'accessDataBase.php';

    $query='UPDATE Dispositivos SET No_serie_equipo=?, Id_tipo_dispositivo=?, Id_marca_dispositivo=?, Id_modelo_dispositivo=?, Id_mesa=?, Comentario=?
    WHERE Id_dispositivo="'.$_GET['id'].'"';
    $values=[
      $_POST['NoSerie'],
      $_POST['tipo_dis'],
      $_POST['marca'],
      $_POST['modelo_dis'],
      $_POST['mesa'],
      $_POST['comentario']
    ];

    if(update($query,$values)){
      header("location: ./_consultar_dispositivo.php?id=".$_GET['id']."&fecha=".$_GET['fecha']."&result=1");


    }else{
      header("location: ./_consultar_dispositivo.php?id=".$_GET['id']."&fecha=".$_GET['fecha']."&result=0");

    }

 ?>
