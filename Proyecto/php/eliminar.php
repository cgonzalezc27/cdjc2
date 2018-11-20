<?php
session_start();
require_once('accessDataBase.php');
if(isset($_SESSION["Usuario"])){
  if($_SESSION["Permisos"][6] == TRUE){
    $table=$_GET['table'];
    $id=$_GET['id'];
    switch ($table) {
      case 'Dependencias':   $con=connect();
                              if($con){
                                 $con->query("SET NAMES 'utf-8'");
                                 try{
                                   $con->beginTransaction();
                                   $query='DELETE FROM Dependencias WHERE Id_destino=?';
                                   $values=[$id];
                                   if(delete($query,$values)){
                                     $query='DELETE FROM Destino WHERE Id_destino=?';
                                     if(delete($query,$values)){
                                       $con->commit();
                                       header('location:./_consultar_clientes.php?result=1');

                                     }
                                   }
                                 }catch(Exception $e){
                                   $con->rollback();

                                 }
                              }
                              header('location:./_consultar_clientes.php?result=0');

        break;

      default:
        // code...
        break;
    }
  }else{
    session_unset();
    session_destroy();
    header("location:/index.php");
  }
}else {
    header("location:/index.php");
}


?>
