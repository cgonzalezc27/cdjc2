<?php
require_once 'accessDataBase.php';

$query='SELECT m.Id_mesa,d.Razon_social, d.RFC, d.Calle,d.Estado,d.Ciudad, m.Correo_electronico, d.Descripcion
        FROM Mesas m
        INNER JOIN NombresMesas nm ON m.Id_mesa=nm.Id_mesa
        INNER JOIN Destino d ON m.Id_destino=d.Id_destino


       ';

       $conditions=[];

       if(isset($_POST["razonS"]) && $_POST["razonS"]!=""){
         $conditions[]='Razon_social LIKE "%'.$_POST["razonS"].'%"';
       }

       if(isset($_POST["rfc"]) && $_POST["rfc"]!=""){
         $conditions[]='RFC LIKE "%'.$_POST["rfc"].'%"';
       }

       if(sizeof($conditions)>0){
         $query.=' WHERE ';
         foreach($conditions as $condition){
           $query.=$condition." and ";
         }
         $query=substr($query, 0, sizeof($query) -6);
       }

       $result=get_data($query);

       $res='<h4 class="display-4 titulo_pagina">Mesas de Ayuda </h4><br>
       <table class="table table-hover table-responsive-sm>
       <thead>
       <tr class="text-center" style="text-align:center;">
       <th>Razón Social</th>
       <th>RFC</th>
       <th>Dirección</th>
       <th>Estado</th>
       <th>Ciudad</th>
       <th>Email</th>
       <th>Comentario</th>
       </tr>
       </thead>
      <tbody>
      ';

      if($result){

        foreach ($result as $fila) {

         $redirect="redirecttoMesa('".$fila['Id_mesa']."', '".$fila['RFC']."')";
           $res.='<tr clas="text-center" style="cursor:pointer;text-align:center;" onclick="'.$redirect.'">
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

      $res.="</tbody></table>";

      echo $res;

?>
