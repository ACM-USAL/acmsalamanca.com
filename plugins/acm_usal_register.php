    <?php


//Debug, ahora mismo desactivado, comentar la linea siguiente y descomentar las otras dos
error_reporting(0);
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
//phpinfo();




//--------GETSIMPLECMS 
    $plugin_path = GSPLUGINPATH.'acm_usal_register/';
    $thisfile = basename(__FILE__, '.php');



    register_plugin(
        $thisfile,
        'Registro ACM USAL',                            
        '28102016.1',                                   
        'acm-usal',                                     
        'http://usal.acm.org/',                         
        'Descripción: Plugin de registro ACM USAL.',     
        'settings',
        'generar_menu_admin'
    );


    add_filter('content','registrar_solicitud');
    add_action('nav-tab','generar_menu');
    add_action('error-404','error404');

//-------FIN -GETSIMPLECMS 






//requires (funcionalidad vital)
require $plugin_path.'/inc/gestion_usuarios.php';


//includes (funcionalidad adicional, se suele ejecutar al final de las funcioens principales, si fallan no interrumpen la gestion, telegram, correos..)

//estas dos variables indican si enviar mensajes por correo y telegram, si no esta el modulo de php del que hacen uso pasan a false directamente
//*pendiente: leen el xml, si no hay ningun destinatario de telegram variable a false, lo mismo con mail, asi no dependen de una variable.
$mensajes_telegram=true;
$mensajes_mail=false;
$aux=include($plugin_path."inc/acmTelegram.php");if (!$aux)$mensajes_telegram=false;
$aux=include($plugin_path."inc/acmMail.php");if ($aux)$mensajes_mail=false;
   



//Obtieniendo datos del archivo de configuracion
$xml=simplexml_load_file($plugin_path."/inc/configuracion.xml") or die("Error: Falla el archivo de configuracion");
$destinatarios=[];foreach ($xml->destinatarios->id as $id) {array_push($destinatarios,$id);}
//se podria meter comprobaciones de si no estan x datos en el archivo de configuracion
// aunque si faltara el usuario y la base de datos petarian  las consultas y el telegram y el correo es opcional, aun asi se deberia implementar
$token_bot=$xml->token_bot;
$emailOrigen=$xml->email->origen;
$emailPass=$xml->email->pass;
$ruta_imagenes_pendientes=$xml->ruta_imagenes_pendientes;
$ruta_imagenes_miembros=$xml->ruta_imagenes_miembros;



    function error404(){
      $urlRedirect = "http://".$_SERVER['SERVER_NAME']."/Paginas_de_error/videos/";
      echo '
        <script>
             window.location.href = "'.$urlRedirect.'"
        </script>
      ';
    }

    function registrar_solicitud($content)
    {
        global $plugin_path;
        global $ruta_imagenes_pendientes;
        global $destinatarios;
        global $token_bot;
        global $emailOrigen;
        global $emailPass;
        global $mensajes_informativos;
        global $mensajes_telegram;
        global $mensajes_mail;


         $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        if ($id=="nosotros"){return mostrar_miembros_activos();}
        $success = false;
        $errors = array();

        // Si se ha enviado el formulario
        if (isset($_POST['register-submit'])) {
    	
            $nombre = (isset($_POST['name'])) ? $_POST['name'] : '';
            $apellidos = (isset($_POST['surname'])) ? $_POST['surname'] : '';
            $mail = (isset($_POST['mail'])) ? $_POST['mail'] : '';
            $ocupacion = (isset($_POST['occupation'])) ?  $_POST['occupation'] : '';
            $telefono = (isset($_POST['phone'])) ? $_POST['phone'] : '';
            $bio = (isset($_POST['bio'])) ? $_POST['bio'] : '';
            $comentarios = (isset($_POST['comments'])) ? $_POST['comments'] : '';
          
            // Comprobar campos
            if(!($errors += check_submit($nombre, $apellidos, $mail, $ocupacion, $telefono, $bio, $comentarios,$_FILES["imagen_campo"]))) {
        
                        $formatoImagen=strtoupper (pathinfo(basename($_FILES["imagen_campo"]["name"]),PATHINFO_EXTENSION));
                        $remplazos=array(".","..","/","$");
                        $pathFinal=htmlspecialchars($ruta_imagenes_pendientes.str_replace($remplazos, "_", $mail).".".$formatoImagen);
                        $resadd=addMiembroPendiente($nombre,$apellidos, $mail ,$ocupacion ,$telefono,$bio, $comentarios,$_FILES["imagen_campo"]["name"],$pathFinal);
                        
                        if(strcmp($resadd,"OK")!=0){

                            return $resadd;
                        }

                        $success=true;

                   

                        try {
			 //una variable global que indica si se deben enviar mensajes de informacion o no
                        if($mensajes_telegram){
                       $acmTelegram=new acmTelegram($token_bot);
                      
                        $success=true;
                        $mensaje=
                            "Se ha registrado un nuevo miembro en la web \n".
                            "Nombre:            ".$nombre." ".$apellidos."\n".
                            "Ocupacion:         ".$ocupacion."\n".
                            "Telefono / Id de telegram:          ".$telefono."\n".
                            "Biografia:         ".$bio."\n".
                            "Comentarios:       ".$comentarios
                            ;

                        $acmTelegram->enviarFotoTelegram($destinatarios, $pathFinal);
                        $acmTelegram->enviarMensajeTelegram($destinatarios, $mensaje);

                        }  
                        if($mensajes_mail){                 
                         $acmMail=new acmMail($emailOrigen,$emailPass);
                        //en este caso el origen y el destino son el mismo
                        $acmMail->enviarMail($emailOrigen,"Registro de miembro",$mensaje);
                       
                    }
                    
                        } catch (Exception $e) {
                         //ha habido un error en el envio de email o telegram
                        } 

    
                }
            }
        

        if(!$success) {
            ob_start();
            require($plugin_path.'/plantillas_HTML/formulario.php');
            $html = ob_get_clean();
        } else {
		//*pendiente: clase de css
            $html = '<font color="##50D050">Solicitud enviada con exito</font>';



 $nombre = (isset($_POST['name'])) ? $_POST['name'] : '';
 $apellidos = (isset($_POST['surname'])) ? $_POST['surname'] : '';

	//*pendiente: clase de css
$content= 
        '
        <div class="row">
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"  >
       

         
          <img src="http://'.$_SERVER['SERVER_NAME'].'/data/uploads/usal_reg.png" class="img-rounded" alt="Cinque Terre" width="100%";> 

            </div>
            <p>
            <b>Bienvenido '.$nombre.' '.$apellidos.'</b>
            </p>
             <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9"  >
               Gracias por tu tiempo e interés, tu solicitud ha sido enviada correctamente y se encuentra en la lista de espera para ser atendida por un administrador, nos pondremos en contacto contigo en breves, estate atento a tu email o telegram, pronto recibirás noticias.
            </div>

       
       
     


        </div>
        ';



        }

        $content = str_replace('(% register_form %)', $html, $content);
          
        return $content;
    }




   

    function generar_menu(){
        $plugin = 'acm_usal_register';
        $class = '';
        $txt = 'ACM Nuevos miembros';
        if (@$_GET['id'] == @$plugin) {
            $class='class="tabSelected"';
        }
        echo '<li><a href="load.php?id=acm_usal_register" >';
        echo "ACM Nuevos";
        echo "</a></li>";
        echo '<li><a href="load.php?id=acm_usal_register&gestion=true" >';
        echo "ACM Gestion";
        echo "</a></li>";
    }





   function generar_menu_admin(){
      global $destinatarios;
        global $token_bot;

    //....de momento ... problemas con las plantillas
    
    echo '<div class="row">';

   //esta diferenciacion viene porque solo he encontrado de momento la manera de asociar una sola pagina de administracion por plugin (para no crear otro)
   //si estamos en modo gestion
   if(isset($_GET['gestion'])){
  
      if(isset($_GET['eliminar']))
        {
             $resultado=eliminarMiembro(htmlentities($_GET['eliminar']));

             switch ($resultado){

              case 1:
                     echo "<p><font color='green'> Usuario: ".$_GET['eliminar']." Se ha eliminado con exito</font></p>";
		       $mensaje="Se ha expulsado al usuario: ".$_GET['eliminar']." de la web";
 		         $acmTelegram=new acmTelegram($token_bot);
                      
                
                        $acmTelegram->enviarMensajeTelegram($destinatarios, $mensaje);
              break;

              case -1:
                    echo "<p><font color='red'>No se ha podido acceder a la base de datos</font></p>";
              break;

              case -2:
                   echo "<p><font color='red'> No se ha podido borrar el usuario de la tabla de  usuarios confirmados</font></p>";
              break;

              case -3:
                   echo "<p><font color='red'> No se ha podido borrar el usuario de la tabla de usuarios confirmados</font></p>";
              break;


             }
        }
      else if(isset($_GET['modificar']))
        {
	   if (!isset($_POST['nombre']) || !isset($_POST['apellido']) || !isset($_POST['cargo']) || !isset($_POST['telefono'])){
		echo "<p><font color='red'> Error en la petición, faltan parametros</font></p>";
		}
	   else{
		 $parametros=[];
		 $parametros['nombre']=htmlentities($_POST['nombre']);
		 $parametros['apellido']=htmlentities($_POST['apellido']);
		 $parametros['cargo']=htmlentities($_POST['cargo']);
		 $parametros['telefono']=htmlentities($_POST['telefono']);
		 $parametros['email']=htmlentities($_GET['modificar']);
		
	  switch (modificarMiembro($parametros)){

			      case 1:
				     echo "<p><font color='green'> Usuario: ".$parametros['email']." Se ha actualizado con exito</font></p>";
			      break;

			      case -1:
				    echo "<p><font color='red'>No se ha podido acceder a la base de datos</font></p>";
			      break;

			      case -2:
				   echo "<p><font color='red'> No se ha podido actualizar el usuario, parametros invalidos</font></p>";
			      break;

			      case -3:
				   echo "<p><font color='red'> No se ha podido actualizar el usuario</font></p>";
			      break;


			     }

	   }
        }

      mostrar_gestion_activos(); 
   }

   //si estamos en nuevos usuarios
   else{
      if(isset($_GET['registrar']))
        {
            $_GET['registrar']=htmlentities($_GET['registrar']);
             $resultado=addMiembro($_GET['registrar']);

             switch ($resultado){

              case 1:
                     echo "<p><font color='green'> Usuario: ".$_GET['registrar']." registrado con exito</font></p>";
              break;

              case -1:
                    echo "<p><font color='red'>No se ha podido acceder a la base de datos</font></p>";
              break;

              case -2:
                    echo "<p><font color='red'>el ID:".$_GET['registrar']." es incorrecto</font></p>";
              break;

              case -3:
                    echo "<font color='red'>El email: ".$_GET['registrar']." ya ha sido registrado</font>";
              break;

              case -4:
                    echo "<p><font color='red'>No se ha podido acceder a la base de datos</font></p>";
              break;

              case -5:
                   echo "<p><font color='red'> No se ha podido borrar el usuario de la tabla de pendientes</font></p>";
              break;


             }

        }


       if(isset($_GET['eliminar']))
        {
            $_GET['eliminar']=htmlentities($_GET['eliminar']);
             $resultado=eliminarMiembroPendiente($_GET['eliminar']);

             switch ($resultado){

              case 1:
                     echo "<p><font color='green'> Usuario: ".$_GET['eliminar']." Se ha rechazado con exito</font></p>";
              break;

              case -1:
                    echo "<p><font color='red'>No se ha podido acceder a la base de datos</font></p>";
              break;

              case -2:
                   echo "<p><font color='red'> No se ha podido borrar el usuario de la tabla de pendientes</font></p>";
              break;


             }

           
        }

         mostrar_miembros_pendientes();
      
    }
   echo "</div>";
    }







    function mostrar_miembros_activos(){
    
    $afiliados=getAfiliados();
    echo '<div class="row">';

    if (is_int($afiliados)){
      //aqui podriamos mostrar mensajes de error mas especializados
      //*pendiente: clase de css 
     echo ' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:7em;"><p><font color="red">
     No se ha obtenido informacion de los usuarios
     </font></p>
     </div>';

    return;
    }
	//no hay junta sin miembros activos ( este caso nunca se va a dar y si se da pondria que no se ha obtenido informacion de usuarios).


    $junta=getJunta();


    $rep=0;
     echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" "; >';

//Junta

// la gracia del rep es que vaya alternando miembros entre derecha e izquierda en la presentacion
  echo "<h3>Junta directiva</h3>";
    foreach ($junta as &$indice) {
    if($rep==2){
        $rep=0;
        echo '</div> <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" "; >';
    }
	//*pendiente: clase de css
     echo '
         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        

         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"  ">
          <img src="http://'.$_SERVER['SERVER_NAME'].substr($indice["pathimagen"],1).'" style="height:200px;" class="img-thumbnail" >
        </div>
         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <p style="word-wrap: break-word; white-space: normal;"><b>Nombre:&nbsp;&nbsp;</b>'.$indice["nombre"].'</p>

        <p style="word-wrap: break-word; white-space: normal;"><b>Apellidos:&nbsp;&nbsp;</b>'.$indice["apellido"].'</p>

	 <p style="word-wrap: break-word; white-space: normal;"><b>Cargo:&nbsp;&nbsp;</b>'.$indice["cargo"].'</p>

        <p style="word-wrap: break-word; white-space: normal;"><b>Email:&nbsp;&nbsp;</b>'.$indice["email"].'</p>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "  ><p style="word-wrap: break-word; white-space: normal;"><b>Biografía:&nbsp;&nbsp;</b>'.$indice["biografia"].'</p></div>
     

        </div>';
        $rep=$rep+1;
    }


        echo '</div> ';
    

 echo "<h3>Miembros</h3>";

//Afiliados
$rep=0;
     echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" "; >';
    foreach ($afiliados as &$indice) {
    if($rep==2){
        $rep=0;
        echo '</div> <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" "; >';
    }
     echo '
         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        

         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"  ">
          <img src="http://'.$_SERVER['SERVER_NAME'].substr($indice["pathimagen"],1).'" style="height:200px;" class="img-thumbnail" >
        </div>
         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <p style="word-wrap: break-word; white-space: normal;"><b>Nombre:&nbsp;&nbsp;</b>'.$indice["nombre"].'</p>

        <p style="word-wrap: break-word; white-space: normal;"><b>Apellidos:&nbsp;&nbsp;</b>'.$indice["apellido"].'</p>

        <p style="word-wrap: break-word; white-space: normal;"><b>Email:&nbsp;&nbsp;</b>'.$indice["email"].'</p>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "  ><p style="word-wrap: break-word; white-space: normal;"><b>Biografía:&nbsp;&nbsp;</b>'.$indice["biografia"].'</p></div>
     

        </div>';
        $rep=$rep+1;
    }

if($rep==2){
        echo '</div> ';
    }
    echo "</div>";
    }








  function mostrar_miembros_pendientes(){
   


    $resultado=getMiembrosPendientes();


      if (is_int($resultado)){
      //aqui podriamos mostrar mensajes de error mas especializados
     echo ' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:7em;"><p><font color="red">
     No se ha obtenido informacion de usuarios pendientes de registro
     </font></p>
     </div>';

    return;
    }

	//*pendiente: clase de css
     foreach ($resultado as &$indice) {
      echo ' <br>
         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:5em;">
         <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><img src="http://'.$_SERVER['SERVER_NAME'].substr($indice["pathimagen"],1).'" class="img-rounded"height="220" width="100%"/></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Email:&nbsp;&nbsp;</b>'.$indice["email"].'</p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Nombre:&nbsp;&nbsp;</b>'.$indice["nombre"].'</p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Apellidos:&nbsp;&nbsp;</b>'.$indice["apellido"].'</p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Telefono:&nbsp;&nbsp;</b>'.$indice["telefono"].'</p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Ocupacion:&nbsp;&nbsp;</b>'.$indice["ocupacion"].'</p></div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "  style="margin-top:0.5em;"><p style="word-wrap: break-word; white-space: normal;"><b>Biografia:&nbsp;&nbsp;</b>'.$indice["biografia"].'</p></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:0.5em;""><p style="word-wrap: break-word; white-space: normal;"><b>Comentarios:&nbsp;&nbsp;</b>'.$indice["comentarios"].'</p></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:0.5em;">
             <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"></div>
             <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                     <a  href="http://'.$_SERVER['SERVER_NAME'].'/admin/load.php?id=acm_usal_register&registrar='.$indice["email"].'"><button type="button" class="btn btn-success">Aceptar</button></a>
             </div>
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                      <a  href="http://'.$_SERVER['SERVER_NAME'].'/admin/load.php?id=acm_usal_register&eliminar='.$indice["email"].'"><button type="button" class="btn btn-danger">Rechazar</button></a>
             </div>
         </div>
        </br>
        </p>
        </div>';
    }



   
    }



    function mostrar_gestion_activos(){


    $resultado=getMiembrosActivos();


      if (is_int($resultado)){
      //aqui podriamos mostrar mensajes de error mas especializados
     echo ' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:7em;"><p><font color="red">
     No se ha obtenido informacion de usuarios pendientes de registro
     </font></p>
     </div>';

    return;
    }

	//*pendiente: clase de css
     foreach ($resultado as &$indice) {
$cargos=['Afiliado','Presidente','Vicepresidente','Secretario','Tesorero','Sponsor'];
if (($key = array_search($indice["cargo"], $cargos)) !== false) {
    unset($cargos[$key]);
}
      $salida= ' <br>
         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:10em;">
             <form action="http://'.$_SERVER['SERVER_NAME'].'/admin/load.php?id=acm_usal_register&gestion=true&modificar='.$indice["email"].'"      method="post">
         <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><img src="http://'.$_SERVER['SERVER_NAME'].substr($indice["pathimagen"],1).'" class="img-rounded"height="220" width="100%"/></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Email:&nbsp;&nbsp;</b>'.$indice["email"].'</p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Nombre:&nbsp;&nbsp;</b><input type="text" name="nombre" value = "'.$indice["nombre"].'"></p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Apellidos:&nbsp;&nbsp;</b><input type="text" name="apellido" value = "'.$indice["apellido"].'"></p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Telefono:&nbsp;&nbsp;</b><input type="text" name="telefono"  value = "'.$indice["telefono"].'"></p></div>
	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Cargo:&nbsp;&nbsp;</b>'.'<select class="selectpicker" name="cargo"  >';
$salida .= '<option autocomplete="off">'.$indice["cargo"].'</option>';
	foreach ($cargos as $cargo){
		$salida .= '<option >'.$cargo.'</option>';
	}
$salida .='</select></p></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><b><p style="word-wrap: break-word; white-space: normal;">Ocupacion:&nbsp;&nbsp;</b>'.$indice["ocupacion"].'</p></div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "  style="margin-top:0.5em;"><p style="word-wrap: break-word; white-space: normal;"><b>Biografia:&nbsp;&nbsp;</b>'.$indice["biografia"].'</p></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:0.5em;""><p style="word-wrap: break-word; white-space: normal;"><b>Comentarios:&nbsp;&nbsp;</b>'.$indice["comentarios"].'</p></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
             <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"></div>
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                      <a  href="http://'.$_SERVER['SERVER_NAME'].'/admin/load.php?id=acm_usal_register&gestion=true&eliminar='.$indice["email"].'"><button type="button" class="btn btn-danger">Eliminar Miembro</button></a>
             </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></div>
 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                      <input  type="submit" class="btn btn-primary" value="Modificar Miembro"></input>
             </div>
         </div>
        </br>
        </p>
        </form>
        </div>';
echo $salida;
    }



   
    }


    

