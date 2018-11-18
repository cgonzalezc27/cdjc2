<?php
require_once 'accessDataBase.php';

$queryInnner='SELECT Id_destino
              From Mesas
              WHERE Id_mesa="'.$_GET['id'].'"
              ';
              $hacerInner=get_data($queryInnner);
              $id_destino= $hacerInner[0]['Id_destino'];


$queryMesa='UPDATE Destino SET Razon_social=?, RFC=?, Calle=?,Numero_exterior=?, Numero_interior=?,CP=?,Estado=?,Ciudad=?, Descripcion=?
            WHERE Id_destino="'.$id_destino.'"
           ';
           $valuesMesa=[
             $_POST['razonSocial'],
             $_POST['rfc'],
             $_POST['calle'],
             $_POST['numExterior'],
             $_POST['numInterior'],
             $_POST['cp'],
             $_POST['estado'],
             $_POST['ciudad'],
             $_POST['comentario']
           ];
  $queryMesaCorreo=' UPDATE Mesas SET Correo_electronico=?
                  WHERE Id_mesa="'.$_GET['id'].'"
                ';
                $valuesMesaCorreo=[
                  $_POST['correo']
                ];
  $con=connect();
  if($con){
    $con->query("SET NAMES 'utf-8'");
    try{
        $con->beginTransaction();
        if((update($queryMesa,$valuesMesa)) && (update($queryMesaCorreo,$valuesMesaCorreo))){
          $query='SELECT rfc FROM Destino WHERE Id_destino="'.$id_destino.'"';
          $rfc=get_data($query)[0]['rfc'];
          $_GET['rfc']=$rfc;

          $con->commit();
          header("location: ./_modificar_mesa_ayuda.php?id=".$_GET['id']."&rfc=".$_GET['rfc']."&result=1");
        }else{
          $con->rollback();
        }
      }catch(Exception $e){
        $con->rollback();
        header('Location: ./_ajustes.php?result=0&creo=mesa');
      }
    }else{
      header('Location: ./_ajustes.php?result=0&creo=mesa');
    }


?>
