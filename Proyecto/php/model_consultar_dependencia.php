<?php
require_once 'accessDataBase.php';

$query='SELECT Destino.Id_destino,Razon_social,RFC,Estado,Ciudad,Calle,Descripcion,d.Nombre_contacto AS Persona,d.Exigencia_tiempo_respuesta AS Tiempo
        FROM Destino
        INNER JOIN Dependencias d on Destino.Id_destino = d.Id_destino';


$conditions=[];
if(isset($_POST["razonS"]) && $_POST["razonS"]!=""){
    $conditions[]='Razon_social LIKE "%'.$_POST["razonS"].'%"';
}

if(isset($_POST["rfc"]) && $_POST["rfc"]!=""){
    $conditions[]='RFC LIKE "%'.$_POST["rfc"].'%"';
}

if(sizeof($conditions)>0){
  $query.=' WHERE ';
    foreach ($conditions as $condition) {
      $query.=$condition." and ";
    }
    $query=substr($query, 0, sizeof($query) -6);
}


$result=get_data($query);

$res='<table>
        <thead>
            <tr style="text-align:center;">
              <th>Razón Social</th>
              <th>RFC</th>
              <th>Dirección</th>
              <th>Estado</th>
              <th>Ciudad</th>
              <th>Persona Encargada</th>
              <th>Tiempo Espera</th>
              <th>Comentario</th>
            </tr>
        </thead>
      <tbody>';

if($result){

  foreach ($result as $fila) {
   $redirect="redirecttoDep('".$fila['Id_destino']."', '".$fila['RFC']."')";

    $res.='<tr style="cursor:pointer;text-align:center;" onclick="'.$redirect.'">
              <td>'.$fila['Razon_social'].'</td>
              <td>'.$fila['RFC'].'</td>
              <td>'.$fila['Calle'].'</td>
              <td>'.$fila['Estado'].'</td>
              <td>'.$fila['Ciudad'].'</td>
              <td>'.$fila['Persona'].'</td>
              <td>'.$fila['Tiempo'].'</td>
              <td>'.$fila['Descripcion'].'</td>
          </tr>';
    }
}


$res.="</tbody></table>";


echo $res;

?>
