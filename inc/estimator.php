<?php
/**
 * Estimador de tarifas mediante coordenadas
 */
class Estimator{

	private $origen         = [0,0]; // Punto de origen
    private $destino        = [0,0]; // Punto de destino
    private $type  = 'tradicional'; // Tipo de servicio (para aplicar comision) (tradicional por defecto)

	public function Estimator(){}

	public function setOrg($lat, $lng){
		$this->origen = [$lat, $lng];
	}

    public function setDst($lat, $lng){
        $this->destino = [$lat, $lng];
    }

    public function setType($type){
        $this->type = $type;
    }

	// Estimar tarifa
	public function estimate($formated = false){
		global $db;

        $id_fare    = $db->getSetting('id_fare');
        $percent	= $db->getSetting('pct_'.$this->type);

		$id_o = 0;
		$id_d = 0;

		$SQL = "SELECT id, points
				FROM zones
				WHERE state = 1
				AND id_fare = $id_fare
				AND points != '[]'";
		$os = $db->get($SQL);
		while($o = $os->fetch_object()){
			$polygon = json_decode($o->points);

			if($this->contains($this->origen,  $polygon)) $id_o = (int) $o->id;
			if($this->contains($this->destino, $polygon)) $id_d = (int) $o->id;

		}

		$fare = [];
		if($id_o > 0 && $id_d > 0){
			$o = $db->o("SELECT * FROM prices WHERE org = $id_o AND dst = $id_d LIMIT 1");

            $cost = (float) $o->cost; // Monto base
            $cost *= (1 + $percent / 100); // Monto sumado el porcentaje

			$fare['id'] 	= (int) $o->id;
			$fare['cost']	= $formated ? number_format($cost, 2, '.', '') : $cost;
		} else {
			$fare['id'] 	= 0;
			$fare['cost']	= $formated ? '0.00' : 0;
		}
		return $fare;
	}

	/**
	 * Saber si un Punto se encuentra dentro de un Poligono
	 */
	public function contains($point, $p){
		if($p[0] != $p[count($p)-1]){
			$p[count($p)] = $p[0];
		}

		$j = 0;
		$oddNodes = false;
		$x = $point[1];
		$y = $point[0];
		$n = count($p);
		for ($i = 0; $i < $n; $i++){
			$j++;
			if ($j == $n){
				$j = 0;
			}
			if ((($p[$i][0] < $y) && ($p[$j][0] >= $y)) || (($p[$j][0] < $y) && ($p[$i][0] >= $y))){
				if ($p[$i][1] + ($y - $p[$i][0]) / ($p[$j][0] - $p[$i][0]) * ($p[$j][1] - $p[$i][1]) < $x){
					$oddNodes = !$oddNodes;
				}
			}
		}
		return $oddNodes;
	}
}