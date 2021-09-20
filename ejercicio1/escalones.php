<?php
class cEscalones{
	public $escalones = null;
	public $solucion = null;
	public $debug = false;
	public $detalleError = null;

	public function BuscarConvinaciones($escalonesAux=null){
		// compruebo si me pasan el número de escalones por parametro
		if(!empty($escalonesAux)){
			$this->escalones = $escalonesAux;
		}
		// Si tengo el debug encendido muestro lo que me viene.
		if($this->debug){
			echo '<pre>------------------------------------------------</pre>';
			echo '<pre>Function: BuscarConvinaciones</pre>';
			echo '<pre>Escalones: '.$this->escalones.'</pre>';
		}
		// compruebo que me pasen escalones.
		if(is_null($this->escalones)){ 
			// Si falla por algo dejo el motivo y corto la ejecución
			$this->DetallarError("No se cargo el número de escalones."); 
			// Retorno el valor por defecto de solucion.
			return $this->solucion; 
		}
		// El num. de escalones tiene que ser positivo.
		if($this->escalones<=0){ 
			// Si falla por algo dejo el motivo y corto la ejecución
			$this->DetallarError("El número de escalones tiene que ser positivo."); 
			// Retorno el valor por defecto de solucion.
			return $this->solucion; 
		}
		// Si escalones es menor a 3 entonces quiere decir que la solucion es la misma al número de escalones subido.
		if ($this->escalones < 3){
			$this->solucion = $this->escalones;
		}else{
			// Guardo en la posicion actual
			$actual = 1;
			// Guardo la posicion al segundo escalon
			$proximo = 2;
			//Cada vez que incrementamos el escalon, el número de formas de subir la escalera es la suma de las dos formas anteriores.
			// Enotnces pasamos la cantidad de escalones empezando desde el número 2 porque ya tenemos los primeros escalones
			for ($i=2; $i < $this->escalones; $i++) { 
				$this->solucion = $actual + $proximo;
			    $actual = $proximo;
			    $proximo = $this->solucion;
			}
		}
		// Si tengo el debug encendido muestro lo que contesto.
		if($this->debug){
			echo '<pre>Respuesta: '.$this->solucion.'</pre>';
			echo '<pre>------------------------------------------------</pre>';
		}
		// Retorno el resultado
		return $this->solucion;
	}

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