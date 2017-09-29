<?php
class t_drivers extends t_base{

    public function __construct(){
        parent::__construct();
        // El contructor
    }

    public function index(){
        $ui = $this->ui();

        $ui->assign('page_title', 'Opciones de sistema');

        $ui->assign('can_settings', $this->user->can('settings'));

        $ui->display('drivers.tpl');
    }

}