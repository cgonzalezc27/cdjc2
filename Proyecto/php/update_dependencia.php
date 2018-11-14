<?php
  require_once 'accessDataBase.php';
  $queryDestino='UPDATE Destino SET Razon_social=?, RFC=?, Calle=?, Numero_exterior=?, Numero_interior=?, Ciudad=?, Estado=?, CP=?, Descripcion=?
                WHERE Id_destino="'.$_GET['id'].'"';
  //var_dump($_POST)para conocer valores
    $valuesDestino=[
      $_POST['razonSocial'],
      $_POST['rfc'],
      $_POST['calle'],
      $_POST['numExterior'],
      $_POST['numInterior'],
      $_POST['ciudad'],
      $_POST['estado'],
      $_POST['cp'],
      $_POST['comentario']
    ];

  $queryDependencia='UPDATE Dependencias SET Nombre_contacto=?, Exigencia_tiempo_respuesta=?
                      WHERE Id_destino="'.$_GET['id'].'"';
    $valuesDependencia=[
      $_POST['persona'],
      $_POST['tiempo']
    ];

  $con=connect();
  if($con){
  //  var_dump($con);

     $con->query("SET NAMES 'utf-8'");
     try{
       $con->beginTransaction();
       if(update($queryDestino,$valuesDestino) && update($queryDependencia,$valuesDependencia)){
        $query='SELECT rfc FROM Destino WHERE Id_destino="'.$_GET['id'].'"';
        $rfc=get_data($query)[0]['rfc'];
        $_GET['rfc']=$rfc;
         $con->commit();
         header("location: ./_modificar_cliente.php?id=".$_GET['id']."&rfc=".$_GET['rfc']."&result=1");

       }else{
         $con->rollback();
         header("location: ./_modificar_cliente.php?id=".$_GET['id']."&rfc=".$_GET['rfc']."&result=0");

       }

     }catch(Exception $e){
       $con->rollback();
       header('Location: ./_ajustes.php?result=0&creo=mesa');
     }
  }else{
    header('Location: ./_ajustes.php?result=0&creo=mesa');
  }


  ?>
