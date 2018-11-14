<?php
require_once('globals.php');


//Funcion para crear una conexion con la base de datos
function connect2(){
    $conexion = mysqli_connect($GLOBALS["host"],$GLOBALS["user"],$GLOBALS["pass"],$GLOBALS["db"]);
    if($conexion == NULL) {
        die("Error al conectarse con la base de datos/ error de conexion". mysqli_connect_errno() . PHP_EOL);
    }
    $conexion->set_charset("utf8");
    return $conexion;
}

function clean($x){
    $x = trim($x);
    $x = htmlspecialchars($x);
    $x = stripcslashes($x);
    return $x;
}

function cerrar_sesion(){
    session_start();
    session_unset();
    session_destroy();
}

function disconnect2($conexion) {
    mysqli_close($conexion);
}

function obtener_mesas(){
    $conexion = connect2();
    
    $query = ("SELECT NombreM FROM NombresMesas;");

    $results = mysqli_query($conexion, $query);


    $i = 0;
    $rows = [];
    echo '<option value="">Seleccione una Mesa...</option>';
    while($row = mysqli_fetch_array($results,MYSQLI_BOTH)){
        echo '<option value="'.$row['NombreM'].'">'.$row['NombreM'].'</option>';
    }
    mysqli_free_result($results);
    disconnect2($conexion);
}

function obtener_tiempos(){
    $asignador = 0;
    $duracion = "";
    for ($c=1;$c <= 20; $c++){
        if ($asignador == 0){
            $duracion .=  "<option value=''>Seleccione el tiempo esperado de respuesta...</option>";
        } else {
            $duracion .=  "<option value".$asignador.">".$asignador." horas</option>";
        }
        $asignador = $asignador + 0.5;
        
    }
    echo $duracion;
}

//Funcion para registrar una nueva dependencia

function registrar_dependencia($razonSdep, $rfcdep, $calledep, $num1dep, $num2dep, $ciudaddep, $estadodep, $cpdep, $encargadodep, $tesperadep, $mesadep, $comentariodep){
    $conexion = connect2();


    
//INSERTA UN DESTINO EN LA TABLA DESTINO
    $query="INSERT INTO Destino (Razon_social,RFC,Estado,Ciudad,Calle,Numero_exterior,Numero_interior,CP,Descripcion) values('".$razonSdep."','".$rfcdep."','".$estadodep."','".$ciudaddep."','".$calledep."','".$num1dep."','".$num2dep."',".$cpdep.",'".$comentariodep."')";
    $result = mysqli_query($conexion, $query);
//INSERTA EN LA TABLA DEPENDENCIA LA DEPENDENCIA, ESTO GENERA LA RELACION ENTRE DEPENDENCIA Y MESA DE AYUDA
    $query= "SELECT Id_destino FROM Destino WHERE Razon_social = '".$razonSdep."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_destino = $row["Id_destino"];
    //echo "id_destino: ". $id_destino;
    
    $query= "SELECT Id_mesa FROM NombresMesas WHERE NombreM = '".$mesadep."'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_array($result);
    $id_mesa = $row["Id_mesa"];
    //echo "id_mesa: ".$id_mesa;

    $query="INSERT INTO Dependencias(Id_destino,Nombre_contacto,Exigencia_tiempo_respuesta,Id_mesa) values('".$id_destino."','".$encargadodep."','".$tesperadep."','".$id_mesa."')";
    $result = mysqli_query($conexion, $query);
    
    return $result;
    disconnect2($conexion);
}

/*if(isset($_POST['submit'])){
    registrar_dependencia();
}
   */ 
?>
