<?php class kms extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('kms');
    }

    public function index(){
        $items = $this->db->arr("SELECT * FROM kms ORDER BY km");

        $ui = $this->ui();
        $ui->assign('page_title', 'Kilómetros de mantenimiento');
        $ui->assign('items', $items);
        $ui->display($this->module.'.tpl');
    }

    public function add(){
        $this->checkEditPerms('kms');

        $id = _POST_INT('id');

        $isEdit = ($id > 0);

        $data = [];
        $data['km'] = _POST_INT('km');

        if($data['km'] <= 0){
            $this->rsp['msg'] = 'Ingresa el Km';

        } else {

            if($isEdit){
                if($this->db->update('kms', $data, $id)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            } else {
                if($this->db->insert('kms', $data)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            }

        }
        $this->rsp();
    }

    public function remove(){
        $this->checkEditPerms('kms');

        $id = _POST_INT('id');

        if(is_numeric($id) && $id > 0){
            if($this->db->query("DELETE FROM kms WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

}