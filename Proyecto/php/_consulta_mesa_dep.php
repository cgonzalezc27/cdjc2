<?php
    $menu = "ajustes";
    session_start();
    if (isset($_SESSION["Usuario"])){
      require_once 'accessDataBase.php';
        include('../html/_header.html');
        include('../html/_menu.html');

        function generateCardsDep(){
          $query='  SELECT Id_destino,Razon_social,RFC,calle
                    FROM Destino
                    WHERE Destino.Id_destino="'.$_GET["id"].'"
                  ';

          $dependencias=get_data($query);
          //var_dump($dependencias);
          $cards='';
          foreach ($dependencias as $dependencia) {
            $cards.='<div class = "col-lg-4 col-md-6 col-sm-7">
                <div class="card window-card">
                    <div class="row">
                        <div class = "col-3">
                                <div class = "cliente_ingeniero"></div>
                        </div>
                        <div class="col-9" style="cursor:pointer;">
                            <h6 class="control">'.$dependencia["Razon_social"].'</h6>
                            <ul class="list-unstyled">
                                <li style="font-size:15px;">RFC: '.$dependencia["RFC"].'</li>
                                <li style="font-size:15px;">Calle: '.$dependencia["calle"].' </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>';
          }
          return $cards;
        }
        include('../html/Ajustes/Clientes/Mesas_dependencias.html');
        include('../html/_footer.html');
    } else {
      session_unset();
      session_destroy();
      header("location:/index.php");
    }
?>
