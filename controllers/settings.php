<?php class settings extends _base{

    public function index(){
        $ui = $this->ui();
        $ui->assign('page_title', 'Opciones de sistema');
        $ui->display($this->module.'.tpl');
    }

    public function edit_general(){
        $this->checkEditPerms('settings');
        $data = [];
        $data['brand']              = @$_POST['brand'] ?: '';
        $data['coin']               = @$_POST['coin'] ?: '';
        $data['tc']                 = @$_POST['tc'] ?: '';
        $data['igv']                = @$_POST['igv'] ?: '';
        $data['comp_name']          = @$_POST['comp_name'] ?: '';
        $data['comp_ruc']           = @$_POST['comp_ruc'] ?: '';
        $data['menu_collapsed']     = isset($_POST['menu_collapsed']) ? 1 : 0;
        if($this->update($data)){
            $this->rsp['ok'] = true;
        } else $this->rsp['msg'] = 'Erroe interno::DB';
        $this->rsp();
    }

    private function update($data){
        $ok = true;
        foreach($data as $key => $val){
            if(!$this->db->query("UPDATE settings SET value = '$val' WHERE name = '$key'")){
                $ok = false;
            }
        }
        return $ok;
    }

}