
document.getElementById("menu_tickets").onclick = expande_tickets;
document.getElementById("menu_inventario").onclick = expande_inventario;
document.getElementById("menu_clientes").onclick = expande_clientes;
document.getElementById("sidebarCollapse").onclick = expande_menu;
document.getElementById("btn_main_inventario_registrar_dispositivo").onclick = ir_ahora_registrar_dispositivo;
document.getElementById("btn_main_inventario_registrar_movimiento").onclick = ir_ahora_registrar_movimiento;
document.getElementById("btn_main_inventario_buscar_dispositvo").onclick = ir_ahora_buscar_dispositvo;
document.getElementById("btn_main_inventario_tickets_abiertos").onclick = ir_ahora_tickets_abiertos;
document.getElementById("btn_main_inventario_crear_ticket").onclick = ir_ahora_crear_ticket;



function expande_tickets(){
    x = document.getElementById("submenu_tickets")
    y = x.classList.length
   if(x.classList[y-1] == "collapse"){
       x.classList.remove("collapse");
   } else {
       x.classList.add("collapse");
   }
}
function expande_inventario(){
    x = document.getElementById("submenu_inventario")
    y = x.classList.length
   if(x.classList[y-1] == "collapse"){
       x.classList.remove("collapse");
   } else {
       x.classList.add("collapse");
   }
}
function expande_clientes(){
    x = document.getElementById("submenu_clientes")
    y = x.classList.length
   if(x.classList[y-1] == "collapse"){
       x.classList.remove("collapse");
   } else {
       x.classList.add("collapse");
   }
}
function expande_menu(){
    x = document.getElementById("sidebar")
    y = x.classList.length
   if(x.classList[y-1] == "collapse"){
       x.classList.remove("collapse");
   } else {
       x.classList.add("collapse");
   }
}
function ir_ahora_registrar_dispositivo(){
    window.location.href = "../php/_registrar_dispositivo.php"
}
function ir_ahora_registrar_movimiento(){
    window.location.href = "../php/_registrar_movimiento.php"
}
function ir_ahora_buscar_dispositvo(){
    window.location.href = "../php/_buscar_dispositivo.php"
}
function ir_ahora_tickets_abiertos(){
    window.location.href = "../php/_tickets_abiertos.php"
}
function ir_ahora_crear_ticket(){
    window.location.href = "../php/_crear_ticket.php"
}

// Genera lista de dependendencias/mesas para registrar movimiento
function dependencias_mesas(){
    $.get("../php/controller_registrar_movimientos.php",{tipo_destino: document.getElementById("tipo_destino").value})
        .done(function(data){
        var response = document.getElementById('destino_select');
        response.style.display = "block";
        response = document.getElementById('response');
        response.innerHTML = data;
    })
}
// Genera lista de tickets abiertos para relacionarlos a un movimiento
function ticket_relacionado(){
    if (document.getElementById("relacionar_ticket").value == "off"){
        document.getElementById("relacionar_ticket").value = "on";
    } else if (document.getElementById("relacionar_ticket").value == "on"){
        document.getElementById("relacionar_ticket").value = "off";
    }
    $.get("../php/controller_registrar_movimientos.php",{relacionar_ticket: document.getElementById("relacionar_ticket").value})
        .done(function(data){
        var response2 = document.getElementById('ticket_select');
        response2.style.display = "block";
        response2 = document.getElementById('response2');
        response2.innerHTML = data;
    })
}
// Generar dependencias para modificar ticket
function dependencias_modificar_ticket(){
    $.get("../php/controller_registrar_movimientos.php",{mesa_modificar_ticket: document.getElementById("mesa").value})
        .done(function(data){
        var response = document.getElementById('dependencia');
        response.innerHTML = data;
    })
}
// Generar servicios para modificar ticket


// Generar ingenieros para modificar ticket
function no_ingenieros_modificar_ticket(){
    $.get("../php/controller_registrar_movimientos.php",{no_ingenieros: document.getElementById("NoIngenieros").value,id:document.getElementById("Id_ticket").value})
        .done(function(data){
        var response = document.getElementById('listas_ing');
        response.innerHTML = data;
    })
}

function no_ingenieros_modificar_ticket_cambio_ingeniero(x){        
    var y = document.getElementById("no_listas").value;
    
    var id_ingenieros = "";

    for (a = 1; a <= y; a++){
        var id_ingenieros = id_ingenieros + "id_ingeniero" + a + ":document.getElementById('inge" + a + "')[document.getElementById('inge" + a + "').selectedIndex].id,";
    }
    
    var nombre_ingenieros = "";
    
    for (a = 1; a <= y; a++){
        var nombre_ingenieros = nombre_ingenieros + "nombre_ingeniero" + a + ":document.getElementById('inge" + a + "').value";
        if (a != y){
            nombre_ingenieros = nombre_ingenieros + ",";
        }

    }
    eval(
        '$.get("../php/controller_registrar_movimientos.php",{ingeniero_elegido: document.getElementById("inge"+x).value,id:document.getElementById("Id_ticket").value, listainge:x, no_listas:' + y + ',' + id_ingenieros + nombre_ingenieros +'}).done(function(data){var response = document.getElementById("listas_ing");response.innerHTML = data;})'
    )
}


// Generar servicios para modificar ticket

function no_servicios_modificar_ticket(){
    $.get("../php/controller_registrar_movimientos.php",{no_servicios: document.getElementById("NoServicios").value,id:document.getElementById("Id_ticket").value})
        .done(function(data){
        var response = document.getElementById('listas_serv');
        response.innerHTML = data;
    })
}

function no_ingenieros_modificar_ticket_cambio_servicio(x,z){        
    var y = document.getElementById("no_listas_servicio").value;
    
    var id_categorias = "";

    for (a = 1; a <= y; a++){
        var id_categorias = id_categorias + "id_categoria" + a + ":document.getElementById('Catservicio" + a + "')[document.getElementById('Catservicio" + a + "').selectedIndex].id,";
    }
    
    var nombre_categorias = "";
    
    for (a = 1; a <= y; a++){
        var nombre_categorias = nombre_categorias + "nombre_categoria" + a + ":document.getElementById('Catservicio" + a + "').value,";

    }
    
    var id_servicios = "";

    for (a = 1; a <= y; a++){
        var id_servicios = id_servicios + "id_servicio" + a + ":document.getElementById('servicio" + a + "')[document.getElementById('servicio" + a + "').selectedIndex].id,";
    }
    if (z == 1){
        var id_servicios = id_servicios + "cambio_categoria:" + z + ",";
    }
    var nombre_servicios = "";
    
    for (a = 1; a <= y; a++){
        var nombre_servicios = nombre_servicios + "nombre_servicio" + a + ":document.getElementById('servicio" + a + "').value";
        if (a != y){
            nombre_servicios = nombre_servicios + ",";
        }

    }
    eval(
        '$.get("../php/controller_registrar_movimientos.php",{id:document.getElementById("Id_ticket").value, listaserv:' + x + ', no_listas:' + y + ',' + id_categorias + nombre_categorias + id_servicios + nombre_servicios +'}).done(function(data){var response = document.getElementById("listas_serv");response.innerHTML = data;})'
    )
}

function servicios_modificar_ticket(x){
    
    no_ingenieros_modificar_ticket_cambio_servicio(x,1);
    
}

//Genera Dependencia
function dependencias_mesas_crearT(){
    $.get("../php/controller_registrar_movimientos.php",{SelMesa: document.getElementById("SelMesa").value})
        .done(function(data){
        var response = document.getElementById('dependencia_select');
        response.style.display = "block";
        response = document.getElementById('response');
        response.innerHTML = data;
    })
}

//Genera Categoria de Servicio
function categoria_servicio(){
    $.get("../php/controller_registrar_movimientos.php",{cateSer: document.getElementById("cateSer").value})
        .done(function(data){
        var response = document.getElementById('cat_ser_select');
        response.style.display = "block";
        response = document.getElementById('response_cat_ser');
        response.innerHTML = data;
    })
}

//Genera Selecionar Servicio
function servicio_ingeniero(){
    $.get("../php/controller_registrar_movimientos.php",{Ser: document.getElementById("Ser").value})
        .done(function(data){
        var response = document.getElementById('ser_select');
        response.style.display = "block";
        response = document.getElementById('response_ser');
        response.innerHTML = data;
    })
}

//Libera categoria de servicio
function categoriade_servicio_muestra(){
        var response = document.getElementById('categoria_select');
        response.style.display = "block";
}

//Libera duracion estandar
function duracion_estimada(){
        var response = document.getElementById('testimado_select');
        response.style.display = "block";
        var response2 = document.getElementById('fecha_hora_select');
        response2.style.display = "block";
}

//Libera categoria de servicio
function categoriade_servicio_muestra(){
        var response = document.getElementById('categoria_select');
        response.style.display = "block";
}


//Libera Seleccionar número de ingenieros
function muestra_num_inges(){
        var response = document.getElementById('num_inges_select');
        response.style.display = "block";
}


//Genera Selecionar Ingeniero
function ingeniero_elegible(){
    $.get("../php/controller_registrar_movimientos.php",{Ser: document.getElementById("Ser").value, num_inges: document.getElementById("num_inges").value })
        .done(function(data){
        var response = document.getElementById('ser_select');
        response.style.display = "block";
        response = document.getElementById('response_ser');
        response.innerHTML = data;
    })
}

//Seleccionar Ingeniero 2

function ingeniero_elegible2(){
    $.get("../php/controller_registrar_movimientos.php",{Ser: document.getElementById("Ser").value, Ing: document.getElementById("Ing").value, num_inges: document.getElementById("num_inges").value })
        .done(function(data){
        var response = document.getElementById('ing2_select');
        response.style.display = "block";
        response = document.getElementById('response_ing2');
        response.innerHTML = data;
    })
}

//Seleccionar Ingeniero 3

function ingeniero_elegible3(){
    $.get("../php/controller_registrar_movimientos.php",{Ser: document.getElementById("Ser").value, Ing: document.getElementById("Ing").value, Ing2: document.getElementById("Ing2").value, num_inges: document.getElementById("num_inges").value})
        .done(function(data){
        var response = document.getElementById('ing3_select');
        response.style.display = "block";
        response = document.getElementById('response_ing3');
        response.innerHTML = data;
    })
}

//Seleccionar Ingeniero 4

function ingeniero_elegible4(){
    $.get("../php/controller_registrar_movimientos.php",{Ser: document.getElementById("Ser").value, Ing: document.getElementById("Ing").value, Ing2: document.getElementById("Ing2").value, Ing3: document.getElementById("Ing3").value,num_inges: document.getElementById("num_inges").value})
        .done(function(data){
        var response = document.getElementById('ing4_select');
        response.style.display = "block";
        response = document.getElementById('response_ing4');
        response.innerHTML = data;
    })
}

//Libera transporte
function ing_transporte(){
        var response = document.getElementById('trans_select');
        response.style.display = "block";
}

//Libera comentario y boton
function boton_aparece(){
        var response = document.getElementById('comentario_select');
        response.style.display = "block";
        var response2 = document.getElementById('boton_select');
        response2.style.display = "block";
}

//Consultar