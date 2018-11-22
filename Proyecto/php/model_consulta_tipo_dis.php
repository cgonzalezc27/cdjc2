<?php
require_once 'accessDataBase.php';

$query='SELECT tdd.Id_tipo_dispositivo, tdd.Nombre AS Nombre, tdd.descripcion, cdd.Nombre AS Clase
        FROM Tipo_de_dispositivos tdd
        INNER JOIN Clases_de_dispositivos cdd ON tdd.Id_clase_dispositivo=cdd.Id_clase_dispositivo
       ';

       $conditions=[];

       if(isset($_POST["Nombre"]) && $_POST["Nombre"]!=""){
         $conditions[]='tdd.Nombre LIKE "%'.$_POST["Nombre"].'%"';
       }

       if(sizeof($conditions)>0){
         $query.=' WHERE ';
         foreach($conditions as $condition){
           $query.=$condition." and ";
         }
         $query=substr($query, 0, sizeof($query) -6);
       }

       $result=get_data($query);

       $res='<table class="table table-hover table-responsive-sm>
       <thead>
       <tr class="text-center" style="text-align:center;">
       <th>Nombre</th>
       <th>Descripción</th>
       <th>Clase</th>
       </tr>
       </thead>

       <tbody>
       ';

       if($result){

         foreach ($result as $fila) {
           $redirect="window.location.href='./_modificar_tipodis.php?id=".$fila['Id_tipo_dispositivo']."'";
           $res.='<tr class="text-center" style="cursor:pointer;text-align:center;" onclick="'.$redirect.'">
           <td>'.$fila['Nombre'].'</td>
           <td>'.$fila['descripcion'].'</td>
           <td>'.$fila['Clase'].'</td>
           </tr>';
         }

       }

       $res.="</tbody></table>";

       echo $res;

?>
