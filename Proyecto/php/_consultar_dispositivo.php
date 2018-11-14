<?php
    $menu = "inventario";

    session_start();
    require_once 'accessDataBase.php';
    if (isset($_SESSION["Usuario"])){
        include('../html/_header.html');
        include('../html/_menu.html');

        function createTable(){
          $query='SELECT Id_dispositivo, Fecha_hora, dtd.Id_destino, d.Razon_social
                  FROM Destinos_tickets_dispositivos dtd
                  INNER JOIN Destino d ON dtd.Id_destino = d.Id_destino
                  WHERE Id_dispositivo="'.$_GET["id"].'"
                  ORDER BY Fecha_hora DESC
                  ';

            $dependencias=get_data($query);

          $table="";
          $aux = $dependencias[0]["Razon_social"];
          for ($i=0; $i < sizeof($dependencias) - 1 ; $i++) {
            $table.='<tr class="text-center">
                      <td>'.substr($dependencias[$i]["Fecha_hora"], 0, 10).'</td>
                      <td>'.substr($dependencias[$i]["Fecha_hora"], 11, 8).'</td>
                      <td>'.$dependencias[$i+1]["Razon_social"].'</td>
                      <td>'.$aux.'</td>
                    </tr>';

            $aux=$dependencias[$i+1]["Razon_social"];
          }

          $table.='<tr class="text-center">
                    <td>'.substr($dependencias[sizeof($dependencias)-1]["Fecha_hora"], 0, 10).'</td>
                    <td>'.substr($dependencias[sizeof($dependencias)-1]["Fecha_hora"], 11, 8).'</td>
                    <td>  </td>
                    <td>'.$dependencias[sizeof($dependencias)-1]["Razon_social"].'</td>
                  </tr>';

          return $table;
        }


        function getconsultaDispositivo(){

          $query= 'SELECT d.Id_dispositivo AS id, No_serie_equipo, cdd.Nombre AS Clase, t.Nombre AS Tipo, madd.Nombre AS marca, mdd.Nombre AS Modelo, nm.NombreM AS mesa, dst.Razon_social as destino, dtd.Fecha_hora AS fecha
          FROM Dispositivos d
          INNER JOIN Tipo_de_dispositivos t ON d.Id_tipo_dispositivo = t.Id_tipo_dispositivo
          INNER JOIN Clases_de_dispositivos cdd on t.Id_clase_dispositivo = cdd.Id_clase_dispositivo
          INNER JOIN Marca_de_dispositivos madd ON d.Id_marca_dispositivo = madd.Id_marca_dispositivo
          INNER JOIN Modelos_de_dispositivos mdd ON madd.Id_marca_dispositivo = mdd.Id_marca_dispositivo
          INNER JOIN NombresMesas nm ON d.Id_mesa = nm.Id_mesa
          INNER JOIN Destinos_tickets_dispositivos dtd ON d.Id_dispositivo = dtd.Id_dispositivo
          INNER JOIN Destino dst ON dtd.Id_destino = dst.Id_destino
          WHERE dtd.Fecha_hora=(SELECT MAX(Fecha_hora) FROM Destinos_tickets_dispositivos WHERE Id_dispositivo=d.Id_dispositivo )
          AND d.Id_dispositivo="'.$_GET["id"].'" AND dtd.Fecha_hora="'.$_GET["fecha"].'"
          GROUP BY d.Id_dispositivo ORDER BY Fecha_hora DESC
          ';


           $obtener=get_data($query);

            return $obtener;

        }
        $dispositivos=getconsultaDispositivo();


        $dispositivo=$dispositivos[0];


        if(isset($_GET["result"]) && $_GET["result"]==1){
          echo '<div id="notify" class="alert alert-success" role="alert"> ¡El dispositivo se ha sido modificado de manera exitosa! </div>';
          echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
        }else if(isset($_GET["result"]) &&  $_GET["result"]==0){
          echo '<div id="notify" class="alert alert-danger" role="alert"> Hubo un error al modificar el dispositivo </div>';
          echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
        }else{

        }

        include('../html/Ajustes/Dispositivos/_consultar_dispositivo.html');
        include('../html/_footer.html');


    } else {
        header("location:/index.php");
    }

?>
