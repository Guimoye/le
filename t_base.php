<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

define('PAGE', 	basename($_SERVER["SCRIPT_FILENAME"], '.php')); // Pagina actual
define('PAGE_FILE', basename($_SERVER["SCRIPT_FILENAME"])); // Pagina actual con extension

include('inc/util.php');
include('inc/mysql.php');
include('inc/user.php');

$uu 	= new Util();
$db 	= new MySQL();
$user	= new User();
$stg 	= $db->getSettings();

$stg->page        = PAGE;
$stg->page_file   = PAGE_FILE;
$stg->url_cms     = URL_CMS;


// Verificar si es usuario logeado
if(PAGE != "login" && !$user->isLogged()){
    header("Location: login.php?r=".base64_encode($_SERVER['REQUEST_URI']));
    exit;
}

class t_base {

    protected $uu;
    protected $db;
    protected $user;
    protected $stg;

    public $rsp = array(
        'ok' => false,
        'msg' => '---'
    );

    public function __construct(){
        global $uu,$db,$user,$stg;

        $this->uu 	= $uu;
        $this->db 	= $db;
        $this->user	= $user;
        $this->stg 	= $stg;
    }

    public function setModule($module){
        $this->stg->page = $module;
        $this->stg->page_file = $module;
    }

    // Cargar para la UI
    public function ui(){
        require('inc/smarty/Smarty.class.php');

        $this->user->loadPerms(true, $this->stg->page_file);

        $smarty = new Smarty;
        $smarty->setCompileDir('inc/smarty/templates_c');
        $smarty->assign('stg', $this->stg);
        $smarty->assign('u', $this->user->getInfo()); // Informacion del usuario
        $smarty->assign('v', '0.0.8'); // Version (para borrar cache de css/js)

        $smarty->assign('url_home', $this->user->getHome());
        $smarty->assign('menu', $this->user->getMenu());

        if(PAGE != "login" && $this->user->state != 1){
            $smarty->display('e403.tpl');
            exit;
        }

        if(PAGE != "login" && !$this->user->see(PAGE_FILE)){
            $smarty->display('e404.tpl');
            exit;
        }

        return $smarty;

    }

    // Revisar permisos de edicion
    public function checkEditPerms($action_code){
        $this->user->loadPerms();
        if(!$this->user->can($action_code)){
            $this->rsp['msg'] = 'No hay permisos para esto';
            exit(json_encode($this->rsp));
        }
    }

    // Rev...
    public function rsp(){
        header('Content-Type: application/json');
        echo json_encode($this->rsp);
        exit;
    }

}