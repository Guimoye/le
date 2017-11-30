<?php class fleets extends _base{

    public function index(){
        $items = $this->db->arr("SELECT * FROM fleets WHERE state = 1 ORDER BY name");

        $ui = $this->ui();
        $ui->assign('page_title', 'Flotas');
        $ui->assign('items', $items);
        $ui->display($this->module.'.tpl');
    }

    public function add(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $isEdit = ($id > 0);

        $data = [];
        $data['name'] = _POST('name');
        $data['state'] = 1;

        if(empty($data['name'])){
            $this->rsp['msg'] = 'Ingresa el Nombre';

        } else {

            if($isEdit){
                if($this->db->update('fleets', $data, $id)){
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB :: ';
                }

            } else {
                if($this->db->insert('fleets', $data)){
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
            if($this->db->query("UPDATE fleets SET state = 0 WHERE id = $id")){
                $this->rsp['ok'] = true;
            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';

        $this->rsp();
    }

}