<?php class loans extends _base{

    public function __construct(){
        parent::__construct();
        $this->setModule('drivers');
    }

    private function index(){}

    public function item($id_driver){
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

        $os = $this->db->get("SELECT * FROM loans WHERE id_driver = $driver->id AND state = 1");
        if($os){
            while($o = $os->fetch_assoc()){
                $total_amount += $o['amount'];

                $num_dues_all = 0; // Total de cuotas
                $num_dues_paid = 0; // Total de cuotas pagadas
                $num_dues_expired = 0; // Total de cuotas vencidas
                $num_dues_pending = 0; // Total de cuotas pendientes

                $amount_total = 0; // Saldo
                $amount_balance = 0; // Saldo

                // Obtener cuotas para ver si han vencido
                $ols = $this->db->get("SELECT * FROM dues_loans WHERE id_loan = ".$o['id']." AND state != 0");
                while($ol = $ols->fetch_assoc()){

                    $due_time = strtotime($ol['date_due']);

                    $num_dues_all += 1;

                    if($ol['state'] == 1){
                        $amount_balance += ($ol['amount_due']+$ol['amount_previous']);
                    }

                    $amount_total += ($ol['amount_due']+$ol['amount_previous']);

                    if($ol['state'] == 2 || $ol['state'] == 3){
                        $num_dues_paid += 1;

                    } else if($due_time < $today_time) {
                        $num_dues_expired += 1;

                    } else {
                        $num_dues_pending += 1;
                    }
                }

                if($num_dues_expired > 0){
                    $o['pay_state']         = 'expired';
                    $o['num_dues_state']    = $num_dues_expired;

                } else if($num_dues_pending > 0){
                    $o['pay_state']         = 'pending';
                    $o['num_dues_state']    = $num_dues_pending;

                } else {
                    $o['pay_state']         = 'payer';
                    $o['num_dues_state']    = $num_dues_paid;

                }

                $o['amount_total']    = $amount_total;
                $o['amount_balance']    = $amount_balance;

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Préstamos');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('dates', $this->db->arr("SELECT * FROM dues_rental WHERE id_driver = $id_driver AND DATE(date_due) >= CURDATE() AND state != 0"));
        $ui->assign('total_amount_due', $total_amount);

        $ui->display('loans.tpl');
    }

    public function add(){
        $this->checkEditPerms();

        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $isEdit = (is_numeric($id) && $id > 0);

        $data = [];
        $data['id_driver']      = _POST_INT('id_driver');
        $data['description']    = _POST('description');
        $data['num_dues']       = _POST_INT('num_dues');
        $data['tea']            = _POST_INT('tea');
        $data['amount']         = _POST_INT('amount');
        $data['date_pay']       = _POST('date_pay'); // fecha de inicio de pago de primera cuota
        $data['state']          = 1;

        if($data['id_driver'] <= 0){
            $this->rsp['msg'] = 'No se reconoce el conductor';

        }/* else if(empty($data['description'])){
            $this->rsp['msg'] = 'Indica el tipo de gasto';

        }*/ else if($data['num_dues'] <= 0){
            $this->rsp['msg'] = 'Indica el número de cuotas';

        } else if($data['tea'] < 0){
            $this->rsp['msg'] = 'Indica la tasa efectiva anual';

        } else if($data['amount'] <= 0){
            $this->rsp['msg'] = 'Indica el monto';

        } else if(!$this->uu->isDate($data['date_pay'])){
            $this->rsp['msg'] = 'Indica la fecha';

        } else {

            // Monto de la cuota
            $amount = $data['amount'] / $data['num_dues'];

            $TES = ((1+$data['tea']) ^ (1/52)) - 1;

            $amount_total = $amount + (($amount*$TES)/100);

            if($this->db->insert('loans', $data)){
                $this->rsp['ok'] = true;

                $id = $this->db->lastID();

                // Generar cuotas
                $lastTime = strtotime($data['date_pay']);
                for($i = 1; $i <= $data['num_dues']; $i++){

                    $date_due = date("Y-m-d", $lastTime);
                    $day_num = date("w", $lastTime);
                    $day_nam = date("l", $lastTime);

                    $data_dues_loans = [
                        'id_loan' => $id,
                        'num_due' => $i,
                        'amount_due' => $amount_total,
                        'date_due' => $date_due,
                        'state' => 1
                    ];

                    $this->rsp['dayy'] = date("w", $lastTime);

                    $this->rsp['dues'][] = $data_dues_loans;

                    if($this->db->insert('dues_loans', $data_dues_loans)){

                    } else {
                        $this->rsp['ok'] = false;
                    }

                    $lastTime = strtotime('next '.$day_nam, $lastTime);
                }

            } else {
                $this->rsp['msg'] = 'Error interno :: DB :: ';
            }

        }
        $this->rsp();
    }

    public function set_paid(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $due = $this->db->o('loans', $id);

        if(!$due){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {

            $data = [];
            $data['date_paid'] = 'NOW()';
            if($this->db->update('loans', $data, $id)){
                $this->rsp['ok'] = true;
            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }
        }
        $this->rsp();
    }

    public function set_unpaid(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        if($this->db->query("UPDATE loans SET date_paid = NULL WHERE id = $id")){
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
            if($this->db->query("UPDATE loans SET state = 0 WHERE id = $id")){
                $this->db->query("UPDATE dues_loans SET state = 0 WHERE id_loan = $id"); // Eliminar cuotas

                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

    // Obtener cuotas
    public function get_dues(){
        $id_loan = _POST_INT('id_loan');

        if($id_loan <= 0){
            $this->rsp['msg'] = 'ID Inválido';

        } else {
            $this->rsp['ok'] = true;
            $this->rsp['dues'] = $this->db->arr("SELECT * FROM dues_loans WHERE id_loan = $id_loan");
        }

        $this->rsp();
    }

}