<?php 
/*
	Test para verificar rapidamente si funciona todo correctamente.
*/
	require_once("escalones.php");
	$escalones = new cEscalones();
	$escalones->debug = true;
	echo(PHP_EOL);
	for ($i=1; $i < 10; $i++) { 
		$resultado = $escalones->BuscarConvinaciones($i);
		echo("Para una escalera de ".$i." escalones, el resultado es: ".$resultado."<br>".PHP_EOL);
	}
	echo(PHP_EOL);
?>