<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
	Future Imperfect by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
	Heavily adapted for GetSimple CMS by Timbo, 2016.
*****************************************************/
?>

<!DOCTYPE HTML>

<html>
	<head> <?php   echo '<link rel="stylesheet"  href="http://'.$_SERVER['SERVER_NAME'].'/css/bootstrap.min.css">';?>
		<title><?php get_page_clean_title(); ?> &lt; <?php get_site_name(); ?></title>
		<?php get_header(); ?>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.7" />
	
		<link rel="stylesheet" href="<?php get_theme_url(); ?>/assets/css/main.css" />
		<link rel="stylesheet" href="./css/acm_usal.css" />

	</head>
	<body id="<?php get_page_slug(); ?>" >

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Header -->
				
					<header id="header">
						<h1><a href="<?php get_site_url(); ?>"><?php get_site_name(); ?></a></h1>
						<nav class="links">
							<ul>
								<?php get_navigation(return_page_slug()); ?>
							</ul>
						</nav>
						
					</header>

				<!-- Menu -->
					<section id="menu">

						<!-- Links -->
							<section>
								<ul class="links">
									<?php get_navigation(return_page_slug()); ?>
								</ul>
							</section>

					</section>

				<!-- Main -->
					<div id="main">

						<!-- Post -->
							<article class="post">
								<header>
									<div class="title">
										<h2><?php get_page_title(); ?></h2>
									</div>
									<div class="meta" style="overflow:hidden;">
										<time class="published" datetime="<?php get_page_date('c'); ?>">
<?php

//Esta modificacion se ha hecho para traducir la fecha de los articulos que aparecen en el home

//$fecha=get_page_date('F j, Y',false);
/*
list( $mes, $dia, $anio)=sscanf($fecha,"%s %s %s");

if ($mes=="January") $mes="Enero";
if ($mes=="February") $mes="Febrero";
if ($mes=="March") $mes="Marzo";
if ($mes=="April") $mes="Abril";
if ($mes=="May") $mes="Mayo";
if ($mes=="June") $mes="Junio";
if ($mes=="July") $mes="Julio";
if ($mes=="August") $mes="Agosto";
if ($mes=="September") $mes="Setiembre";
if ($mes=="October") $mes="Octubre";
if ($mes=="November") $mes="Noviembre";
if ($mes=="December") $mes="Diciembre";
$dia=substr($dia, 0, -1);
echo $dia." de ".$mes." de ".$anio;
 */


$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 
echo date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
?> 
</time>
									</div>
								</header>
								<?php get_page_content(); ?>
							</article>

					</div>

				<!-- Sidebar -->
				<?php
				//en caso de que la pagina sea la de actividades  no se muestra la sidebar
				//$reflFunc = new ReflectionFunction('function_name');
				//print $reflFunc->getFileName() . ':' . $reflFunc->getStartLine();
				$titulo=get_page_title(false);

				$paginas_sin_sidebar = array("Actividades", "Nosotros", "Registro");
				if (!in_array($titulo, $paginas_sin_sidebar)) {
					$sidebar_path=realpath(dirname(__FILE__))."/sidebar.php";
					include ($sidebar_path);
				}
				?>
					
				
			</div>
		<!-- Scripts -->
			<script src="<?php get_theme_url(); ?>/assets/js/jquery.min.js"></script>
			<script src="<?php get_theme_url(); ?>/assets/js/skel.min.js"></script>
			<script src="<?php get_theme_url(); ?>/assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="<?php get_theme_url(); ?>/assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="<?php get_theme_url(); ?>/assets/js/main.js"></script>
			<?php get_footer(); ?>



	</body>
</html>
