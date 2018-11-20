<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
      require_once('accessDataBase.php');
        include('../html/_header.html');
        include('../html/_menu.html');
        if(isset($_GET["result"]) &&  $_GET["result"]==1){
          echo '<div id="notify" class="alert alert-success" role="alert"> ¡la dependencia se ha eliminado manera exitosa! </div>';
          echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
        }else if(isset($_GET["result"]) &&  $_GET["result"]==0){
          echo '<div id="notify" class="alert alert-danger" role="alert"> ¡la dependencia NO se ha eliminado! </div>';
          echo '<script>setTimeout(function(){$("#notify").remove();}, 3000);</script>';
        }
        include('../html/Ajustes/Clientes/_consultar_clientes.html');
        include('../html/_footer.html');
     } else {
        header("location:/index.php");
    }
?>
