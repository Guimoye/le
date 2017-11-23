<?php class maintenances extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    private function index(){
        // No deberian acceder aca
    }

    public function item($id_driver){
        $this->uiByType($id_driver, 1);
    }

    public function gas($id_driver){
        $this->uiByType($id_driver, 2);
    }

    private function uiByType($id_driver,$type){
        $driver = $this->db->o('drivers', $id_driver);

        $ui = $this->ui();

        if($driver == FALSE){
            $ui->display('e404.tpl');
            exit;
        }

        $items = [];
        $total_amount = 0;

        $today_date = date('Y-m-d');
        $today_time = strtotime($today_date);

        $os = $this->db->get("SELECT * FROM maintenances WHERE id_driver = $driver->id AND type = $type AND state != 0");
        if($os){
            while($o = $os->fetch_assoc()){
                $o['amount_total'] = $o['amount'] - $o['amount_stored'];
                $total_amount += $o['amount_total'];


                $due_time = strtotime($o['date_item']);

                if($o['state'] == 2){ // Pagado
                    $o['pay_state'] = 'paid';

                } else if($due_time < $today_time) {
                    $o['pay_state'] = 'expired';

                } else {
                    $o['pay_state'] = 'pending';

                }

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Mantenimientos');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('total_amount_due', $total_amount);
        $ui->assign('type', $type);

        $ui->display('maintenances.tpl');
    }

    public function add(){
        $this->checkEditPerms();

        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $isEdit = (is_numeric($id) && $id > 0);

        $data = [];
        $data['id_driver']      = _POST_INT('id_driver');
        $data['type']           = _POST_INT('type');
        $data['kms']            = _POST_INT('kms');
        $data['amount']         = _POST_INT('amount');
        //$data['amount_stored']  = _POST_INT('amount_stored');
        $data['date_item']      = _POST('date_item');
        $data['state']          = 1;

        $driver = $this->db->o('drivers', $data['id_driver']);

        if(!$driver){
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else if($data['type'] <= 0){
            $this->rsp['msg'] = 'Tipo de mantenimiento no especificado';

        } else if($data['kms'] <= 0){
            $this->rsp['msg'] = 'Indica el kilometraje';

        } /*else if($data['amount'] <= 0){
            $this->rsp['msg'] = 'Indica el monto';

        }*/ else if(!$this->uu->isDate($data['date_item'])){
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
        $this->checkEditPerms();

        $id             = _POST_INT('id');
        $amount         = _POST_INT('amount');
        $amount_stored  = _POST_INT('amount_stored');
        $date_paid      = _POST('date_paid');

        $ids_dues_rental = _POST('ids_dues_rental');

        $o = $this->db->o('maintenances', $id);

        if(!$o){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if(!$this->uu->isDate($date_paid)){
            $this->rsp['msg'] = 'Ingresa la fecha';

        } else {

            /**
             * Detectar el siguiente mantenimiento
             *
             * 5000 = 30 dias
             * 10000 = ? dias
             *
             */

            $amount_paid = ($amount - $amount_stored);

            $next_kms = $this->getNextKms($o->kms,$o->type);

            if($next_kms > 0){
                $next_days = round($next_kms / $o->kms_daily);

                $next_date_item = date('Y-m-d', strtotime($o->date_item." + $next_days days"));

                $nextData = [];
                $nextData['id_driver'] = $o->id_driver;
                $nextData['type'] = $o->type;
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
            $data['amount'] = $amount;
            $data['amount_stored'] = $amount_stored;
            $data['amount_paid'] = $amount_paid;
            $data['date_item'] = $date_paid;
            $data['date_paid'] = $date_paid;
            $data['state'] = 2; // Pagado
            if($this->db->update('maintenances', $data, $id)){

                // Marcamos como usado los pozos de mantenimiento usados
                if(!empty($ids_dues_rental)){
                    $arr = explode(',',$ids_dues_rental);
                    foreach($arr as $id_dues_rental){
                        if(is_numeric($id_dues_rental) && $id_dues_rental > 0){
                            $this->db->update('dues_rental', ['amount_pit_used'=>1], $id_dues_rental);
                        }
                    }
                }

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
    private function getNextKms($last_kms,$type){
        $os = $this->db->get("SELECT * FROM kms WHERE type = $type ORDER BY km");
        while($o = $os->fetch_object()){
            if($o->km > $last_kms) return $o->km;
        }
        return 0;
    }

    public function set_unpaid(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        if($this->db->query("UPDATE maintenances SET date_paid = NULL WHERE id = $id")){
            $this->rsp['ok'] = true;
        } else {
            $this->rsp['msg'] = 'Error interno :: DB';
        }

        $this->rsp();
    }

    public function remove(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        if(is_numeric($id) && $id > 0){
            if($this->db->query("UPDATE maintenances SET state = 0 WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

    // obtener pozo disponible para este mantenimiento
    public function get_pit_to_maintenance(){
        $id = _POST_INT('id');
        $mnt = $this->db->o('maintenances', $id);
        if(!$mnt){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {
            $pit = 0;
            $ids_dues_rental = [];

            $os = $this->db->get("SELECT * FROM dues_rental WHERE id_driver = $mnt->id_driver AND amount_pit_used = 0 AND (state = 2 OR state = 3)");
            while($o = $os->fetch_object()){
                $pit += $o->amount_pit;
                $ids_dues_rental[] = $o->id;
            }

            $this->rsp['pit'] = $pit;
            $this->rsp['ids_dues_rental'] = implode(',',$ids_dues_rental);
            $this->rsp['ok'] = true;
        }

        $this->rsp();
    }

}