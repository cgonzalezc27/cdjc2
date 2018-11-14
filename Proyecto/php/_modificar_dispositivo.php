<?php
    $menu = "inventario";
    session_start();

    if (isset($_SESSION["Usuario"])){
        require_once 'accessDataBase.php';
        include('../html/_header.html');
        include('../html/_menu.html');
        
        if(isset($_GET['eliminado'])){
            if($_GET['eliminado'] == 1){
                echo '<div id="notify" class="alert alert-success" role="alert">
                    ¡El movimiento ha sido eliminado de manera exitosa!
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
              </script>';
            } else {
                echo '<div id="notify" class="alert alert-danger"    role="alert">
                    Hubo un error al eliminar el movimiento
                    </div>';
                echo '<script>
                  setTimeout(function(){$("#notify").remove();}, 3000);
                </script>'; 
            } 
            
        }
        function getclase($id=false){
          $options='<option value="">seleccione una clase dispositivo...</option>';
          $query = 'SELECT Id_clase_dispositivo, Nombre FROM Clases_de_dispositivos';
          $clases= get_data($query);

          foreach($clases as $clase){
            $options.='<option value="'.$clase['Id_clase_dispositivo'].'"';
            $options.=($id!=false && $id==$clase['Id_clase_dispositivo'])? ' selected>'.$clase['Nombre'].'</option>' : '>'.$clase['Nombre'].'</option>';
          }

          return $options;
        }


        function getmarca($id=false){
          $options='<option  value="">selecciona la marca...</option>';
          $query = 'SELECT Id_marca_dispositivo, Nombre FROM Marca_de_dispositivos';
          $marcas= get_data($query);

          foreach($marcas as $marca){
            $options.= '<option value="'.$marca['Id_marca_dispositivo'].'"';
            $options.=($id!=false && $id==$marca['Id_marca_dispositivo'])?' selected>'.$marca['Nombre'].'</option>' : '>'.$marca['Nombre'].'</option>';

          }
          return $options;
        }


        function getmesapropietaria($id=false){
          $options='<option value="">seleccione mesa propietaria...</option>';
          $query = 'SELECT Id_mesa, Razon_social FROM Mesas m INNER JOIN Destino d on m.Id_destino = d.Id_destino';
          $mesas= get_data($query);

          foreach ($mesas as $mesa) {
            $options.='<option value="'.$mesa['Id_mesa'].'"';
            $options.=($id!=false && $id==$mesa['Id_mesa'])?' selected>'.$mesa['Razon_social'].'</option>' : '>'.$mesa['Razon_social'].'</option>';
          }
          return $options;
        }


        function createTable(){
          $query='SELECT Id_dispositivo, Fecha_hora, dtd.Id_destino, d.Razon_social
                  FROM Destinos_tickets_dispositivos dtd
                  INNER JOIN Destino d ON dtd.Id_destino = d.Id_destino
                  WHERE Id_dispositivo="'.$_GET["id"].'"
                  ORDER BY Fecha_hora DESC
                  ';

            $dependencias=get_data($query);

          $table="";
          $aux ['Razon_social']= $dependencias[0]["Razon_social"];
          $aux ['Id_destino']= $dependencias[0]["Id_destino"];
          $aux ['Fecha_hora']= $dependencias[0]["Fecha_hora"];
          $aux ['Id_dispositivo']= $dependencias[0]["Id_dispositivo"];
          for ($i=0; $i < sizeof($dependencias) - 1 ; $i++) {
            
              $table.='<tr class="text-center">
                      <td>'.substr($dependencias[$i]["Fecha_hora"], 0, 10).'</td>
                      <td>'.substr($dependencias[$i]["Fecha_hora"], 11, 8).'</td>
                      <td>'.$dependencias[$i+1]["Razon_social"].'</td>
                      <td>'.$aux['Razon_social'].'</td>
                      <td>
                      <a href="../php/_eliminar_movimiento.php?eliminar_usuario=1&Id_destino='.$aux['Id_destino'].'&Id_dispositivo='.$aux['Id_dispositivo'].'&Fecha_hora='.$aux['Fecha_hora'].'"><i class="material-icons">delete_sweep</i> Eliminar movimiento</a>
                      
                      </td>
                    </tr>';

            $aux['Razon_social']=$dependencias[$i+1]["Razon_social"];
            $aux['Id_destino']=$dependencias[$i+1]["Id_destino"];
            $aux['Id_dispositivo']=$dependencias[$i+1]["Id_dispositivo"];
            $aux['Fecha_hora']=$dependencias[$i+1]["Fecha_hora"];
          }

          $table.='<tr class="text-center">
                    <td>'.substr($dependencias[sizeof($dependencias)-1]["Fecha_hora"], 0, 10).'</td>
                    <td>'.substr($dependencias[sizeof($dependencias)-1]["Fecha_hora"], 11, 8).'</td>
                    <td>  </td>
                    <td>'.$dependencias[sizeof($dependencias)-1]["Razon_social"].'</td>
                  </tr>';

          return $table;
        }

        function getModificarDis(){

          $query='SELECT d.Id_dispositivo AS id, No_serie_equipo, cdd.Id_clase_dispositivo AS Clase, t.Id_tipo_dispositivo AS Tipo, madd.Id_marca_dispositivo AS marca, mdd.Id_modelo_dispositivo AS Modelo, nm.Id_mesa AS mesa, dst.Id_destino as destino, dtd.Fecha_hora AS fecha, d.Comentario AS comentario
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
          GROUP BY d.Id_dispositivo ORDER BY Fecha_hora DESC';

          $obtener=get_data($query);

           return $obtener;
        }

        $dispositivos=getModificarDis();


        $dispositivo=$dispositivos[0];


        include('../html/Ajustes/Dispositivos/_modificar_dispositivo.html');

        echo '<script>
                var idDispositivo = "'.$dispositivo['Tipo'].'";
                var idModelo = "'.$dispositivo['Modelo'].'";
              </script>';

        include('../html/_footer.html');
    } else {
        header("location:/index.php");
    }
?>
