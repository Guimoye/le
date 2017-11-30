<?php class login extends _base{

    private $return = '';

    public function __construct(){
        parent::__construct(false);
    }

    public function index(){
        if($this->user->isLogged()){
            $this->redirect($this->return);
        }

        $ui = $this->ui();
        $ui->assign('page_title', 'Iniciar sesión');
        $ui->display($this->module.'.tpl');
    }

    public function login(){
        $username = trim(_POST('username'));
        $password = trim(_POST('password'));

        if(empty($username)){
            $this->rsp['msg'] = 'Ingresa un usuario';

        } else if(empty($password)){
            $this->rsp['msg'] = 'Ingresa una contraseña';

        } else {
            $username = addslashes($username);
            $password = md5(addslashes($password));

            if($this->uu->isEmail($username)) {

                // Es email, se logea un conductor
                $o = $this->db->o("SELECT * FROM drivers WHERE email = '$username' AND password = '$password' LIMIT 1");
                if($o){
                    $this->db->update('users', ['date_login'=>'NOW()'], $o->id);
                    $_SESSION['id_user'] = $o->id;
                    $_SESSION['is_driver'] = true;

                    $this->rsp['ok'] = true;
                    $this->rsp['url'] = $this->return;

                } else {
                    $this->rsp['msg'] = 'Datos de <b>conductor</b> incorrectos';
                }

            } else {

                $o = $this->db->o("SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1");
                if($o){
                    $this->db->update('users', ['date_login'=>'NOW()'], $o->id);
                    $_SESSION['id_user'] = $o->id;
                    $_SESSION['is_driver'] = false;

                    $this->rsp['ok'] = true;
                    $this->rsp['url'] = $this->return;

                } else {
                    $this->rsp['msg'] = 'Datos incorrectos';
                }

            }
        }

        $this->rsp();
    }

    public function logout(){
        $_SESSION['id_user'] = 0;
        unset($_SESSION['id_user']);
        session_destroy();

        if(isset($_SERVER["HTTP_REFERER"])){
            header('Location: '.URL_CMS.'login?r='.base64_encode($_SERVER['HTTP_REFERER']));
        } else {
            header('Location: '.URL_CMS.'login');
        }
    }

}