A continuación se explican los pasos que es necesario llevar a cabo para instalar una version de la web:
Apache:
	/etc/apache2/sites_available/000-default.conf
	<Directory "/var/www/html">
		AllowOverride All
	</Directory>
	//Esto es debido al control de acceso a directorios restringidos y a la re-dirección de errores

Módulos necesarios:
	Php7
	Curl	
	Mysqli

En este punto se debería mostrar el contenido de la web, pero sin ver sin hojas de estilo y sin ejecutarse el javascript, esto es porque getsimplecms asocia el path que le asignemos en la administracion con los templates y la carpeta script, tenemos dos formas de solucionarlo para poder trabajar con la web:
Opción 1):
	Nos metemos en http://xxxxx/admin con los siguientes credenciales:
		User:acmsalamanca
		Password:acmpruebagit
	Entramos en el menú configuración y cambiamos el parámetro URL del sitio por nuestra url.
Opción 2):
	Entrar en el archivo /etc/hosts (o equivalente del SO con el que trabajes) y añadimos:
		127.0.0.1       acmsalamanca.com
		*Mientras esta modificación este no podremos acceder a la web original.

Lo siguiente es configurar el Plug-in de acm_registro, esto se hace mediante un archivo de configuración situado en:
		./plugins/acm_usal_register/inc/configuracion.xml

Hay que modificar el contenido del archivo:
<?xml version='1.0' ?>
<ACM_WEB>
 <IP_base_sql>Ruta de la conexión de la base de datos (ip solo), ejemplo: localhost</IP_base_sql> 
 <base_sql>Nombre de la base de datos</base_sql>
 <usuario_sql>Nombre del usuario de la base de datos con el que se conectara el plugin</usuario_sql>
 <pass_usuario_sql>Contraseña del usuario</pass_usuario_sql>
 <token_bot>Token del bot de telegram que enviara los mensajes-</token_bot>
 <destinatarios>
 <id>---1 Id del grupo al que enviar el registro--</id>
 <id>---2 Id del grupo al que enviar el registro--</id>
 <id>---... Id del grupo al que enviar el registro--</id>
 </destinatarios>
 <email>
 <origen>
 	---- Correo origen que envia los mensajes (gmail,hotmail,servidor local ...)----
 </origen>
 <pass>
 	--- Pass del correo origen ---
 </pass>
 </email>
</ACM_WEB>



La base de datos tiene que contener dos tablas:


CREATE TABLE `usuarios_confirmados` (
  `email` varchar(30) NOT NULL,
  `cargo` varchar(30) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `ocupacion` varchar(40) NOT NULL,
  `biografia` varchar(300) NOT NULL,
  `comentarios` varchar(300) NOT NULL,
  `pathimagen` varchar(300) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `usuarios_pendientes` (
  `email` varchar(30) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `ocupacion` varchar(40) NOT NULL,
  `biografia` varchar(300) NOT NULL,
  `comentarios` varchar(300) NOT NULL,
  `pathimagen` varchar(300) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

Y el usuario que acceda tiene que tener permisos de select, update y delete.


Hay dos carpetas inicialmente vacías cuya subida se ha ignorado, imagenes_miembros e imagenes_pendientes, estas dos carpetas guardan una relación estrecha con la consistencia de la base de datos (contienen las imágenes de los miembros pendientes de ser admitidos y los ya admitidos), hay que asegurarse de que se tienen permisos de escritura sobre estas carpetas.

Una vez hecho esto ya se tiene instalada una versión de la web acmsalamanca.com para trabajar con ella.

La plataforma sobre la que corre es un CMS llamado GetSimpleCMS, es una plataforma muy sencilla, parecida a wordpress en la forma de operar pero bastante ligera, la idea es crear un repositorio publico donde se puedan ir aportando futuras mejoras a la nueva web.
