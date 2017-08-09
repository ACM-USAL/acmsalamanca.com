	<?php
	class acmTelegram {

	   var $token;

	   function __construct($token)
	   {
	       $this->token = $token;
	  
	   }
	function enviarMensajeTelegram($destinatarios, $mensaje) {


	foreach ($destinatarios as $destino) {
		$url = "https://api.telegram.org/" .$this->token . "/sendMessage?chat_id=" .$destino;
		$url = $url . "&text=" . urlencode($mensaje);
		$ch = curl_init();
		$campos_post = array(
		        CURLOPT_URL => $url,
		        CURLOPT_RETURNTRANSFER => true
		);
		curl_setopt_array($ch, $campos_post);
		$result = curl_exec($ch);
		curl_close($ch);

	}

	}

	function enviarFotoTelegram($destinatarios, $path_foto) {



	foreach ($destinatarios as $destino) {
		$url = "https://api.telegram.org/" .$this->token. "/sendPhoto?chat_id=" .$destino;

	

		$ch = curl_init(); 
		$campos_post = array('chat_id'   => $destino,
		'photo'     => new CURLFile(realpath($path_foto))
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Content-Type:multipart/form-data"
		));
		
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $campos_post); 
		$output = curl_exec($ch);
	}


	}
	   

	} 











	?>
