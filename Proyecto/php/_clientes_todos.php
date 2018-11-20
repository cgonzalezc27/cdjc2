<?php
    $menu = "mesas";
    session_start();
    require_once('accessDataBase.php');
    if (isset($_SESSION["Usuario"])){
        if($_SESSION["Permisos"][6] == TRUE){
          include('../html/_header.html');
          include('../html/_menu.html');
          function generateCards(){
            $query='SELECT m.Id_mesa,d.Id_destino,d.Razon_social, d.RFC, d.Calle,d.Numero_exterior,d.Numero_interior,d.CP,d.Estado,d.Ciudad, m.Correo_electronico, d.Descripcion
                    FROM Mesas m
                    INNER JOIN NombresMesas nm ON m.Id_mesa=nm.Id_mesa
                    INNER JOIN Destino d ON m.Id_destino=d.Id_destino
                    ';
                    $mesas=get_data($query);
                    //var_dump($mesas);
                    $cards='';
                    foreach ($mesas as $mesa) {
                      $redirect="redirecttoConsultaDep('".$mesa['Id_destino']."', '".$mesa['RFC']."')";
                      $cards.='<div class = "col-lg-4 col-md-6 col-sm-7">
                          <div class="card window-card">
                              <div class="row">
                                  <div class = "col-3">
                                          <div class = "cliente_ingeniero"></div>
                                  </div>
                                  <div class="col-9" style="cursor:pointer;" onclick="'.$redirect.'">
                                      <h6 class="control">'.$mesa["Razon_social"].'</h6>
                                      <ul class="list-unstyled">
                                          <li style="font-size:15px;">RFC: '.$mesa["RFC"].'</li>
                                          <li style="font-size:15px;">Email: '.$mesa["Correo_electronico"].' </li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                      </div>';
                    }
                    return $cards;
          }
          include('../html/Ajustes/Clientes/_clientes_todos.html');
          include('../html/_footer.html');
        }else {
         session_unset();
         session_destroy();
         header("location:/index.php");
       }
    }else {
        header("location:/index.php");
    }
?>
