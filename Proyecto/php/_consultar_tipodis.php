<?php
$menu = "ajustes";
session_start();
if (isset($_SESSION["Usuario"])){
  require_once('accessDataBase.php');
  include('../html/_header.html');
  include('../html/_menu.html');

  include('../html/Ajustes/Dispositivos/_consultar_tipodis.html');
  include('../html/_footer.html');
}else{
  header("location:/index.php");
}
?>
