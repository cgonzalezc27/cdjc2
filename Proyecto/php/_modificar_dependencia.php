<?php
  $menu = "inventario";
  session_start();

  if(isset($_SESSION["Usuario"])){
    require_once('globals.php');
    require_once 'accessDataBase.php';
    include('../html/_header.html');
    include('../html/_menu.html');

    function getModificarDependencia(){

      $query='SELECT nm.Id_mesa,d.Id_destino,d.Razon_social AS razon_social,d.RFC AS rfc,d.Calle,d.Numero_exterior,d.Numero_interior,d.Ciudad,d.Estado,d.CP, dep.Nombre_contacto AS persona, dep.Exigencia_tiempo_respuesta AS tiempo, d.Descripcion, nm.NombreM AS mesa
              FROM Destino d
              INNER JOIN Dependencias dep ON d.Id_destino=dep.Id_destino
              INNER JOIN NombresMesas nm ON dep.Id_mesa=nm.Id_mesa
              WHERE d.Id_destino="'.$_GET["id"].'" AND rfc="'.$_GET["rfc"].'"';

      $obtener=get_data($query);

      return $obtener;
    }
    function getMesas($idMesa){
      $query='SELECT Id_mesa,NombreM
              FROM NombresMesas';

              $mesas=get_data($query);
              $options='';

              foreach ($mesas as $mesa) {
                if($mesa['Id_mesa']==$idMesa){
                  $options.='<option value="'.$mesa['Id_mesa'].'" selected>'.$mesa['NombreM'].'</option>';
                }else{
                  $options.='<option value="'.$mesa['Id_mesa'].'">'.$mesa['NombreM'].'</option>';
                }
              }
              return $options;
    }
    function obtener_tiempos($tiempo){
        $asignador = 0;
        $duracion = "";
        for ($c=1;$c <= 20; $c++){
            if ($asignador == 0){
                $duracion .=  '<option value="">Seleccione el tiempo esperado de respuesta...</option>';
            } else {
              if($asignador.' horas'==$tiempo){
                $duracion .=  '<option value"'.$asignador.' horas" selected>'.$asignador.' horas</option>';
              }else{
                $duracion .=  '<option value"'.$asignador.' horas">'.$asignador.' horas</option>';
              }

            }
            $asignador = $asignador + 0.5;

        }
        return $duracion;
    }
    $dependencias=getModificarDependencia();


    $dependencia=$dependencias[0];
    $mesas=getMesas($dependencia['Id_mesa']);
    $tiempos=obtener_tiempos($dependencia['tiempo']);


    include('../html/Ajustes/Clientes/_modificar_dependencia.html');
    include('../html/_footer.html');

  }else{
    header("location:/index.php");
  }


?>
