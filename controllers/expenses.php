<?php class expenses extends _base{

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

        if($this->user->isDriver()){
            $this->exitUI('Acceso no permitido');
        }

        $items = [];
        $total_amount = 0;

        $today_date = date('Y-m-d');
        $today_time = strtotime($today_date);

        $os = $this->db->get("SELECT * FROM expenses WHERE id_driver = $driver->id AND state = 1");
        if($os){
            while($o = $os->fetch_assoc()){
                $total_amount += $o['amount'];

                $due_time = strtotime($o['date_pay']);

                if($o['date_paid']){
                    $o['pay_state'] = 'paid';

                } else if($due_time < $today_time) {
                    $o['pay_state'] = 'expired';

                } else {
                    $o['pay_state'] = 'pending';

                }

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Gastos');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('total_amount_due', $total_amount);

        $ui->display('expenses.tpl');
    }

    public function add(){
        $this->checkEditPerms();

        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $isEdit = (is_numeric($id) && $id > 0);

        $data = [];
        $data['id_driver']      = _POST_INT('id_driver');
        $data['description']    = _POST('description');
        $data['amount']         = _POST_INT('amount');
        $data['date_pay']       = _POST('date_pay');
        $data['state']          = 1;

        if($data['id_driver'] <= 0){
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else if(empty($data['description'])){
            $this->rsp['msg'] = 'Indica el tipo de gasto';

        } else if($data['amount'] <= 0){
            $this->rsp['msg'] = 'Indica el monto';

        } else if(!$this->uu->isDate($data['date_pay'])){
            $this->rsp['msg'] = 'Indica la fecha';

        } else {

            if($isEdit){
                if($this->db->update('expenses', $data, $id)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            } else {
                if($this->db->insert('expenses', $data)){
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

        $id = _POST_INT('id');

        $due = $this->db->o('expenses', $id);

        if(!$due){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {

            $data = [];
            $data['date_paid'] = 'NOW()';
            if($this->db->update('expenses', $data, $id)){
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

        if($this->db->query("UPDATE expenses SET date_paid = NULL WHERE id = $id")){
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
            if($this->db->query("UPDATE expenses SET state = 0 WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

}