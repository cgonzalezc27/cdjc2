<?php
  $menu = "inventario";
  session_start();

  if(isset($_SESSION["Usuario"])){
    require_once 'accessDataBase.php';
    include('../html/_header.html');
    include('../html/_menu.html');

    function getModificarDependencia(){

      $query='SELECT d.Id_destino,d.Razon_social AS razon_social,d.RFC AS rfc,d.Calle,d.Numero_exterior,d.Numero_interior,d.Ciudad,d.Estado,d.CP, dep.Nombre_contacto AS persona, dep.Exigencia_tiempo_respuesta AS tiempo, d.Descripcion
              FROM Destino d
              INNER JOIN Dependencias dep ON d.Id_destino=dep.Id_destino
              WHERE d.Id_destino="'.$_GET["id"].'" AND rfc="'.$_GET["rfc"].'"';

      $obtener=get_data($query);

      return $obtener;
    }
    $dependencias=getModificarDependencia();


    $dependencia=$dependencias[0];


    include('../html/Ajustes/Clientes/_modificar_dependencia.html');
    include('../html/_footer.html');

  }else{
    header("location:/index.php");
  }


?>
