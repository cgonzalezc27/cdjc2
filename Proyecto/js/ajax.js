$(document).ready(function (){
        let pathname = window.location.pathname;
        if(pathname.includes("_modificar_dispositivo.php")){
          actualizar_tipo_dis(idDispositivo);//var del script de _modificcar_dispositivo.php
          marca_modelo_dispositivo(idModelo);
        }
      });


function actualizar_tipo_dis(dispositivo=''){
  let clase= $('#clase_dispositivo').val();
  let data;
  if(dispositivo!=''){
    data={
      Id_clase_dispositivo: clase,
      Id_dispositivo: dispositivo
    }
  }else{
    data={
      Id_clase_dispositivo: clase
    }
  }


  //let clase= $('#clase_dispositivo').val();
    $.ajax({
      url: "ajax_tipo_dispositivo.php",
      type: 'post',
      data: data,
      success:function(response){


        $("#tipo_dispositivo").html(response);
      }
    });


}

function marca_modelo_dispositivo(modelo=''){
  let marca= $('#marca').val();
  let data;
  if(modelo!=''){
    data={
      //bd                 //let
      Id_marca_dispositivo: marca,
      Id_modelo_dispositivo: modelo
    }
  }else{
    data={
      Id_marca_dispositivo: marca
    }
  }
  $.ajax({
    url:"ajax_modelo_marca_dis.php",
    type:'post',
    data: data,
    success:function(response){$("#modelo").html(response);}
  })
}

function desplegar_destino(){
  let destino=$('#destino').val();//obtener valor
  //reflejarlo de a cuerdo al 1 o 2
    if(destino == 1){
      $('#div_cliente').removeAttr("style");
      $('#div_mesa').css("display","none");
    }else{
      $('#div_mesa').removeAttr("style");
      $('#div_cliente').css("display","none");
    }
}

function actualizar_busqueda(){
  let forma=$('#forma_buscar_dis').serialize();
  $('#table').remove();

  $.ajax({
    url: "model_buscar_dispositivo.php", //procesamiento
    type: 'post',
    data: forma,
    success:function(response){

      $("#table_query").append('<div id="table"></div>');
     $("#table").append(response);

    }
  })
}

function actualizar_busqueda_dependencias(){
  let forma=$('#forma_buscar_dep').serialize();
  $('#table').remove();

  $.ajax({
    url: "model_consultar_dependencia.php", //procesamiento
    type: 'post',
    data: forma,
    success:function(response){

      $("#table_query").append('<div id="table"></div>');
     $("#table").append(response);

    }
  })
}
function actualizar_busqueda_mesas(){
  let forma=$('#forma_buscar_mesa').serialize();
  $('#table').remove();

  $.ajax({
    url: "model_consultar_mesa.php", //procesamiento
    type: 'post',
    data: forma,
    success:function(response){

      $("#table_query").append('<div id="table"></div>');
     $("#table").append(response);

    }
  })
}

function consulta_tipo_dispo() {
  let form=$('#forma_buscar_dispositivo').serialize();
  $('#table').remove();

  $.ajax({
    url: "model_consulta_tipo_dis.php",
    type: 'post',
    data: forma,
    success: function(response){
      
        $("#table_query").append('<div id="table"></div>');
        $("#table").append(response);

    }
  })
}
function redirectto(id,fecha){
//redirigir
  window.location.replace("./_consultar_dispositivo.php?id="+id+"&fecha="+fecha);

}

function redirecttoDep(Id_destino,RFC){

  window.location.replace("./_modificar_cliente.php?id="+Id_destino+"&rfc="+RFC);

}

function redirecttoMesa(Id_mesa,RFC){

  window.location.replace("./_modificar_mesa_ayuda.php?id="+Id_mesa+"&rfc="+RFC);

}

function redirecttoConsultaDep(Id_destino,RFC){

  window.location.replace("./_consulta_mesa_dep.php?id="+Id_destino+"&rfc="+RFC);

}
