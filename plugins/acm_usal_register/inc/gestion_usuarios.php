<?php
	//*pendiente: Este archivo contiene las funciones mas propias de administracion, validar formulario, consultas de la base de datos...

$xml=simplexml_load_file($plugin_path."/inc/configuracion.xml") or die("Error: Falla el archivo de configuracion");
$Mysql_Usuario_add=$xml->usuario_sql;
$Mysql_usuario_pass=$xml->pass_usuario_sql;
$Mysql_base=$xml->base_sql;
$destinatarios=[];foreach ($xml->destinatarios->id as $id) {array_push($destinatarios,$id);}
$token_bot=$xml->token_bot;
$IP_base_sql=$xml->IP_base_sql;

$ruta_proyecto=$xml->ruta_proyecto;
$ruta_imagenes_pendientes=$xml->ruta_imagenes_pendientes;
$ruta_imagenes_miembros=$xml->ruta_imagenes_miembros;

function check_submit($name, $surname, $mail, $student, $phone, $bio, $comments,$imagen_campo)
{
    $errors = array();
    
    // Nombre
    if(empty($name)) {
        $errors[] = "<p><font color='red'> El campo nombre no puede estar vacío.</font></p>";
    }
    
    // Apellidos
    if(empty($surname)) {
        $errors[] = "<p><font color='red'> El campo apellidos no puede estar vacío.</font></p>";
    }
    
    // Email
    if(empty($mail)) {
        $errors[] = "<p><font color='red'> El campo email no puede estar vacío.</font></p>";
    } elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "<p><font color='red'> El formato del email es incorrecto.</font></p>";
    }
    
    // Estudiante
    if(empty($student)) {
        $errors[] = "<p><font color='red'> El campo de ocupacion no puede estar vacío.</font></p>";
    }
    
    // Telefono
    if(empty($phone)) {
        $errors[] = "<p><font color='red'> El campo de telefono no puede estar vacío.</font></p>";
    }
    // Biografía
    if(empty($bio)) {
        $errors[] = "<p><font color='red'> El campo de biografia no puede estar vacío.</font></p>";
    }

    //Imagen

        if(getimagesize($imagen_campo["tmp_name"]) == false) {
              $errors[] = "<p><font color='red'> El archivo no es una imagen</font></p>";
        } 
    else{

        if ($imagen_campo["size"] > 500000) {
             $errors[] = "<p><font color='red'> La imagen es demasiado grande</font></p>";
        }
    
            else {
            $formatoImagen=strtoupper (pathinfo(basename($imagen_campo["name"]),PATHINFO_EXTENSION));
            if($formatoImagen != strtoupper ("jpg") && $formatoImagen != strtoupper ("png") && $formatoImagen != strtoupper ("jpeg")
            && $formatoImagen != strtoupper ("gif") && $formatoImagen != strtoupper ("svg") ) {
                 $errors[] = "<p><font color='red'> La imagen no tiene un formato permitido</font></p>";
            }
        }
    }
    return $errors;
}




//Existen dos directorios imagenes_miembros, imagenes_pendientes, muchas operaciones consiste en jugar con el cambio de la imagen, por ejemplo
//aceptar a un miembro implica consultar en la base de datos su info, trasladar su imagen pendiente a imagenes_miembro y luego meter su info



    function addMiembroPendiente($nombre,$apellidos, $mail ,$ocupacion ,$telefono, $bio, $comentarios,$imagenPendiente,$pathFinal){

    global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;

          $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);
                if ($mysqli->connect_errno) {
                    $content = str_replace('(% register_form %)',"<font color='red'> No se ha podido procesar tu solicitud</font>", $content);
                    return $content;
                }
                else{
                	//*pendiente: clase de css
                    $acentos = $mysqli->query("SET NAMES 'utf8'");
                    $nombre =  htmlspecialchars($mysqli->real_escape_string($nombre));
                    $apellidos =   htmlspecialchars($mysqli->real_escape_string( $apellidos));
                    $mail =  htmlspecialchars($mysqli->real_escape_string( $mail));
                    $ocupacion =  htmlspecialchars($mysqli->real_escape_string( $ocupacion));
                    $telefono =  htmlspecialchars($mysqli->real_escape_string( $telefono));
                    $bio = htmlspecialchars($mysqli->real_escape_string( $bio));
                    $comentarios = htmlspecialchars($mysqli->real_escape_string( $comentarios));
                    $sql=sprintf ('Insert into  usuarios_pendientes values("%s","%s","%s","%s","%s","%s","%s","%s");',$mail,$nombre,$apellidos,$telefono,$ocupacion,$bio,$comentarios,$pathFinal);

                    if (!$resultado = $mysqli->query($sql)) {
                        if ($mysqli->errno==1062){
                         $content ="<font color='red'>Este email ya se ha registrado, esta pendiente de confirmacion</font>";
                        }
                        else  $content ="<font color='red'> No se ha podido procesar tu solicitud</font>";
                        return $content;
                    }
                    else{
                  
                        if (!move_uploaded_file($_FILES["imagen_campo"]["tmp_name"], $pathFinal)) {
                             $content = str_replace('(% register_form %)',"<font color='red'>Hubo un problema al subir su archivo</font>", $content);
                             return $content;
                               $sql=sprintf ('delete from usuarios_pendientes where email = %s;',$mail);

                        }
                        else{
			    chmod($pathFinal,0755);
                            return "OK";
                        }
                    }
                }
    }


    function getMiembrosPendientes(){

    global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;


      $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);

   

$acentos = $mysqli->query("SET NAMES 'utf8'");
    $sql = "SELECT * FROM usuarios_pendientes";
    $consulta = $mysqli->query($sql);
    $array_resultados=array();

    if ($consulta->num_rows > 0) {
        while($row = $consulta->fetch_assoc()) {
          array_push($array_resultados,$row);

        }
    } else {
       return -2;
    }
    $mysqli->close();
    if (count($array_resultados)<=0)return -3;
    else return $array_resultados;


    }



function eliminarMiembroPendiente($email){
     global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;
    global $ruta_proyecto;

    $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);
    if ($mysqli->connect_errno) {
        return -1;
    }
$acentos = $mysqli->query("SET NAMES 'utf8'");
     $email = $mysqli->real_escape_string($email);

    $sql1 = 'SELECT * FROM usuarios_pendientes where email="'.$email.'" limit 1;';
    $result1 = $mysqli->query($sql1);


    if ($result1->num_rows > 0) {$row=$result1->fetch_assoc();}
    else{ 
    return -2;
  }


    unlink($ruta_proyecto.$row["pathimagen"]);



    $sql3 = 'DELETE FROM usuarios_pendientes where email="'.$email.'" limit 1;';

    if (!$result3  = $mysqli->query($sql3)) {
           return -3;
    }

    return 1;

    }







function eliminarMiembro($email){
     global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;
    global $ruta_proyecto;

    $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);
    if ($mysqli->connect_errno) {
        return -1;
    }
$acentos = $mysqli->query("SET NAMES 'utf8'");
     $email = $mysqli->real_escape_string($email);

    $sql1 = 'SELECT * FROM usuarios_confirmados where email="'.$email.'" limit 1;';
    $result1 = $mysqli->query($sql1);


    if ($result1->num_rows > 0) {$row=$result1->fetch_assoc();}
    else{ 
    //echo $sql1;
    return -2;
  }


    unlink($ruta_proyecto.$row["pathimagen"]);



    $sql3 = 'DELETE FROM usuarios_confirmados where email="'.$email.'" limit 1;';

    if (!$result3  = $mysqli->query($sql3)) {
          // echo $sql3;
           return -3;
    }

    return 1;

    }


function modificarMiembro($parametros){
if (empty($parametros['nombre']) || empty($parametros['apellido']) || empty ($parametros['cargo']) || empty($parametros['telefono']) || empty ($parametros['email'])) return -2;

     global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;
    global $ruta_proyecto;

    $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);
    if ($mysqli->connect_errno) {
        return -1;
    }

$acentos = $mysqli->query("SET NAMES 'utf8'");
$sql1 = 'update usuarios_confirmados set nombre="'.$mysqli->real_escape_string($parametros['nombre']).'", apellido="'.$mysqli->real_escape_string($parametros['apellido']).'", cargo="'.$mysqli->real_escape_string($parametros['cargo']).'", telefono="'.$mysqli->real_escape_string($parametros['telefono']).'" where email="'.$mysqli->real_escape_string($parametros['email']).'";';

$salida=1;
if ($mysqli->query($sql1) === TRUE) {
    $salida = 1;
} else {
    $salida = -3;
}

$mysqli->close();

    
  
    return $salida;

    }






    function addMiembro($email){
    global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;
    global $ruta_imagenes_pendientes;
    global $ruta_imagenes_miembros;
    global $ruta_proyecto;

    $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);
    if ($mysqli->connect_errno) {
        return -1;
    }
$acentos = $mysqli->query("SET NAMES 'utf8'");
     $email = $mysqli->real_escape_string($email);

    $sql1 = 'SELECT * FROM usuarios_pendientes where email="'.$email.'" limit 1;';
    $result1 = $mysqli->query($sql1);


    if ($result1->num_rows > 0) {$row=$result1->fetch_assoc();}
    else{ 
    //echo $sql1;
    return -2;
  }


   $rutafinal=str_replace($ruta_imagenes_pendientes,$ruta_imagenes_miembros,$row["pathimagen"]);


//estos valores vienen de la primera tabla, por lo que ya estan limpios
    $sql2=sprintf ('Insert into  usuarios_confirmados values("%s","%s","%s","%s","%s","%s","%s","%s","%s");',$row["email"],"Afiliado",$row["nombre"],$row["apellido"],$row["telefono"],$row["ocupacion"],$row["biografia"],$row["comentarios"],$rutafinal);

    if (!$result2  = $mysqli->query($sql2)) {
        if ($mysqli->errno==1062)return -3;
        else return -4;
        
    }



    $sql3 = 'DELETE FROM usuarios_pendientes where email="'.$email.'" limit 1;';

    if (!$result3  = $mysqli->query($sql3)) {
          // echo $sql3;
           return -5;
    }


   rename($ruta_proyecto.substr($row["pathimagen"],2),$ruta_proyecto.substr($rutafinal,2));

    return 1;

    }




  function getMiembrosActivos($cargo){
       global $Mysql_Usuario_add;
    global $Mysql_usuario_pass;
    global $Mysql_base;
    global $IP_base_sql;
      $mysqli = new mysqli($IP_base_sql, $Mysql_Usuario_add, $Mysql_usuario_pass, $Mysql_base);
    if ($mysqli->connect_errno) {
       return -1;
    }
    $acentos = $mysqli->query("SET NAMES 'utf8'");
    if (!is_null($cargo)) $sql = 'SELECT * FROM usuarios_confirmados'.$cargo;
    else $sql = "SELECT * FROM usuarios_confirmados";
    $consulta = $mysqli->query($sql);
    $array_resultados=array();

    if ($consulta->num_rows > 0) {
        while($row = $consulta->fetch_assoc()) {
          array_push($array_resultados,$row);

        }
    } else {
       return -2;
    }
    $mysqli->close();
    if (count($array_resultados)<=0)return -3;
    else return $array_resultados;


    }


    function getJunta(){
	$junta= getMiembrosActivos(' where cargo <> "Afiliado"');
	$final=[];
	$cargoadd=0;
	foreach ($junta as &$indice) {

		switch ($indice["cargo"]) {
		    case "Presidente":
			$final[0]=$indice;
			break;
		    case "Vicepresidente":
			$final[1]=$indice;
			break;
		    case "Secretario":
			$final[2]=$indice;
			break;
		    case "Tesorero":
			$final[3]=$indice;
			break;
 		    case "Sponsor":
			$cargoadd=$cargoadd+1;
			$final[4+$cargoadd]=$indice;
			break;
		    default:
			$final[5+$cargoadd]=$indice;
			$cargoadd=$cargoadd+1;
			break;
		}
	}
ksort($final);
	return $final;
    }


    function getAfiliados(){
	return getMiembrosActivos(' where cargo = "Afiliado"');

    }







