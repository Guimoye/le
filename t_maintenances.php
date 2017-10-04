<?php

/*$last_kms_daily = 166.667;

$next_kms = 20000;
$next_days = round($next_kms / $last_kms_daily); // Hallar esto

echo 'last_kms_daily: '.$last_kms_daily.'<hr>';

echo 'next_kms: '.$next_kms.'<br>';
echo 'next_days: '.$next_days.'<br>';
*/

class t_maintenances extends t_base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    private function index(){
        // No deberian acceder aca
    }

    public function item($id_driver){
        $driver = $this->db->o('drivers', $id_driver);

        $ui = $this->ui();

        if($driver == FALSE){
            $ui->display('e404.tpl');
            exit;
        }

        $items = [];
        $total_amount = 0;

        $os = $this->db->get("SELECT * FROM maintenances WHERE id_driver = $driver->id AND state != 0");
        if($os){
            while($o = $os->fetch_assoc()){
                $o['amount_total'] = $o['amount'] - $o['amount_stored'];
                $total_amount += $o['amount_total'];

                if($o['state'] == 2){ // Pagado
                    $o['pay_state'] = 'paid';

                } else if(strtotime($o['date_item']) > time()) {
                    $o['pay_state'] = 'pending';

                } else {
                    $o['pay_state'] = 'expired';

                }

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Mantenimientos');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('total_amount_due', $total_amount);

        $ui->display('maintenances.tpl');
    }

    public function add(){
        $this->checkEditPerms('drivers');

        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $isEdit = (is_numeric($id) && $id > 0);


        $data = [];
        $data['id_driver']      = _POST_INT('id_driver');
        $data['kms']            = _POST_INT('kms');
        $data['amount']         = _POST_INT('amount');
        $data['amount_stored']  = _POST_INT('amount_stored');
        $data['date_item']      = _POST('date_item');
        $data['state']          = 1;

        $driver = $this->db->o('drivers', $data['id_driver']);

        if(!$driver){
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else if($data['kms'] <= 0){
            $this->rsp['msg'] = 'Indica el kilometraje';

        } else if($data['amount'] <= 0){
            $this->rsp['msg'] = 'Indica el monto';

        } else if(!$this->uu->isDate($data['date_item'])){
            $this->rsp['msg'] = 'Indica la fecha';

        } else {

            // Calculas kms diarios
            if($driver->rental_date){
                $time_start = strtotime($driver->rental_date);
                $time_end = strtotime($data['date_item']);
                $diff_secs = $time_end - $time_start;
                $diff_days = intval($diff_secs / (3600 * 24));

                $kms_daily = $data['kms'] / $diff_days;

                $data['kms_daily'] = $kms_daily;
            }


            if($isEdit){
                if($this->db->update('maintenances', $data, $id)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            } else {
                if($this->db->insert('maintenances', $data)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            }

        }
        $this->rsp();
    }

    public function set_paid(){
        $this->checkEditPerms('drivers');

        $id = _POST_INT('id');

        $o = $this->db->o('maintenances', $id);

        if(!$o){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {

            /**
             * Detectar el siguiente mantenimiento
             *
             * 5000 = 30 dias
             * 10000 = ? dias
             *
             */

            $next_kms = $this->getNextKms($o->kms);

            if($next_kms > 0){
                $next_days = round($next_kms / $o->kms_daily);

                $next_date_item = date('Y-m-d', strtotime($o->date_item." + $next_days days"));

                $nextData = [];
                $nextData['id_driver'] = $o->id_driver;
                $nextData['kms'] = $next_kms;
                $nextData['kms_daily'] = $o->kms_daily;
                $nextData['amount'] = $o->amount;
                $nextData['date_item'] = $next_date_item;
                $nextData['state'] = 1;

                $this->db->insert('maintenances', $nextData);

                $this->rsp['msg'] = "
                    next_kms: $next_kms <br>
                    next_days: $next_days <br>
                    kms_daily: $o->kms_daily <br>
                    next_date_item: $next_date_item
                ";

            } // ELSE: no hay mas mantenimientos



            $data = [];
            $data['date_paid'] = 'NOW()';
            $data['state'] = 2; // Pagado
            if($this->db->update('maintenances', $data, $id)){
                $this->rsp['ok'] = true;
            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }
        }
        $this->rsp();
    }

    /**
     * Obtener siguiente kms de mantenimiento, mediante el ultimo
     * Si retorna "0" es porque ya no hay siguientes
     */
    private function getNextKms($last_kms){
        $os = $this->db->get("SELECT * FROM kms ORDER BY km");
        while($o = $os->fetch_object()){
            if($o->km > $last_kms) return $o->km;
        }
        return 0;
    }

    public function set_unpaid(){
        $this->checkEditPerms('drivers');

        $id = _POST_INT('id');

        if($this->db->query("UPDATE maintenances SET date_paid = NULL WHERE id = $id")){
            $this->rsp['ok'] = true;
        } else {
            $this->rsp['msg'] = 'Error interno :: DB';
        }

        $this->rsp();
    }

    public function remove(){
        $this->checkEditPerms('drivers');

        $id = _POST_INT('id');

        if(is_numeric($id) && $id > 0){
            if($this->db->query("UPDATE maintenances SET state = 0 WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

}