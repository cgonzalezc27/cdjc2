<?php
require_once('globals.php');

 function connect(){
   try{
     //$GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"],$GLOBALS["db"]
     $connection= new PDO('mysql:dbname='.$GLOBALS["db"].';host='.$GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"]);
     return $connection;
   }catch(PDOException $e){
     echo 'Error al conectarse con la base de datos';
     return false;
   }
 }
 function disconnect($conexion) {
   mysqli_close($conexion);
 }
 function inserts ($query, $values){
   $con= connect();
   $con->query("SET NAMES 'utf8'");
   if($con){
     $affectedRows=$con->prepare($query)->execute($values);
     $result=false;
     if($affectedRows !== false){
       $result=$con->lastInsertId();
     }
     $con=NULL;
     return $result;
   }
   return false;
 }

 function get_data ($query){
   $con= connect();
   if($con){
     $con->query("SET NAMES 'utf8'");
     $result= $con->query($query);
     if($result){
       $con= null;
       return $result->fetchAll(PDO::FETCH_ASSOC);
     }
   }
   return false;
 }

 function update($query,$values){
   $con= connect();
   $con->query("SET NAMES 'utf8'");
   if($con){
     $affectedRows=$con->prepare($query)->execute($values);
     $result=false;
     if($affectedRows !== false){
       $result = true;
     }
     $con=NULL;
     return $result;
   }
   return false;
 }

 function delete ($query, $values){
   $con= connect();
   $con->query("SET NAMES 'utf8'");
   if($con){
     $affectedRows=$con->prepare($query)->execute($values);
     $result=false;
     if($affectedRows !== false){
       $result = true;
     }
     $con=NULL;
     return $result;
   }
   return false;
 }

?>
