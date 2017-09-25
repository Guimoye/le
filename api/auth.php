<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
usleep(300000);
//sleep(5);
include("../../inc/util.php");
include("../../inc/mysql.php");
include("driver.php");
$uu  = new Util();
$db  = new MySQL();
$stg = $db->getSettings();

// Respuesta del servidor
$rsp = array();
$rsp['ok'] = false;
$rsp['msg'] = '---';

$api = isset($_GET['api']) ? $_GET['api'] : '';

switch($api){

	/**
	 * Inicio de sesion de conductores
	 * Se valida estado bloqueado/nuevo desde app
	 */
	case 'login':
		$email 		= isset($_GET['email']) ? trim($_GET['email']) : '';
		$password 	= isset($_GET['password']) ? ($_GET['password']) : '';
		$firebase 	= isset($_GET['firebase']) ? ($_GET['firebase']) : '';
		if(!empty($email)){
			if(!empty($password)){
				$password = md5($password);
				$o = $db->o("SELECT * FROM drivers WHERE email = '$email' AND state != 0");
				if($o){
					if($o->password === $password){
						$data = [];
						$data['token'] 		= $uu->mkToken($o->id);
						$data['firebase'] 	= $firebase;
						$data['date_login']	= 'NOW()';
						if($db->update('drivers', $data, $o->id)){
							$rsp['ok'] = true;
							$dvr = new Driver($data['token']);
							$rsp = array_merge($rsp, $dvr->getInfo());

						} else $rsp['msg'] = 'Error al generar token de sesión';
					} else $rsp['msg'] = 'password_invalid';
				} else $rsp['msg'] = 'email_unregistered';
			} else $rsp['msg'] = 'Contraseña vacía';
		} else $rsp['msg'] = 'Email vacío';
		break;

	case 'register':
		$json_dvr = isset($_GET['dvr'])	? $_GET['dvr'] 	: '';
		$json_car = isset($_GET['car'])	? $_GET['car']	: '';
		
		$dvr = @json_decode($json_dvr) ?: new stdClass;
		$car = @json_decode($json_car) ?: new stdClass;

		// Limpiar valores
		foreach($dvr as &$val){
			$val = @trim($val);
		}
		foreach($car as &$val){
			$val = @trim($val);
		}

		// Validar CONDUCTOR
		if(empty($dvr->name)){
			$rsp['msg'] = 'Nombre incorrecto';

		} else if(empty($dvr->surname)){
			$rsp['msg'] = 'Apellido incorrecto';

		} else if(empty($dvr->email) || !$uu->isEmail($dvr->email)){
			$rsp['msg'] = 'Email incorrecto';

		} else if($db->has('drivers','email',$dvr->email)){
			$rsp['msg'] = 'email_busy';

		} else if(empty($dvr->password)){
			$rsp['msg'] = 'Contraseña incorrecta';

		} else if(empty($dvr->phone) || !$uu->isPhone($dvr->phone)){
			$rsp['msg'] = 'Teléfono incorrecto';

		}/* else if(empty($dvr->date_birth) || !$uu->isDate($dvr->date_birth)){
			$rsp['msg'] = 'Fecha de nacimiento incorrecta';
		}*/

		// Validar VEHICULO
		else if(empty($car->brand)){
			$rsp['msg'] = 'Marca incorrecta';

		} else if(empty($car->model)){
			$rsp['msg'] = 'Modelo incorrecta';

		} else if(empty($car->year) || $car->year < 1990){
			$rsp['msg'] = 'Año de fabricación incorrecto o no permitido';

		} else if(empty($car->plate) || strlen($car->plate) < 6 || strlen($car->plate) > 7){
			$rsp['msg'] = 'Placa incorrecta';

		} else {
			
			$data_dvr = [];
			$data_dvr['token'] 		= $uu->mkToken($dvr->email);
			$data_dvr['refer'] 		= $db->mkRefer('drivers');
			$data_dvr['name'] 		= $dvr->name;
			$data_dvr['surname'] 	= $dvr->surname;
			$data_dvr['email'] 		= $dvr->email;
			$data_dvr['password'] 	= md5($dvr->password);
			$data_dvr['phone'] 		= $dvr->phone;
			$data_dvr['firebase'] 	= $dvr->firebase;
			$data_dvr['state'] 		= 2; // Nuevo
			//$data_dvr['date_birth']	= $dvr->date_birth;

			// Si ingreso algun codigo referido, convertir en id_refer, para guardar solo id_driver al q pertenece
			if(!empty($dvr->refer)){
				$od = $db->o('drivers', 'refer', $dvr->refer);
				if($od){
					$data_dvr['id_refer'] = $od->id;
				}
			}

			if($db->insert('drivers', $data_dvr)){
				$id_driver = $db->lastID();

				$data_car = [];
				$data_car['id_driver'] = $id_driver;
				$data_car['brand'] = $car->brand;
				$data_car['model'] = $car->model;
				$data_car['year'] = $car->year;
				$data_car['plate'] = $car->plate;

				if($db->insert('cars', $data_car)){
					$id_car = $db->lastID();
					if($db->update('drivers', ['id_car'=>$id_car], $id_driver)){
						$rsp['ok'] = true;
						$dvr = new Driver($data_dvr['token']);
						$rsp = array_merge($rsp, $dvr->getInfo());

					} else $rsp['msg'] = 'Error al asignar vehiculo';
				} else $rsp['msg'] = 'Error al registrar vehículo';
			} else $rsp['msg'] = 'Error al registrar conductor';
		}
		
		break;

	/**
	 * Recuperar contraseña: enviar nueva contraseña a su correo electrónico
	 */
	case 'send_password':
		$email = isset($_GET['email']) ? trim($_GET['email']) : '';
		if($uu->isEmail($email)){
			$dvr = $db->o("SELECT * FROM drivers WHERE email = '$email' LIMIT  1");
			if($dvr){
				$password = $uu->mkPassword();
				$rsp['pwd'] = $password;
				if($db->update('drivers', ['password'=>md5($password)], $dvr->id)){
					include('../../inc/umail.php');
					if(UMail::sendPassword($email, $password)){
						$rsp['ok'] = true;
					} else $rsp['msg'] = 'Error al enviar correo';
				} else $rsp['msg'] = 'Error al generar contraseña';
			} else $rsp['msg'] = 'Email no registrado';
		} else $rsp['msg'] = 'Email inválido';
		break;

}

header('Content-Type: application/json');
echo json_encode($rsp);