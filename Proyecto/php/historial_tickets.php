<?php
  echo '<div class="container">';

  for($i=0;$i<3;$i++){
    echo '<div class="row">';

    for($j=0;$j<3;$j++){
      echo'<div class="col">
        <a href="irc_mut" class="form-group col-md-6">';
      switch ($j) {
        case 0:
        echo '
            <img class="irc_mut" alt="Imagen relacionada" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzlpQ1m7VdecdOHbiVbTUML8FxsyksTRIk2M8tflUDuLTERNry" width="104" height="104" style="margin-top: 25px;">
          </a>
          <div class="desc">information del ticket en proceso</div>
          </div>';
          break;
        case 1:
        echo '
          <img class="irc_mut" width="104" height="104" style="margin-top: 25px;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQKeNi8UfkoW4VqTqWOBF5oP4tcNI47ZYMqITmgzqwv0oSY4L-oQQ" alt="Resultado de imagen para ticket icon">
        </a>
        <div class="desc">information del ticket concluso</div>
        </div>';
          break;
        case 2:
        echo '<img class="irc_mut" width="104" height="104" style="margin-top: 25px;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSdw6rAN0uGAJk3uFFXhH8yMTVVdm_FWnFczLTqQiijFn01sKS9" alt="Imagen relacionada">
        </a>
        <div class="desc">information del ticket cancelado</div>
        </div>';
        break;
      }
    }
    echo '</div>';
  }
?>
