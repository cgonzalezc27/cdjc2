<?php
require_once 'accessDataBase.php';
  $query='SELECT tdd.Nombre AS Nombre, tdd.descripcion, cdd.Nombre AS Clase
          FROM Tipo_de_dispositivos tdd
          INNER JOIN Clases_de_dispositivos cdd ON tdd.Id_clase_dispositivo=cdd.Id_clase_dispositivo
         ';
    $conditions[];
    if(isset($_POST["nombre"]) && $_POST["nombre"]!=""){
      $conditions[]='Nombre LIKE "%'.$_POST["nombre"].'%"';
    }
    if(sizeof($conditions)>0){
      $query.=' WHERE ';
      foreach($conditions as $condition){
        $query.=$condition." and ";
      }
      $query=substr($query, 0, sizeof($query) -6);
    }
    $result=get_data($query);
    var_dump($result);
    $res='<table class="table table-hover table-responsive-sm>
            <thead>
                <tr style="text-align:center;">
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Clase</th>
                </tr>
            </thead>
          <tbody>
          ';
    /*
          if($result){

            foreach ($result as $fila) {
             $redirect="redirecttoMesa('".$fila['Id_mesa']."', '".$fila['RFC']."')";
               $res.='<tr class="text-center" style="cursor:pointer;text-align:center;" onclick="'.$redirect.'">
                         <td>'.$fila['Razon_social'].'</td>
                         <td>'.$fila['RFC'].'</td>
                         <td>'.$fila['Calle'].'</td>
                         <td>'.$fila['Estado'].'</td>
                         <td>'.$fila['Ciudad'].'</td>
                         <td>'.$fila['Correo_electronico'].'</td>
                         <td>'.$fila['Descripcion'].'</td>
                     </tr>';
              }
          }
    */
    if($result){

      foreach ($result as $fila) {
         $res.='<tr class="text-center" style="cursor:pointer;text-align:center;">
                   <td>'.$fila['Nombre'].'</td>
                   <td>'.$fila['descripcion'].'</td>
                   <td>'.$fila['Clase'].'</td>
               </tr>';
        }
    }
          $res.="</tbody></table>";

          echo $res;
 ?>
