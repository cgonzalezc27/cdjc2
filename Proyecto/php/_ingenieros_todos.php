<?php
    $menu = "ingenieros";
    session_start();
    require_once('accessDataBase.php');
    if (isset($_SESSION["Usuario"])){
        if($_SESSION["Permisos"][6] == TRUE){
          include('../html/_header.html');
          include('../html/_menu.html');
          function generateCards(){
            $query='SELECT usr.Nombre AS NombreUsr, usr.Apellido1 AS Apellido1, usr.Apellido2 AS Apellido2, edt.Nombre AS NR, t.No_ticket AS NT
            FROM Usuarios usr
            INNER JOIN Ingenieros i ON usr.Id_usuario = i.Id_usuario
            INNER JOIN Ingenieros_Tickets it ON i.Id_ingeniero = it.Id_ingeniero
            INNER JOIN Tickets t ON it.Id_ticket = t.Id_ticket
            INNER JOIN Tickets_Estatus_de_tickets tet ON t.Id_ticket = tet.Id_ticket
            INNER JOIN  Estatus_de_tickets edt ON tet.Id_estatus = edt.Id_estatus
            WHERE i.Visible = 1
            GROUP BY usr.Nombre
            ORDER BY usr.Nombre';
    
                    $mesas=get_data($query);
                    //var_dump($mesas);
                    $cards='';
                    foreach ($mesas as $mesa) {
                      //$redirect="redirecttoConsultaDep('".$mesa['Id_destino']."', '".$mesa['RFC']."')";
                      $cards.='<div class = "col-lg-4 col-md-6 col-sm-7">
                          <div class="card window-card">
                              <div class="row">
                                  <div class = "col-3">
                                          <div class = "cliente_ingeniero"></div>
                                  </div>
                                  <div class="col-9" style="cursor:pointer;>
                                      <h6 class="control">'.$mesa["NombreUsr"].'</h6>
                                      <ul class="list-unstyled">
                                          <li style="font-size:15px;">Estatus: '.$mesa["NR"].'</li>
                                          <li style="font-size:15px;">Ticket: '.$mesa["NT"].'</li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                      </div>';
                    }
                    return $cards;
          }
          include('../html/Ingenieros/_ingenieros_todos.html');
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