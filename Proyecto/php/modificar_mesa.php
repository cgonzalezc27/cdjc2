<?php
  $menu = "inventario";
  session_start();

  if(isset($_SESSION["Usuario"])){
    require_once 'accessDataBase.php';
    include('../html/_header.html');
    include('../html/_menu.html');

    function getmodificarMesa(){
      $query='SELECT m.Id_mesa,d.Razon_social, d.RFC, d.Calle,d.Numero_exterior,d.Numero_interior,d.CP,d.Estado,d.Ciudad, m.Correo_electronico, d.Descripcion
              FROM Mesas m
              INNER JOIN NombresMesas nm ON m.Id_mesa=nm.Id_mesa
              INNER JOIN Destino d ON m.Id_destino=d.Id_destino
              WHERE m.Id_mesa="'.$_GET["id"].'" AND RFC="'.$_GET["rfc"].'"
              ';

              $obtener=get_data($query);
              return $obtener;
    }
    $mesas=getmodificarMesa();

    $mesa=$mesas[0];

    include('../html/Ajustes/Mesas/modificarMesa.html');
    include('../html/_footer.html');

  }else{
    header("location:/index.php");
  }


?>
