<?php
require_once 'accessDataBase.php';

$query= 'SELECT d.Id_dispositivo AS id, No_serie_equipo, cdd.Nombre AS Clase, t.Nombre AS Tipo, madd.Nombre AS marca, mdd.Nombre AS Modelo, nm.NombreM AS mesa, dst.Razon_social as destino, dtd.Fecha_hora AS fecha
FROM Dispositivos d
INNER JOIN Tipo_de_dispositivos t ON d.Id_tipo_dispositivo = t.Id_tipo_dispositivo
INNER JOIN Clases_de_dispositivos cdd on t.Id_clase_dispositivo = cdd.Id_clase_dispositivo
INNER JOIN Marca_de_dispositivos madd ON d.Id_marca_dispositivo = madd.Id_marca_dispositivo
INNER JOIN Modelos_de_dispositivos mdd ON madd.Id_marca_dispositivo = mdd.Id_marca_dispositivo
INNER JOIN NombresMesas nm ON d.Id_mesa = nm.Id_mesa
INNER JOIN Destinos_tickets_dispositivos dtd ON d.Id_dispositivo = dtd.Id_dispositivo
INNER JOIN Destino dst ON dtd.Id_destino = dst.Id_destino
WHERE dtd.Fecha_hora=(SELECT MAX(Fecha_hora) FROM Destinos_tickets_dispositivos WHERE Id_dispositivo=d.Id_dispositivo )';

$conditions=[];

//               NAME EN FORMA
if (isset($_POST["NoSerie"]) && $_POST["NoSerie"]!="") {
                  //columna BD                    NAME EN FORMA
  $conditions[]='No_serie_equipo LIKE "%'.$_POST["NoSerie"].'%"';
}

if(isset($_POST["clase_dispositivo"]) && $_POST["clase_dispositivo"]!=""){
                //inner join
  $conditions[]='cdd.Id_clase_dispositivo = "'.$_POST["clase_dispositivo"].'"';
}

if(isset($_POST["tipo_dis"]) && $_POST["tipo_dis"]!=""){
  $conditions[]='t.Id_tipo_dispositivo ="'.$_POST["tipo_dis"].'"';
}

if(isset($_POST["marca"]) && $_POST["marca"]!=""){
  $conditions[]='madd.Id_marca_dispositivo ="'.$_POST["marca"].'"';
}

if(isset($_POST["modelo_dis"]) && $_POST["modelo_dis"]!=""){
  $conditions[]='mdd.Id_modelo_dispositivo ="'.$_POST["modelo_dis"].'"';
}

if(isset($_POST["mesaP"]) && $_POST["mesaP"]!=""){
  $conditions[]='nm.Id_mesa ="'.$_POST["mesaP"].'"';
}

if(isset($_POST["cliente"]) && $_POST["cliente"]){
  $conditions[]='dst.Id_destino ="'.$_POST["cliente"].'"';
}
if(isset($_POST["mesa"]) && $_POST["mesa"]){
  $conditions[]='dst.Id_destino ="'.$_POST["mesa"].'"';
}

if(sizeof($conditions)>0){
  $query.=' AND ';
  foreach ($conditions as $condition) {
    $query.=$condition . " AND ";
  }

  $query=substr($query, 0, sizeof($query) -6);
}


$query.=' GROUP BY d.Id_dispositivo ORDER BY Fecha_hora DESC';


$result=get_data($query);
//echo $query;
$res='<table class="table table-hover table-responsive-sm">
        <thead>
          <tr style="text-align:center;">
            <th># Serie </th>
            <th>Clase </th>
            <th>Tipo </th>
            <th>Marca </th>
            <th>Modelo </th>
            <th>Mesa </th>
            <th>Último Destino </th>
          </tr>
        </thead>
      <tbody>';
if($result){
  foreach ($result as $fila) {
    //redirect=>modificar_depedencia
  $redirect="redirectto('".$fila['id']."', '".$fila['fecha']."')";

    $res.='<tr class="text-center" style="cursor:pointer;text-align:center;" onclick="'.$redirect.'">
                <td>'.$fila['No_serie_equipo'].'</td>
                <td>'.$fila['Clase'].'</td>
                <td>'.$fila['Tipo'].'</td>
                <td>'.$fila['marca'].'</td>
                <td>'.$fila['Modelo'].'</td>
                <td>'.$fila['mesa'].'</td>
                <td>'.$fila['destino'].'</td>
            </tr>';
  }
}


$res.="</tbody></table>";

echo $res;

?>
