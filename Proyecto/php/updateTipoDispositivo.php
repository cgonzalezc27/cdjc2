<?php
require_once 'accessDataBase.php';
var_dump($_POST);
  $query='UPDATE Tipo_de_dispositivos SET Nombre=?,Id_clase_dispositivo=?,Descripcion=? WHERE Id_tipo_dispositivo="'.$_GET['id'].'"';
  $values=[
    $_POST['nombre'],
    $_POST['clase'],
    $_POST['descripcion']
  ];

  if(update($query,$values)){
    header("location: ./_modificar_tipodis.php?id=".$_GET['id']."&result=1");
  }else{
      header("location: ./_modificar_tipodis.php?id=".$_GET['id']."&result=0");
  }
?>
