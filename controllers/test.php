<?php
class test extends _base{

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