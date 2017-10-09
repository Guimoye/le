<?php
include('inc/util.php');
include('inc/mysql.php');
include('inc/user.php');

class _base {

    protected $uu;
    protected $db;
    protected $user;
    protected $stg;

    public $rsp = array(
        'ok' => false,
        'msg' => '---'
    );

    public function __construct(){
        $this->module = get_class($this);

        $this->uu 	= new Util();
        $this->db 	= new MySQL();
        $this->user	= new User($this->db);
        $this->stg 	= $this->db->getSettings();
        $this->stg->url_cms     = URL_CMS;
        $this->stg->module      = get_class($this);

        if($this->stg->module != "login" && !$this->user->isLogged()){
            header("Location: ".URL_CMS."login?r=".base64_encode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public function setModule($module){
        $this->stg->module = $module;
    }

    private function getUITemplate(){
        require_once('inc/smarty/Smarty.class.php');

        $menu = [
            'main' => $this->uu->ordMenu($this->user->getMenu()),
            'shortcuts' => $this->user->getShortcuts()
        ];

        $ui = new Smarty;
        $ui->setCompileDir('inc/smarty/templates_c');
        $ui->setCacheDir('inc/smarty/cache');
        $ui->setTemplateDir('views');
        $ui->assign('stg', $this->stg);
        $ui->assign('u', $this->user->getInfo()); // Informacion del usuario
        $ui->assign('v', '0.0.8'); // Version (para borrar cache de css/js)
        $ui->assign('url_home', $this->user->getHome());
        $ui->assign('menu', $menu);

        return $ui;
    }

    // Cargar para la UI
    public function ui(){
        $this->user->loadPerms(true, $this->stg->module);

        if($this->module != "login" && $this->user->state != 1){
            $this->exitUI('Usuario bloqueado/eliminado');
        }

        if($this->module != "login" && !$this->user->see($this->stg->module)){
            $this->exitUI('No hay permisos de lectura');
        }

        return $this->getUITemplate();
    }

    // Revisar permisos de edicion
    public function checkEditPerms($action_code){
        $this->user->loadPerms();
        if(!$this->user->can($action_code)){
            $this->rsp['msg'] = 'No hay permisos para esto';
            exit(json_encode($this->rsp));
        }
    }

    // Error UI
    public function exitUI($text = ''){
        $this->user->loadPerms(true, $this->stg->module);
        $ui = $this->getUITemplate();
        $ui->assign('text', $text);
        $ui->display('e404.tpl');
        exit;
    }

    // Error UI
    public function goHome(){
        $this->user->loadPerms(true);
        header("Location: ".URL_CMS.$this->user->getHome());
    }

    // Rev...
    public function rsp(){
        header('Content-Type: application/json');
        echo json_encode($this->rsp);
        exit;
    }

    // Incluir
    public function inc($file_name){
        include('inc/'.$file_name.'.php');
    }

}