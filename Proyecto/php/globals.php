<?php
 $GLOBALS["env"] = "prod";

 switch($GLOBALS["env"]){
     case "dev_camilo":
         $GLOBALS["host"] = "ssynsupport.com";
         $GLOBALS["pass"] = "1.2.3.4.";
         $GLOBALS["user"] = "camilo123";
         $GLOBALS["db"] = "Administrador_ticketsDB_dev";
         break;
     case "prod":
         $GLOBALS["host"] = "localhost";
         $GLOBALS["pass"] = "y)l*wOmLO0e";
         $GLOBALS["user"] = "adminssyn";
         $GLOBALS["db"] = "Administrador_ticketsDB_dev";
         break;
         
 }
?>