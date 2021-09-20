<?php 
/*
	Archivo que va a ser llamado en formato ajax.
	Retorna un texto plano.
*/

	require_once("escalones.php");
	$escalones = new cEscalones();
	$escalones->escalones = $_GET["escalones"];
	$escalones->BuscarConvinaciones();
	if(!empty($escalones->detalleError)){
		header('X-PHP-Response-Code: 400', true, 400);
		echo($escalones->detalleError);
		return;
	}
	echo("Para una escalera de ".$escalones->escalones." escalones, el resultado es: ".$escalones->solucion);
?>