<?php class levels extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('levels');
    }

    public function index(){

        $menu = $this->uu->ordMenu($this->db->arr("SELECT * FROM menu WHERE state = 1 ORDER BY sort"));

        $levels = [];
        $os = $this->db->get("SELECT * FROM levels");
        while($o = $os->fetch_object()){
            $o->perms = $this->db->arr("SELECT * FROM perms WHERE id_level = $o->id");
            $levels[$o->id] = $o;
        }

        $ui = $this->ui();
        $ui->assign('page_title', 'Perfiles de usuario');
        $ui->assign('menu_all', $menu);
        $ui->assign('levels', $levels);
        $ui->display($this->module.'.tpl');
    }

    public function get_levels(){
        $this->rsp['levels'] = $this->db->arr("SELECT * FROM levels");
        $this->rsp();
    }

}