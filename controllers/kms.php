<?php class kms extends _base{

    public function index(){
        $ui = $this->ui();
        $ui->assign('page_title', 'Kilómetros de mantenimiento');
        $ui->assign('items_1', $this->db->arr("SELECT * FROM kms WHERE type = 1 ORDER BY km"));
        $ui->assign('items_2', $this->db->arr("SELECT * FROM kms WHERE type = 2 ORDER BY km"));
        $ui->assign('type', 1);
        $ui->display($this->module.'.tpl');
    }

    public function add(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $isEdit = ($id > 0);

        $data = [];
        $data['type']   = _POST_INT('type');
        $data['km']     = _POST_INT('km');

        if($data['type'] <= 0){
            $this->rsp['msg'] = 'Tipo de KM no especificado';

        } else if($data['km'] <= 0){
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
        $this->checkEditPerms();

        $id = _POST_INT('id');

        if(is_numeric($id) && $id > 0){
            if($this->db->query("DELETE FROM kms WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

}