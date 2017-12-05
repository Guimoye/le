<?php class pics extends _base{

    public function __construct(){
        parent::__construct();
    }

    public function get_all(){
        $type   = _POST_INT('type');
        $id_ref = _POST_INT('id_ref');

        if($id_ref <= 0){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {
            $this->rsp['items'] = $this->db->arr("SELECT * FROM pics WHERE type = $type AND id_ref = $id_ref AND state = 1");
            $this->rsp['ok'] = true;

        }
        $this->rsp();
    }

    public function remove(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        if($id <= 0){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {
            if($this->db->update('pics', ['state'=>0], $id)){
                $this->rsp['ok'] = true;
            }
        }
        $this->rsp();
    }

    public function upload(){
        $this->checkEditPerms();

        $type   = _POST_INT('type');
        $id_ref = _POST_INT('id_ref');

        $photo = (isset($_FILES['photo']) ? $_FILES['photo'] : '' );

        $this->rsp['photo'] = $photo;

        if($type <= 0){
            $this->rsp['msg'] = 'Tipo incorrecto';

        } else if($id_ref <= 0){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if(empty($photo['name'])){
            $this->rsp['msg'] = 'No se ha seleccionado la imágen';

        } else {
            require('inc/plugins/ImageResize.php');
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $pic_voucher = md5(uniqid($id_ref)).'.'.$ext;


            if (move_uploaded_file($photo['tmp_name'], 'uploads/'.$pic_voucher)) {
                $data = [];
                $data['type']   = $type;
                $data['id_ref'] = $id_ref;
                $data['pic']    = $pic_voucher;
                $data['state']  = 1;
                if($this->db->insert('pics', $data)){
                    $this->rsp['pic'] = $pic_voucher;
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }
            } else {
                $this->rsp['msg'] = 'Error al guardar la imágen';
            }
        }
        $this->rsp();
    }

}