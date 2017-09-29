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

class t_base {

    protected $uu;
    protected $db;
    protected $user;
    protected $stg;

    public function __construct(){
        global $uu,$db,$user,$stg;

        $this->uu 	= $uu;
        $this->db 	= $db;
        $this->user	= $user;
        $this->stg 	= $stg;

    }

    // Cargar para la UI
    public function ui(){
        require('inc/smarty/Smarty.class.php');

        $this->user->loadPerms(true, PAGE_FILE);

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

}