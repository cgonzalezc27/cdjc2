<?php
  require_once 'accessDataBase.php';
  $query='INSERT INTO Destino (Razon_social,RFC,Estado,Ciudad,Calle,Numero_exterior,Numero_interior,CP,Descripcion) values(?,?,?,?,?,?,?,?,?)';
  $values= [
    $_POST['razonS'],
    $_POST['rfc'],
    $_POST['estado'],
    $_POST['ciudad'],
    $_POST['calle'],
    $_POST['no_ext'],
    $_POST['no_int'],
    $_POST['cp'],
    $_POST['descripcion'],
  ];

  $con=connect();
  if($con){
  //  var_dump($con);

     $con->query("SET NAMES 'utf-8'");
     try{
       $con->beginTransaction();
       $affectedRows=$con->prepare($query)->execute($values);
       var_dump($affectedRows);
       if($affectedRows !== false){
         $idDestino=$con->lastInsertId();//recuperas el destino(anterior query)
          $query='INSERT INTO Mesas (Id_destino, Correo_electronico) values(?,?)';
          $values=[$idDestino, $_POST['correo']];
           $affectedRows=$con->prepare($query)->execute($values);

           if($affectedRows!== false){
              $con->commit();
               header('Location: ./_ajustes.php?result=1&creo=mesa');
           } else{
             $con->rollback();
              header('Location: ./_ajustes.php?result=0&creo=mesa');
           }
       }

     }catch(Exception $e){
       $con->rollback();
       header('Location: ./_ajustes.php?result=0&creo=mesa');
     }
  }else{
    header('Location: ./_ajustes.php?result=0&creo=mesa');
  }


//  $result=inserts($query,$values);
  /*  if($result!== false){
      header('Location: ./_registrar_dispositivo.php?result=1');
    }else {
      header('Location: ./_registrar_dispositivo.php?result=0');
    }
  */
?>
