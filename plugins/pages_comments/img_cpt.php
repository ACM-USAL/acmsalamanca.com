<?php

        session_start();
        $url=$_GET['url'];
        $_SESSION["imagencadena"] = randomText(5);
        $imagenCadena = $_SESSION["imagencadena"];
// Verificamos que el usuario inicio sesión
if (! isset($imagenCadena)){
// El usuario no inicio sessión header("HTTP/1.0 405"); // Recurso no permitido
return;
}

        $fuente = $url.'Creature.ttf';
        $alto = 45;
        $espacio= 20;
        $ancho = $espacio * strlen($imagenCadena);
	$img = @imagecreatetruecolor($ancho, $alto);

        imagealphablending($img,false);
        //$fondo = imagecolorallocate($img, 255, 255, 255);
	//fondo blanco transparente (valor alfa: 0 opaco > 127 transparente)
        $fondot = imagecolorallocatealpha($img, 255, 255, 255, 127);
        //Hacemos nuestro rectángulo para tapar el fondo (transparente)
	imagefilledrectangle ( $img , 0 , 0 , $ancho, $alto , $fondot);

	//@imagettftext($img, $fontsize, $ang, $i*$espacio - 2, 26, $negro, $fuente, $imagenCadena[$i]);
        @imagettftext ($img, 45, 6, 22, 55, 0, $fuente,$imagenCadena);


        //colocamos líneas para que sea más complicado sacar el captcha 
        $clinea = imagecolorallocate($img, 0, 0, 0);
        imageline($img,10,40,149,12,$clinea);
        imageline($img,14,16,152,38,$clinea);
        imageline($img,97,6,167,46,$clinea);
        imageline($img,0,10,70,38,$clinea);
        imageline($img,34,11,110,38,$clinea);

        header("Content-type: image/png");
        imagesavealpha($img,true);
        @imagepng($img);
  
// Liberamos recursos       imagedestroy ($img);

function randomText($length) {
	$key = ' ';
        $key1 = ' ';
	//$cadena = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $cadena="AaBbCcDdEeFfGgHhJjKkMmNnPpQqRrSsTtUuVvWwXxYyZx2345689";
	for($i=0;$i<$length;$i++) {

	      $key .= $cadena{rand(0, strlen($cadena)-1)};

	}
	return $key.$key1;
 
}
?>

?>
