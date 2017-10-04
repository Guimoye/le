<?php
class t_hola extends t_base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    public function index(){
        exit('index(...');
    }


    public function test($prm){
        exit('test(...: '.$prm);
    }

}