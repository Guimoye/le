<?php
class UMail{

	public function __construct(){}

	public function cancel(){}

	// Configurar Mailer
	private static function getMailer(){
		require_once('phpmailer/PHPMailerAutoload.php');
		$mail = new PHPMailer;
		//$mail->SMTPDebug = 2;
		//$mail->Debugoutput = 'html';

		$mail->isSMTP();
		$mail->Host = "smtp.mailgun.org";
		$mail->SMTPAuth = true;
		$mail->Username = "postmaster@mg.liner.pe";
		$mail->Password = "55bfd38340bad77ac6b512e361014904";
		$mail->CharSet = 'UTF-8';
		return $mail;
	}

	// Correo de confirmacion
	public static function sendPassword($email, $pwd){
		global $stg;
		$mail = self::getMailer();
		$mail->setFrom('noreply@liner.pe', $stg->brand);
		$mail->addCC($email);
		//$mail->addBCC('acs.lmv@gmail.com');
		$mail->Subject = 'Nueva contraseña';
		$mail->msgHTML('
			Hola '.$email.',
			<br>has solicitado una nueva contraseña para tu cuenta de '.$stg->brand.'.
			<br><br>Nueva contraseña: <b>'.$pwd.'</b>.<br>
			<br>El equipo de '.$stg->brand.'.
		');
		return ($mail->send());
	}

}