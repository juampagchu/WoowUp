<?php 
/*
	Test para verificar rapidamente si funciona todo correctamente.
*/
	$archivoJson = file_get_contents("archivo.json");
	$datos = json_decode($archivoJson, true);
	require_once("compras.php");
	$compras = new cCompras();
	$compras->datos = $datos;
	$compras->ProcesarProductos();
	if(!empty($compras->detalleError)){
		header('X-PHP-Response-Code: 400', true, 400);
		echo($compras->detalleError);
		return;
	}
	echo(json_encode($compras->respuesta));
?>