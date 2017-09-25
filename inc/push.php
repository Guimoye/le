<?php
/**
 * Controlador de mensajes push firebase
 * tipos
 *  message_client: Mensaje del cliente para el conductor
 *  ...hay otros, revisar.
 */

/*
	Enviar notificaciones push (Firebase)
*/
class Push{

	private $tokens 	= []; // Array de identificadores
	private $type 		= ''; // A quien va dirigido la notificacion (clientes, conductores)
	private $title 		= ''; // Titulo de la notificacion
	private $content 	= ''; // Contenido de notificacion

	private $status 	= false; // Estado del envio
	private $status_txt = '';

    public function __construct(){} // Mensaje para un evento

    /**
     * Asignar tokens, uno (string) -o- varios (array)
     * @param string|array $tokens
     */
	public function setTokens($tokens){
	    if(is_array($tokens)){
            $this->tokens = $tokens;
        } else {
            $this->addToken($tokens);
        }
	}

	// Agregar Token a la lista
	public function addToken($token){
		$this->tokens[] = $token;
	}

	// Asignat tipo
	public function setType($type){
		$this->type = $type;
	}

	// Asignar Titulo
	public function setTitle($title){
		$this->title = $title;
	}

	// Asignar Texto
	public function setContent($content){
		$this->content = $content;
	}

	// Retornar estado
	public function getStatus(){
		return $this->status;
	}
	public function getStatusMessage(){
		return $this->status_txt;
	}

	/*
		Enviar notificacion
		@return: respuesta de Firebase
	*/
	public function send(){
		global $stg;

		// Al no haber tokens
		if(count($this->tokens) == 0){
			$this->status_txt = 'No Tokens';
			return;
		}

		$msg = [
			'type' 		=> $this->type,
			'title' 	=> $this->title,
			'content'	=> $this->content
		];

		// Post DATA
	    $post = array(
	    	'registration_ids'	=> $this->tokens,
	    	'data' => array(
	    		'message' => json_encode($msg)
	    	)
	    );

	    // CURL Headers
	    $headers = array(
	    	'Authorization: key='.$stg->key_firebase,
	    	'Content-Type: application/json'
	    );
	 
	    $ch = curl_init(); // Inicializar curl
	    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send'); // URL de peticion
	    curl_setopt($ch, CURLOPT_POST, true); // Metodo POST
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);    
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post)); // Data en formato JSON
	    $result = curl_exec($ch); // Enviar peticon actual

	    if (curl_errno($ch)){
	        $this->status_txt = curl_error($ch); // Error
	    }

	    curl_close($ch); // Cerrar curl
	 	
	 	$this->status = true;
	    $this->status_txt = $result; // Respuesta Firebase

        //exit($result);
	}

}