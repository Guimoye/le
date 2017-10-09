<?php
class users extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('users');
    }

    public function index(){
        $ui = $this->ui();
        $ui->assign('page_title', 'Opciones de sistema');
        $ui->assign('can_users', $this->user->can('users'));
        $ui->display($this->module.'.tpl');
    }

    public function pager(){
        include('inc/arrays.php');
        global $st_user;

        $max 		= isset($_POST['max']) 		&& is_numeric($_POST['max'])	? $_POST['max'] 	: 10;
        $page 		= isset($_POST['page'])		&& is_numeric($_POST['page'])	? $_POST['page']	: 1;
        $date_from	= isset($_POST['date_from'])	? trim($_POST['date_from'])	: '';
        $date_to 	= isset($_POST['date_to']) 		? trim($_POST['date_to'])	: '';
        $word		= isset($_POST['word'])			? trim($_POST['word'])		: '';
        $state		= isset($_POST['state'])		? trim($_POST['state'])		: '';

        $offset = ($page - 1) * $max; // Offet

        $this->rsp['total'] = 0;

        $WHERE = "us.state > 0";

        if(!empty($date_from) && !empty($date_to)){
            $WHERE .= " AND DATE(us.date_added) between '$date_from' and '$date_to'";
        }
        if(!empty($word)){
            $word = '%'.str_replace(' ', '%', $word).'%';
            $WHERE .= " AND (CONCAT(us.name,us.surname,us.phone) LIKE '$word')";
        }
        if(is_numeric($state)){
            $WHERE .= " AND us.state = $state";
        }

        $SQL = "SELECT us.*,
                       DATE_FORMAT(us.date_added, '%d-%m-%Y %h:%i %p') date_added,
                       le.name le_name
                FROM users us
                  LEFT JOIN levels le ON le.id = us.id_level
                WHERE $WHERE
                ORDER BY us.name
                LIMIT $offset,$max";
        $os = $this->db->get($SQL);

        $table = '';
        $items = [];

        $canEdit = $this->user->can('users');

        if($os){
            $this->rsp['total_items'] = $os->num_rows;
            while($o = $os->fetch_object()){
                $link = 'driver.php?id='.$o->id;

                $items[''.$o->id] = $o;

                switch($o->state){
                    case 1:	$estado = 'success'; break;
                    case 2: $estado = 'danger'; break;
                    default:$estado = 'default';
                }

                $table .= '';
                $table .= '<tr>';
                $table .= '	<td> '.$o->id.' </td>';
                $table .= '	<td> <div style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis"> '.$o->name.' '.$o->surname.' </div></td>';
                $table .= '	<td> '.$o->username.' </td>';
                $table .= '	<td> '.$o->phone.' </td>';
                $table .= '	<td> '.$o->le_name.' </td>';
                $table .= '	<td> '.$o->date_added.' </td>';
                $table .= '	<td> <span class="label label-sm label-'.$estado.'"> '.$st_user[$o->state].' </span> </td>';
                $table .= '	<td>';
                if($canEdit){
                    $table .= '<span class="btn btn-outline btn-circle dark btn-sm" onclick="MUser.edit(Pager.items['.$o->id.']);">';
                    $table .= ' <i class="fa fa-pencil"></i>';
                    $table .= '</span>';
                }
                $table .= '	</td>';
                $table .= '</tr>';
            }
        }

        $this->rsp['data'] = $table;
        $this->rsp['items'] = $items;
        $this->rsp();
    }

    public function add(){
        $this->checkEditPerms('users');

        $id = (isset($_POST['id']) ? $_POST['id'] : 0 );

        $isEdit = (is_numeric($id) && $id > 0);

        $data = array();
        $data['name'] 			= isset($_POST['name']) 		? trim($_POST['name']) 			: '';
        $data['surname'] 		= isset($_POST['surname']) 		? trim($_POST['surname']) 		: '';
        $data['username'] 		= isset($_POST['username']) 	? trim($_POST['username']) 		: '';
        $data['password'] 		= isset($_POST['password']) 	? trim($_POST['password']) 		: '';
        $data['phone'] 			= isset($_POST['phone']) 		? trim($_POST['phone']) 		: '';
        $data['id_level'] 		= isset($_POST['id_level']) 	? trim($_POST['id_level']) 		: '';
        $data['state']			= isset($_POST['state'])		? trim($_POST['state'])			: '';

        if(empty($data['name'])){
            $this->rsp['msg'] = '<b>Nombre</b> incorrecto';

        } else if(empty($data['surname'])){
            $this->rsp['msg'] = '<b>Apellido</b> incorrecto';

        } else if(strlen($data['username']) < 3){
            $this->rsp['msg'] = '<b>Usuario</b> incorrecto (min. 3 letras)';

        } else if($this->db->has("SELECT * FROM users WHERE username = '".$data['username']."' AND id != '$id'")){
            $this->rsp['msg'] = '<b>Usuario</b> ya en uso';

        } else if(!$isEdit && empty($data['password'])){
            $this->rsp['msg'] = '<b>Contraseña</b> incorrecta';

        } else if(!is_numeric($data['id_level']) || $data['id_level'] <= 0){
            $this->rsp['msg'] = 'Por favor elige el <b>perfil</b>';

        } else if(!is_numeric($data['state'])){
            $this->rsp['msg'] = '<b>Estado</b> inválido';

        } else {
            if($isEdit){
                if(empty($data['password'])){
                    unset($data['password']);
                } else {
                    $data['password'] = md5($data['password']);
                }
                if($this->db->update('users', $data, $id)){
                    $this->rsp['ok'] = true;
                    $this->rsp['id'] = $id;
                } else {
                    $this->rsp['msg'] = 'Se produjo un error al editar';
                }
            } else {
                $data['password'] = md5($data['password']);
                if($this->db->insert('users', $data)){
                    $this->rsp['ok'] = true;
                    $this->rsp['id'] = $this->db->lastID();
                } else {
                    $this->rsp['msg'] = 'Se produjo un error al registrar';
                }
            }
        }

        $this->rsp();
    }

    public function remove(){
        $this->checkEditPerms('users');

        $id = (isset($_POST['id']) ? $_POST['id'] : 0 );

        if(is_numeric($id) && $id > 0){
            $o = $this->db->o('users', $id);
            if($o){
                if($id != $this->user->id){
                    if ($this->db->query("UPDATE users SET username = CONCAT('obsolete.', username), state = 0 WHERE id = $id")) {
                        $this->rsp['ok'] = $this->db->log(1, $id, $this->user->id);
                    } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
                } else $this->rsp['msg'] = 'No puedes eliminar tu cuenta';
            } else $this->rsp['msg'] = 'No se pudo reconocer';
        } else $this->rsp['msg'] = 'Identificador no válido';

        $this->rsp();
    }

}