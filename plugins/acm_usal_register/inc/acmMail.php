		<?php
		require $plugin_path.'./inc/mail/PHPMailer/PHPMailerAutoload.php';

		class acmMail {


		   var $origen;
		   var $pass;

		   function __construct($origen,$pass)
		   {
		       $this->origen = $origen;
		       $this->pass = $pass;
		  
		   }
		
	function enviarMail($destino,$asunto,$mensaje){

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug  = 0;
	$mail->Host       = 'smtp.gmail.com';
	$mail->Port       = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth   = true;
	$mail->Username   = $this->origen;
	$mail->Password   = $this->pass;
	$mail->SetFrom($this->origen, ' Mensaje Automatico de ACM Usal');
	$mail->AddAddress($destino);
	$mail->Subject = $asunto;
	$mail->Body=$mensaje;
	$mail->AltBody = '';
	if(!$mail->Send()) {
	  return "Error: " . $mail->ErrorInfo;
	} else {
	  return "OK";
	}
	}

		} 

		?>
