<?php
//exit(md5('demo'));
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

    public $module;

    public function __construct(){
        $this->module = get_class($this);

        $this->uu 	= new Util();
        $this->db 	= new MySQL();
        $this->user	= new User($this->db);
        $this->stg 	= $this->db->getSettings();

        if($this->module != "login" && !$this->user->isLogged()){
            $this->redirect('login?r='.base64_encode($_SERVER['REQUEST_URI']));
        }
    }

    public function setModule($module){
        $this->module = $module;
    }

    private function getUITemplate(){
        require_once('inc/smarty/Smarty.class.php');

        $menu = [
            'main' => $this->uu->ordMenu($this->user->getMenu()),
            'shortcuts' => $this->user->getShortcuts()
        ];

        // Asignar valores a los Ajustes
        $this->stg->url_cms     = URL_CMS;
        $this->stg->module      = get_class($this);

        $ui = new Smarty;
        $ui->setCompileDir('inc/smarty/templates_c');
        $ui->setCacheDir('inc/smarty/cache');
        $ui->setTemplateDir('views');
        $ui->assign('stg', $this->stg);
        $ui->assign('u', $this->user->getInfo()); // Informacion del usuario
        $ui->assign('v', '0.0.9'); // Version (para borrar cache de css/js)
        $ui->assign('url_home', $this->user->getHome());
        $ui->assign('menu', $menu);
        $ui->assign('can_edit', $this->user->can($this->module));

        return $ui;
    }

    // Cargar para la UI
    public function ui(){
        $this->user->loadPerms(true, $this->module);

        if($this->module != "login" && $this->user->state != 1){
            $this->exitUI('Usuario bloqueado/eliminado');
        }

        if($this->module != "login" && !$this->user->see($this->module)){
            $this->exitUI('No hay permisos de lectura');
        }

        return $this->getUITemplate();
    }

    // Saber si puede editar
    public function canEdit($module = ''){
        $this->user->loadPerms();
        return $this->user->can(empty($module) ? $this->module : $module);
    }

    // Revisar permisos de edicion
    public function checkEditPerms($action_code = ''){
        $this->user->loadPerms();
        if(!$this->canEdit($action_code)){
            $this->rsp['msg'] = 'No hay permisos de escritura';
            exit(json_encode($this->rsp));
        }
    }

    // Error UI
    public function exitUI($text = ''){
        $this->user->loadPerms(true, $this->module);
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

    /**
     * Cargar clase
     *
     * @param $class_name : Nombre del archivo/clase ubicado en carpeta inc/, no incluir .php
     * @param string $name : Nombre de variable con que sera guardado, por defecto usara el @class_name
     */
    public function load($class_name, $name = ''){
        $name = $name ?: $class_name;
        if(!isset($this->$name))
        {
            include('inc/'.$class_name.'.php');
            $this->$name = new $class_name();

        } else {
            // Fue instanciado previamente
        }
    }

    /**
     * Funciones Util
     */

    // Redireccionar
    public function redirect($uri){
        header('Location: '.URL_CMS.$uri);
        exit;
    }
}