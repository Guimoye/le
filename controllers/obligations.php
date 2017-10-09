<?php class obligations extends _base{

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

        $os = $this->db->get("SELECT * FROM obligations WHERE id_driver = $driver->id AND state = 1");
        if($os){
            while($o = $os->fetch_assoc()){
                $total_amount += $o['amount'];

                if(strtotime($o['date_end']) > time()) {
                    $o['pay_state'] = 'pending';

                } else {
                    $o['pay_state'] = 'expired';

                }

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Obligaciones');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('total_amount_due', $total_amount);

        $ui->display($this->module.'.tpl');
    }

    public function add(){
        $this->checkEditPerms('drivers');

        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $isEdit = (is_numeric($id) && $id > 0);

        $data = [];
        $data['id_driver']      = _POST_INT('id_driver');
        $data['description']    = _POST('description');
        $data['amount']         = _POST_INT('amount');
        $data['date_end']       = _POST('date_end');
        $data['state']          = 1;

        if($data['id_driver'] <= 0){
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else if(empty($data['description'])){
            $this->rsp['msg'] = 'Indica el tipo de gasto';

        } else if($data['amount'] <= 0){
            $this->rsp['msg'] = 'Indica el monto';

        } else if(!$this->uu->isDate($data['date_end'])){
            $this->rsp['msg'] = 'Indica la fecha';

        } else {

            if($isEdit){
                if($this->db->update('obligations', $data, $id)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            } else {
                if($this->db->insert('obligations', $data)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            }

        }
        $this->rsp();
    }

    public function remove(){
        $this->checkEditPerms('drivers');

        $id = _POST_INT('id');

        if(is_numeric($id) && $id > 0){
            if($this->db->query("UPDATE obligations SET state = 0 WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

}