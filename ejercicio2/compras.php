<?php
class cCompras{
	public $datos = null;
	public $respuesta = array();
	public $debug = false;
	public $detalleError = null;

	public function ProcesarProductos(){
		if(!empty($this->datos) and !empty($this->datos["customer"]) and !empty($this->datos["customer"]["purchases"])){
			foreach ($this->datos["customer"]["purchases"] as $ordenes) {
				foreach ($ordenes["products"] as $productos) {
					if(!isset($this->respuesta[$productos["sku"]])){
						$this->respuesta[$productos["sku"]] = array(
							"id"=>$productos["sku"],
							"nombre"=>$productos["name"],
							"periodo"=>null,
							"ultima"=>null
						);
					}
					if(!isset($this->respuesta[$productos["sku"]]["ultima"])){
						$this->respuesta[$productos["sku"]]["ultima"] = $ordenes["date"];
					}else{
						$ultima = $this->DevolverMayor($ordenes["date"],$this->respuesta[$productos["sku"]]["ultima"]);
						$periodo = $this->DiferenciaEntreFechas($this->respuesta[$productos["sku"]]["ultima"],$ultima);
						$proxima = $this->SumarDias($ultima,$periodo);
						$this->respuesta[$productos["sku"]]["periodo"] = $periodo;
						$this->respuesta[$productos["sku"]]["ultima"] = $ultima;
						$this->respuesta[$productos["sku"]]["proxima"] = $proxima;
						$this->respuesta[$productos["sku"]]["atipico"] = $this->FechaExpirada($proxima);
					}
				}
			}
			$this->respuesta = $this->DejarSoloProductosRecompra($this->respuesta);
			return $this->respuesta;
		}else{
			// El json no cumple la estructura
			$this->DetallarError("El json no cumple la estructura esperada o bien no es un archivo json."); 
		}
	}

	/*
		Verifica que sea un producto de recompra
	*/
	public function DejarSoloProductosRecompra($preRespuesta) {
		$respuestaFinal = array();
		foreach ($preRespuesta as $datos) {
			if($datos["periodo"] !== null){
				$respuestaFinal[] = $datos;
			}
		}
		return $respuestaFinal;
	}

	/*
		Verifica que sea fecha ISO o LATIN y la transforma para poder trabajar con esta.
	*/
	public function PreFormatoFecha($fecha) {
		// Primer caso ISO
		$salida = Date('Y-m-d', strtotime($fecha));
		if($fecha == $salida){
			return $fecha;
		}else{
			// Verifico que venga en formato latin
			if(preg_match("/^[0-3]?[0-9](-|\/)?[0-1]?[0-9](-|\/)?[0-9]{4}$/",$fecha)){
				// Remplazo la barra por iones
				$aux = str_replace('/','-',$fecha);
				// Separo la info de la fecha
				$aux = explode('-',$aux);
				return sprintf('%04d-%02d-%02d',$aux[2],$aux[1],$aux[0]);
			}
		}
		$this->DetallarError("La fecha del producto no es valida, debe ser ISO o LATIN."); 
		return false;
	}

	/*
		Verifico que una fecha siga vigente
	*/
	public function FechaExpirada($fecha){
		$fecha = $this->PreFormatoFecha($fecha);
		// Paso la fecha de string a segundos
		$timeCheck = strtotime($fecha);
		// Paso la fecha de hoy string a segundos, lo hacemos asi para que tome el dia y no horas,minutos y segundos con time
		$timeNow = strtotime(date('Y-m-d'));
		// Verifico que la fecha ahora sea mayor a la que estoy verificando y si es mayor le digo que es una fecha vencida
		if($timeNow >= $timeCheck){
			return true;
		}
		// Si no le digo que es una fecha vigente
		return false;
	}

	/*
		Sumo N dias a una fecha
	*/
	public function SumarDias($fecha,$dias){
		$fecha = $this->PreFormatoFecha($fecha);
		// Paso la fecha de string a segundos
		$time = strtotime($fecha);
		// Retorno la fecha sumandole N dias
		return date('Y-m-d',strtotime('+'.$dias.' day', $time));
	}

	/*
		Devuelve el mayor valor entre dos fechas
	*/
	public function DevolverMayor($fechaUno,$fechaDos){
		$fechaUno = $this->PreFormatoFecha($fechaUno);
		$fechaDos = $this->PreFormatoFecha($fechaDos);
		// Paso la fecha de string a segundos
		$timeUno = strtotime($fechaUno);
		// Paso la fecha de string a segundos
		$timeDos = strtotime($fechaDos);
		// Verifico cual de las dos es mayor
		if($timeUno >= $timeDos){
			return $fechaUno;
		}else{
			return $fechaDos;
		}
	}

	/*
		Devuelve la diferencia en dias entre dos fechas
	*/
	public function DiferenciaEntreFechas($inicio, $fin){
		$inicio = $this->PreFormatoFecha($inicio);
		$fin = $this->PreFormatoFecha($fin);
		// Paso la fecha de string a segundos
		$timeInicio = strtotime($inicio);
		// Paso la fecha de string a segundos
		$timeFin = strtotime($fin);
		// Resto las fechas para que me quede la solo la diferencia
		$timeDiferencia = ($timeFin-$timeInicio);
		// La diferencia la divido en horas minutos y dias
		return ((($timeDiferencia/60)/60)/24);
	}

	/*
		Anota un texto de error
	*/
	public function DetallarError($error=null){
		// Verifico que no venga vacio
		if(!empty($error)){
			// La hago valer
			$this->detalleError = ((!empty($this->detalleError))? $this->detalleError."<br>":"").$error;
			// Verifico que no este en debug
			if($this->debug){
				echo '<pre>ERROR DEBUG:</pre>';
				echo '<pre>'; print_r($this->detalleError); echo '</pre>';
			}
		}
	}
}
?>