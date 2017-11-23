<?php class dues_loans extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    private function index(){}

    public function item($id_loan){

        $ui = $this->ui();

        $driver = $this->db->o("SELECT dr.*
                                FROM loans lo
                                  INNER JOIN drivers dr ON dr.id = lo.id_driver
                                WHERE lo.id = $id_loan");

        if($driver == FALSE){
            $ui->display('e404.tpl');
            exit;
        }

        $items = [];
        $total_amount_due = 0;

        $today_date = date('Y-m-d');
        $today_time = strtotime($today_date);

        $SQL = "SELECT dl.*
                FROM dues_loans dl
                WHERE dl.id_loan = $id_loan AND dl.state != 0";

        $os = $this->db->get($SQL);
        if($os){
            while($o = $os->fetch_assoc()){

                $o['amount_total'] = $o['amount_due']+$o['amount_previous'];

                $total_amount_due += $o['amount_due'];

                $due_time = strtotime($o['date_due']);

                if($o['state'] == 2 || $o['state'] == 3){
                    $o['pay_state'] = 'paid';

                    $o['all_paid'] = ($o['state'] == 3);

                } else if($due_time < $today_time) {
                    $o['pay_state'] = 'expired';

                } else {
                    $o['pay_state'] = 'pending';

                }

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Cuotas de préstamo');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('total_amount_due', $total_amount_due);

        $ui->display('dues_loans.tpl');
    }

    public function set_due_paid(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $amount             = _POST_INT('amount');
        $date_paid          = _POST('date_paid');

        // Obtener actual cuota a pagar
        $due = $this->db->o("SELECT * FROM dues_loans WHERE id = $id LIMIT 1");

        if(!$due){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if($amount <= 0){
            $this->rsp['msg'] = 'Debe ingresar un monto válido';

        } else if(!$this->uu->isDate($date_paid)){
            $this->rsp['msg'] = 'Debe ingresar la fecha de pago';

        } else {

            $amount_total = ($due->amount_due+$due->amount_previous);

            $data = [];
            $data['amount_paid']        = $amount;
            $data['date_paid']          = $date_paid;
            $data['state']              = 3; // Pago total

            // La siguiente cuota
            if($amount < $amount_total){
                $next_amount = $amount_total - $amount; // A pagar el proximo mes

                $data['state'] = 2; // Pago parcial

                // Obtener la siguiente cuota
                $nextO = $this->db->o("SELECT * FROM dues_loans WHERE id_loan = $due->id_loan AND num_due > $due->num_due AND state != 0 LIMIT 1");
                if($nextO){
                    $this->db->update('dues_loans', ['amount_previous' => $next_amount], $nextO->id);
                    //exit('$nextO: '.$nextO->id);
                }
            }

            if($this->db->update('dues_loans', $data, $id)){
                $this->rsp['ok'] = true;
            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }

        }

        $this->rsp();
    }

}