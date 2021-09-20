<?php 
/*
	Archivo que va a ser llamado en formato ajax.
	Retorna un JSON.
*/
	// Verifico que el .json llegue bien
	if(empty($_FILES) or empty($_FILES["file"]) or empty($_FILES["file"]["tmp_name"])){
		header('X-PHP-Response-Code: 400', true, 400);
		echo("No se recibio el archivo .json");
		return false;
	}
	// Extraigo el contenido del archivo
	$archivoJson = file_get_contents($_FILES["file"]["tmp_name"]);
	// Lo decodifico
	$datos = json_decode($archivoJson, true);
	// Compruebo que contenga algo
	if(empty($datos)){
		header('X-PHP-Response-Code: 400', true, 400);
		echo("El archivo .json no contenia datos");
		return false;
	}
	require_once("compras.php");
	$compras = new cCompras();
	$compras->datos = $datos;
	$compras->ProcesarProductos();
	if(!empty($compras->detalleError)){
		header('X-PHP-Response-Code: 400', true, 400);
		echo($compras->detalleError);
		return;
	}
	header('Content-Type: application/json');
	echo(json_encode($compras->respuesta));
?>