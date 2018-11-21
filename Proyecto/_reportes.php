
<html xmlns="http://www.w3.org/1999/xhtml", PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Ingenieros</title>

<link rel="stylesheet" href="../css/styl.css" />

</head>

<body>
<div id="content">

<h1>Ingenieros</h1>

<hr />

<?php
    
	include_once("conexion.php");

	$con = new DB();
	$usuarios = $con -> conectar();
	$strConsulta = "SELECT Id_usuario, Nombre_de_usuario, Nombre, Apellido1, Apellido2 from usuarios";
	$usuarios = mysqli_query($con->conect, $strConsulta);
	$numfilas = mysqli_num_rows($usuarios);
	
	echo '<table cellpadding="0" cellspacing="0" width="100%">';
	echo '<thead><tr><td>No.</td><td>CLAVE</td><td>NOMBRE</td><td>REPORTES</td></tr></thead>';
	for ($i=0; $i<$numfilas; $i++)
	{
		$fila = mysqli_fetch_array($usuarios);
		$numlista = $i + 1;
		echo '<tr><td>'.$numlista.'</td>';
		echo '<td>'.$fila['Nombre_de_usuario'].'</td>';
        echo '<td>'.$fila['Nombre'].' '.$fila['Apellido1'].' '.$fila['Apellido2'].'</td>';
		echo '<td><a href="reporte_historial.php?id='.$fila['Id_usuario'].'">ver</a></td></tr>';
	}
	echo "</table>";
?>			

</div>
</body>
</html>