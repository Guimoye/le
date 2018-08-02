<?php
ini_set('max_execution_time', 600);

class protemax extends _base
{

    private $api_key = 'SC901ZGoUxOufRiNs8ynSOdF9qgmVi6z';

    public function __construct()
    {
        parent::__construct(false);
    }

    public function index()
    {
        $this->rsp['methods'] = [
            'syncDrivers' => 'Sincronizar conductores',
            'syncKms' => 'Sincronizar kilometraje'
        ];
        $this->rsp();
    }

    public function syncDrivers()
    {

        $str = @file_get_contents('https://api.fm-track.com/objects?version=1&api_key=' . $this->api_key);

        if ($str) {
            $arr = json_decode($str);

            if (is_array($arr)) {

                $this->rsp['ok'] = true;

                $items = [];

                foreach ($arr as $a) {
                    $item = [];

                    $pmx_id = @$a->id;

                    $arr_name = explode(' - ', @$a->name);
                    $plate = count($arr_name) > 0 ? trim($arr_name[0]) : '';
                    $name = count($arr_name) > 1 ? trim($arr_name[1]) : '';

                    $item['id_pmx'] = $pmx_id;
                    $item['plate'] = $plate;

                    if (!empty($plate)) {

                        $o = $this->db->o("SELECT * FROM drivers WHERE vh_plate = '$plate'");

                        if ($o) {
                            $item['id'] = (int)$o->id;

                            if ($this->db->update('drivers', ['pmx_id' => $pmx_id, 'pmx_name' => $name, 'pmx_date_sync' => 'NOW()'], $o->id)) {
                                $item['ok'] = true;

                            } else {
                                $item['msg'] = 'Error al sincronizar';
                            }

                        } else {
                            $item['msg'] = 'No se encuentra el vehiculo';
                        }

                    } else {
                        $item['msg'] = 'No hay placa';
                    }

                    $items[] = $item;
                }

                $this->rsp['items'] = $items;

            } else {
                $this->rsp['msg'] = 'No se obtuvo datos válidos';
            }

        } else {
            $this->rsp['msg'] = 'Problemas para conectar con el servidor de protemax';

        }

        $this->rsp();
    }

    public function syncKms()
    {

        $date = new DateTime();
        $date->sub(new DateInterval('P0DT0H10M'));

        $date_from = $date->format(DATE_ISO8601);
        $date_to = date(DATE_ISO8601);

        $this->rsp['ok'] = true;
        $this->rsp['date_from'] = $date_from;
        $this->rsp['date_to'] = $date_to;

        $items = [];

        $SQL = "SELECT * FROM drivers WHERE pmx_id != ''";

        if ($_limit = _GET_INT('limit')) {
            $SQL .= " LIMIT  $_limit";
        }

        $os = $this->db->get($SQL);

        while ($o = $os->fetch_object()) {
            $item = [];
            $item['id'] = (int)$o->id;
            $item['name'] = $o->name;
            $item['vh_plate'] = $o->vh_plate;
            $item['pmx_id'] = $o->pmx_id;

            // Obtener registros de protemax
            $url = 'https://api.fm-track.com/objects/' . $o->pmx_id
                . '/coordinates?version=1&fromDatetime=' . $date_from
                . '&toDatetime=' . $date_to . '&api_key=' . $this->api_key;
            $str = @file_get_contents($url);

            $item['pmx_url'] = $url;

            if ($str) {
                $obj = @json_decode($str);

                if ($obj) {
                    $obj_items = @$obj->items;

                    if (is_array($obj_items) && count($obj_items) > 0) {
                        // Obtenemos el ultimo registro
                        $last_obj_item = $obj_items[count($obj_items) - 1];

                        $kms = $last_obj_item->calculated_inputs->mileage;

                        $item['pmx_kms'] = $kms;

                        if ($this->db->update('drivers', ['pmx_kms' => $kms, 'pmx_date_sync_kms' => 'NOW()'], $o->id)) {
                            $item['ok'] = true;

                        } else {
                            $item['msg'] = 'No se pudo sincronizar el kilometraje';
                        }

                    } else {
                        $item['msg'] = 'No hay registros';
                    }

                } else {
                    $item['msg'] = 'Formato incorrecto';
                }

            } else {
                $item['msg'] = 'No se pudo conectar con el servidor';
            }

            $items[] = $item;
        }

        $this->rsp['items'] = $items;
        $this->rsp();
    }

}