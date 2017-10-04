<?php
class t_home extends t_base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    public function index(){
        $ui = $this->ui();
        $ui->assign('page_title', 'Conductores');
        $ui->display('home.tpl');
    }

}