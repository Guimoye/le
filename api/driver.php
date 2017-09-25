<?php
class Driver{

    // Informacion basica
    public $id;
    public $info = [];

    public function __construct($token){
        global $db,$uu;
        if(strlen($token) > 0){
            $o = $db->o("SELECT * FROM drivers WHERE token = '$token' AND state != 0");
            if($o && $o->id > 0){
                $this->id = $o->id;
                $this->info['id']       = (int) $o->id;
                $this->info['id_car']   = (int) $o->id_car;
                $this->info['refer']    = $o->refer;
                $this->info['name']     = $o->name;
                $this->info['surname']  = $o->surname;
                $this->info['email']    = $o->email;
                $this->info['token']    = $o->token;
                $this->info['phone']    = $o->phone;
                $this->info['pic']      = $uu->getPic($o->pic, false, false);
                $this->info['lat']      = $o->lat;
                $this->info['lng']      = $o->lng;
                $this->info['state']    = (int) $o->state;
            }
        }
    }

    public function isLogged(){
        return $this->id > 0;
    }
    
    public function getInfo(){
        return $this->info;
    }
    
    public function check($v){
        global $db,$stg;
        return [
            'updated' 	=> (is_numeric($v) && $v >= $stg->v_app_driver), // Saber si es la ultima version
            'driver' => $this->getInfo(),
            'settings' => [
                'time_request'	=> (int) $stg->time_request,
                'time_wait' 	=> (int) $stg->time_wait,
                'time_track' 	=> (int) $stg->time_track,
                'coin'		 	=> $stg->coin
            ],
            'race' => null
        ];
    }

    public function setFirebase($token){
        global $db;
        return $db->query("UPDATE drivers SET firebase = '$token' WHERE id = $this->id");
    }

    // Asignar Ubicacion
    public function setLocation($lat, $lng, $course, $speed, $id_race){
        global $db,$uu,$rsp;
        $lat = round($lat, 5);
        $lng = round($lng, 5);

        $data = [
            'id_race'	=> $id_race,
            'lat' 		=> $lat,
            'lng' 		=> $lng,
            'adr'	    => $uu->getAddress($lat,$lng),
            'course'	=> $course,
            'speed'		=> $speed
        ];

        $dataUp = $data;
        $dataUp['tracked'] = (time()+60); // Valido por 1 minuto
        $db->update('drivers', $dataUp, $this->id);

        if($this->info['lat'] != $lat && $this->info['lng'] != $lng){
            $data['id_ref']	= $this->id;
            $rsp['msg'] = $db->insert('tracking', $data);
        }
    }

    // Cambiar estado de conductor
    public function setState($state){
        global $db;
        if($state == 'off'){
            return $db->update('drivers', ['tracked'=>time()], $this->id);
        }
        return false;
    }

    /**
     * Aceptar carrera, dependiento si es automatica o personalizada
     * @param int $id
     * @return bool true :: aun le pertenece la carrera
     * @return bool false :: ya no me pertenece la carrera
     */
    public function acceptRace($id){
        global $db;
        $SQL = "SELECT * FROM races
                WHERE id = $id AND id_driver = 0 AND (state = 0 OR state = 1) AND ids_rr LIKE '%\"$this->id\"%'";
        $race = $db->o($SQL);
        if($race){
            if($race->state == 1){
                $db->query("UPDATE races SET id_driver = $this->id WHERE id = $id");
            } else {
                $db->query("UPDATE races SET id_driver = $this->id, state = 2 WHERE id = $id");
            }
            return true;
        } else {
            return false;
        }
    }
}