<?php
class Controller{
    
    public function __construct(){}
    
    public function getLevels(){
        global $db,$rsp;
        $rsp['levels'] = $db->arr("SELECT * FROM levels");
    }

    public function getUnimeds(){
        global $db,$user,$rsp;
        $rsp['unimeds'] = $db->arr("SELECT * FROM unimeds WHERE state = 1");
    }
    
}
