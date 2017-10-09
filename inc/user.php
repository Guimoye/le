<?php
class User{

	private $online = false;

	// Informacion de usuario
	public $id;
	public $id_level;
	public $name;
	public $surname;
	public $username;
	public $avatar;
	public $state;

    private $db;

	public function __construct($db){
	    $this->db = $db;
		$this->init();
	}

	private function init(){
		if(isset($_SESSION['id_user']) && is_numeric($_SESSION['id_user']) && $_SESSION['id_user'] > 0)
		{
			$user = $this->db->o("SELECT * FROM users WHERE id = ".$_SESSION['id_user']);

			if($user)
			{
				$this->online 	= true;
				$this->id 		= $user->id;
				$this->id_level = $user->id_level;
				$this->name 	= $user->name;
				$this->surname 	= $user->surname;
				$this->username = $user->username;
				$this->state 	= $user->state;
			}
		}
	}

    private $menu = [];
    private $shortcuts = [];
    private $home = 'home?def=1';
    private $actions = [];
    public function loadPerms($loadMenu = false, $url = ''){

        $SQL = "SELECT *
                FROM perms pe
                  LEFT JOIN menu me ON me.id = pe.id_menu
                WHERE pe.id_level = $this->id_level AND me.state = 1
                ORDER BY me.sort";
        $os = $this->db->get($SQL);

        if($os)
        {
            while($o = $os->fetch_assoc())
            {
                if($loadMenu)
                {
                    $o['active'] = ($url==$o['url']);

                    if($o['see']        == 1) $this->menu[$o['url']?:$o['id']] = $o;
                    if($o['shortcut']   == 1) $this->shortcuts[] = $o;
                    if($o['home']       == 1) $this->home = $o['url'];
                }

                if($o['edit'] == 1) $this->actions[$o['code']] = 1;

            }
        }
    }
    
    // Obtener pagina de inicio
    public function getHome(){
        return $this->home;
    }

    // Saber si usuario tiene permiso para acceder a
    public function see($page_file){
        return $this->id_level==1 || isset($this->menu[$page_file]);
    }
    // Saber si usuario tiene permiso para editar
    public function can($action_code){
        return $this->id_level==1 || isset($this->actions[$action_code]);
    }
    // Obtener menu
    public function getMenu(){
        return $this->menu;
    }
    // Obtener menu Shortcuts
    public function getShortcuts(){
        return $this->shortcuts;
    }

	public function getInfo(){
		return [
			'id' 		=> $this->id,
			'id_level'  => $this->id_level,
			'name' 		=> $this->name,
			'surname' 	=> $this->surname,
			'username'	=> $this->username,
			'state' 	=> $this->state
		];
	}

	// Si ha iniciado sesion
	public function isLogged(){
		return $this->online;
	}

	// Iniciar sesion
	public function login($username, $password){
		$username = strtolower(trim($username));
		$password = md5(trim($password));

		$o = $this->db->o("SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1");
		if($o){
			$this->db->update('users', ['date_login'=>'NOW()'], $o->id);
			$_SESSION['id_user'] = $o->id;
			return true;
		} else {
			return false;
		}

		//$_SESSION['user'] = $o;

		//$this->init();

		//return ($o != false);
	}
	
}