<?php class menu extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('settings');
    }

    public function index(){
        $menu = $this->uu->ordMenu($this->db->arr("SELECT * FROM menu WHERE state = 1 ORDER BY sort"));

        $ui = $this->ui();
        $ui->assign('page_title', 'Modulos');
        $ui->assign('menu_all', $menu);
        $ui->display('menu.tpl');
    }

    public function add_menu(){
        $this->checkEditPerms();

        $id     = _POST_INT('id');
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['name']   = _POST('name');
        if(isset($_POST['url'])){
            $data['url'] = _POST('url');
        }
        $data['icon']   = _POST('icon');
        $data['state']  = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($this->db->update('menu', $data, $id)){
                    $this->rsp['ok'] = true;
                } else $this->rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($this->db->insert('menu', $data)){
                    $this->rsp['ok'] = true;
                } else $this->rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $this->rsp['msg'] = 'Ingresa un nombre';

        $this->rsp();
    }

    public function re_sort(){
        $this->checkEditPerms();

        $this->saveList($_POST['list']);
        $this->rsp['ok'] = true;

        $this->rsp();
    }

    public function remove_menu(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        if(is_numeric($id) && $id > 0){
            $menu = $this->db->o('menu',$id);
            if($menu){
                if($menu->root != 1){
                    if($this->db->query("DELETE FROM menu WHERE id = $id")){
                        $this->rsp['ok'] = true;
                    } else $this->rsp['msg'] = 'Error interno :: DB';
                } else $this->rsp['msg'] = 'No puedes eliminar este item';
            } else $this->rsp['msg'] = 'No se pudo reconocer';
        } else $this->rsp['msg'] = 'ID inválido';

        $this->rsp();
    }

    public function add_level(){
        $this->checkEditPerms();

        $id             = _POST_INT('id');
        $id_menu_home   = _POST('home');
        $isEdit         = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_menu'] = $id_menu_home;
        $data['name'] = $_POST['name'];
        if(!empty($data['name'])){
            if(is_numeric($data['id_menu']) && $data['id_menu'] > 0){
                if($isEdit){
                    if($this->db->update('levels', $data, $id)){
                        $this->rsp['ok'] = true;
                    } else $this->rsp['msg'] = 'Error interno :: UPDATE';
                } else {
                    if($this->db->insert('levels', $data)){
                        $id = $this->db->lastID();
                        $this->rsp['ok'] = true;
                    } else $this->rsp['msg'] = 'Error interno :: INSERT';
                }

                $perms = [];
                $see = isset($_POST['see']) && is_array($_POST['see']) ? $_POST['see'] : [];
                $edit = isset($_POST['edit']) && is_array($_POST['edit']) ? $_POST['edit'] : [];
                $shortcut = isset($_POST['shortcut']) && is_array($_POST['shortcut']) ? $_POST['shortcut'] : [];
                foreach($see as $id_menu){
                    $perms[$id_menu]['see'] = true;
                }
                foreach($edit as $id_menu){
                    $perms[$id_menu]['edit'] = true;
                }
                foreach($shortcut as $id_menu){
                    $perms[$id_menu]['shortcut'] = true;
                }
                $this->rsp['perms'] = $perms;
                $this->db->query("DELETE FROM perms WHERE id_level = $id");
                foreach($perms as $id_menu => $v){
                    $data = [];
                    $data['id_level'] = $id;
                    $data['id_menu'] = $id_menu;
                    $data['see'] = isset($v['see']) && $v['see'] ? 1 : 0;
                    $data['edit'] = isset($v['edit']) && $v['edit'] ? 1 : 0;
                    $data['shortcut'] = isset($v['shortcut']) && $v['shortcut'] ? 1 : 0;
                    $data['home'] = $id_menu_home == $id_menu ? 1 : 0;
                    $this->db->insert('perms', $data);
                }
                /*//TODO: por acciones
                $this->db->query("DELETE FROM perms WHERE id_level = $id");
                foreach($see as $id_menu){
                    $data = [];
                    $data['id_level'] = $id;
                    $data['id_menu'] = $id_menu;
                    $this->db->insert('perms', $data);
                }*/

                if($isEdit){
                    /*if($this->db->update('menu', $data, $id)){
                        $this->rsp['ok'] = true;
                    } else $this->rsp['msg'] = 'Error interno :: UPDATE';*/
                } else {
                    /*if($this->db->insert('menu', $data)){
                        $this->rsp['ok'] = true;
                    } else $this->rsp['msg'] = 'Error interno :: INSERT';*/
                }
            } else $this->rsp['msg'] = 'Debes elegir la pagina de inicio';
        } else $this->rsp['msg'] = 'Ingresa un nombre';

        $this->rsp();
    }

    private function saveList($list, $parent_id = 0, &$m_order = 0){
        foreach($list as $item) {
            $m_order++;

            $this->db->update('menu', ['id_parent'=>$parent_id,'sort'=>$m_order], $item["id"]);

            if(array_key_exists("children", $item)){
                $this->saveList($item["children"], $item["id"], $m_order);
            }
        }
    }
}