<?php
/*
//////////////////////////////////////////////////////////
Developer for:  GETSIMPLE CMS
Plugin Name: Pages & comments
Description: easily put comments system to your pages'
Version: 3.2beta
Author: cumbe (Miguel Embuena Lance)
//////////////////////////////////////////////////////////
*/
// Relative
$relative = '../';
$path = $relative. 'data/other/';

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");


# register plugin
register_plugin(
	$thisfile,
	'Pages & Comments',
	'3.2',
	'Cumbe',
	'http://www.cumbe.es/',
	'Description: easily put comments system to your pages',
	'pages', //page type
	'pwcomments'
);

//set internationalization
if (basename($_SERVER['PHP_SELF']) != 'index.php') { // backend only
  i18n_merge('pages_comments', $LANG);
  i18n_merge('pages_comments', 'en_US');
}

//add pestaña a sidebar de plugins
add_action('pages-sidebar','createSideMenu',array('pages_comments',i18n_r('pages_comments/NEWS')));

//changedata-save Called just before a page is saved
add_action('changedata-save','prch_name_pages');

//add button Back to content of page if page is in pages&comments
//add_action('content-top','sv_pages_back');

//add comments to content of page if page is in pages&comments and choosed comments=yes
add_action('content-bottom','sv_pages_first');  

//add css to head
add_action('theme-header','inc_pccss');


//filter content to add comments if these are called from page directly
// ** TO DO **
//add_filter('content','comments');

function inc_pccss(){
  ///////////////////////////////////////////////////////////
  //  include file css of page & comments
  ///////////////////////////////////////////////////////////
   global $SITEURL;
   echo '<link href="'.$SITEURL.'plugins/pages_comments/pc.css" rel="stylesheet">';
   echo '<link href="'.$SITEURL.'plugins/pages_comments/templateform/pcform.css" rel="stylesheet" type="text/css" />';
}

function prch_name_pages(){
  ///////////////////////////////////////////////////////////
  //   This function works if is changed name of page.
  //     - $url: file name after changer;
  //     - $_POST: use for to see file name before change,
  //               checking first that exists it in array.
  ///////////////////////////////////////////////////////////

    global $url, $_POST;
    $ext_url=$url;
    if (array_key_exists('existing-url', $_POST)){
       $ext_url=$_POST['existing-url'];
    }

    if ($ext_url != $url){
        $log_name = $url.'.log';
        $log_name_old = $ext_url.'.log';
        $log_path = GSDATAOTHERPATH.'pages_comments/';
        $log_file_old = $log_path . $log_name_old;
        $log_file = $log_path . $log_name;

        //change name to file log that saves comments       
        if (file_exists($log_file_old)) {
            rename ($log_file_old, $log_file);
        }

	//update pc_manager.xml, that contain pages with comments.
	//filterus by url old and after change to new url
  	$pcm_file= GSDATAOTHERPATH.'pages_comments/pc_manager.xml';

        //if exists file   
        if (is_file($pcm_file)){
		//check  entry
		$domDocument = new DomDocument();
		$domDocument->load($pcm_file);

		//DOMXPath to filter
		$xpath = new DOMXPath($domDocument);			

		$verN = $xpath->query('page[url="'.$ext_url.'.xml"]');			
	  	$num = $verN->length;
		if ($num > 0){
			$dNdList = $verN->item(0)->getElementsByTagName( "url" );
        	        $dNdList->item(0)->nodeValue= $url.'.xml';
		} 
 		$domDocument->save($pcm_file);
        }


        //change(update) url in pc_lastcom.xml
        if (is_file($log_path.'pc_lastcom.xml')){
		//check  entry
		$domDocument = new DomDocument();
		$domDocument->load($log_path.'pc_lastcom.xml');

		//DOMXPath to filter
		$xpath = new DOMXPath($domDocument);               
		$verN = $xpath->query('entry/filelog[../filelog="'.$log_name_old.'"]');			
	  	$num = $verN->length;
		if ($num > 0){
                        foreach ($verN as $node) {
         	             $node->nodeValue= $log_name;
                        }
		} 
 		$domDocument->save($log_path.'pc_lastcom.xml');

        }         
   }

}

///////////////////////////////////////////////
//////   BACKEND - ADMIN - PAGES     //////////
///////////////////////////////////////////////

function pwcomments(){

    global $SITEURL;
    global $i18n, $LANG;
    global $PRETTYURLS;

    //Pages with comments
    $log_name = 'pc_manager.xml';
    $log_path = GSDATAOTHERPATH.'pages_comments/';
    $log_file = $log_path . $log_name;

    //check exists dir $log_path = GSDATAOTHERPATH.'pages_comments/';
    //if don't > create.
    if(!is_dir(GSDATAOTHERPATH.'pages_comments')){
	@mkdir(GSDATAOTHERPATH.'pages_comments', 0755);
    }

    //create dir backup
    if (!is_dir($log_path.'pages_comments_bak')){
       mkdir( $log_path.'pages_comments_bak');
    }

    //to integrate with blogs system
    //News manager: $pcintegNM, $pcpageNM
    //GS Blog: $pcintegGS, $pcpageGS
    include ('pages_comments/pcintegrblogs.php');

?>
	<script type="text/javascript">
        <!--
		function Viewmore(id){
                    var frm=document.getElementById(id);
                    if(frm.style.display=="table-row"){
                       frm.style.display="none";
                    }
                    else
                    if(frm.style.display=="none"){
		       frm.style.display="table-row";
		    }
                }
        -->
	</script> 
<?php

    echo '<div style="text-align: center;"><h3>'.strtoupper(stripslashes(html_entity_decode(i18n_r('pages_comments/pc_NCMNG')))).'</h3></div>'."\n";
    echo '<br />';
    echo '<div style=" display: block;">'."\n";
    echo '<a href="load.php?id=pages_comments&amp;action=pc_last" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_lastcom').'">'.i18n_r('pages_comments/pc_lastcom').'</a>'."\n";
    echo '<a href="load.php?id=pages_comments&amp;action=pc_viewpages" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE;  margin-left: 0px; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_NEWS').'">'.i18n_r('pages_comments/pc_NEWS').'</a>'."\n";
    echo '<a href="load.php?id=pages_comments&amp;action=pc_manager" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; margin-left: 0px; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_mng').'">'.i18n_r('pages_comments/pc_mng').'</a>'."\n";
    echo '<a href="load.php?id=pages_comments&amp;action=pc_backup" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; margin-left: 0px; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_bckup').'">'.i18n_r('pages_comments/pc_bckup').'</a>'."\n";
    echo '<a href="load.php?id=pages_comments&amp;action=pc_setting" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px;  color: #EEE; margin-left: 0px; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_settl').'">'.i18n_r('pages_comments/pc_sett').'</a>'."\n";
    echo '<a href="load.php?id=pages_comments&amp;action=pc_help" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px;  color: #EEE; margin-left: 0px; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_hlp').'">'.i18n_r('pages_comments/pc_hlp').'</a>'."\n";
    echo '</div>'."\n";
    echo '<br />'."\n";

    if (@$_GET['action'] == 'pc_viewpages') {
       include ('pages_comments/pcviewpages.php');

    }

    if (@$_GET['action'] == 'pc_manager') {
       include ('pages_comments/pcmanager.php');

    }

    if (@$_GET['action'] == 'add_pages') {
       include ('pages_comments/pcaddpages.php');

    }

    if (@$_GET['action'] == 'del_pages') {
       include ('pages_comments/pcdelpages.php');
    }

    if (@$_GET['action'] == 'edt_pages') {
       include ('pages_comments/pcedtpages.php');
    }

    if (@$_GET['action'] == 'pc_viewcom') {
       include ('pages_comments/pcviewcom.php');

    }

    if (@$_GET['action'] == 'pc_backup') {
       include ('pages_comments/pcbackup.php');

    }

    if (@$_GET['action'] == 'pc_setting') {
       include ('pages_comments/pcsettgs.php');

    }

    if (@$_GET['action'] == 'pc_help') {
       include ('pages_comments/summary.php');

    }

    if (@$_GET['action'] =='pc_last') {
       include ('pages_comments/pclastcom.php');
    }


    if (@$_GET['action'] =='') {
       include ('pages_comments/pclastcom.php');
    }
}





///////////////////////////////////////////////
//////          FRONTEND              /////////
//////  COMMENTS FROM PAGES OR NEWS   /////////
///////////////////////////////////////////////


function sv_pages_first(){

  ///////////////////////////////////////////////////////////   
  //   add comments to content of page if page is 
  //   in pages&comments and choosed comments=yes
  ///////////////////////////////////////////////////////////   

    $fich=return_page_slug();
    $log_path = GSDATAOTHERPATH.'pages_comments/';


    //check if exists $log_path = GSDATAOTHERPATH.'pages_comments/';
    //if do not > create
    if(!is_dir(GSDATAOTHERPATH.'pages_comments')){
	@mkdir(GSDATAOTHERPATH.'pages_comments', 0755);
    }
     
    //data of settings
    if (file_exists($log_path.'pc_settgs.xml')) {
   	$domDocument = new DomDocument();
	$domDocument->load($log_path.'pc_settgs.xml');
        
	//DOMXPath to filter
	$xpath = new DOMXPath($domDocument);
	$verN = $xpath->query('sett');			
  	$num = $verN->length;
	if ($num > 0){
 		    $dNdList = $verN->item(0)->getElementsByTagName( "integ_NM" );
                    $pcintegNM = $dNdList->item(0)->nodeValue; 
 		    $dNdList = $verN->item(0)->getElementsByTagName( "page_NM" );
                    $pcpageNM = $dNdList->item(0)->nodeValue; 
 		    $dNdList = $verN->item(0)->getElementsByTagName( "integ_GS" );
                    $pcintegGS = $dNdList->item(0)->nodeValue; 
 		    $dNdList = $verN->item(0)->getElementsByTagName( "page_GS" );
                    $pcpageGS = $dNdList->item(0)->nodeValue; 
	}	
    } 

    $com ='';
    //data of manager
    if (file_exists($log_path.'pc_manager.xml')) {
   	$domDocument = new DomDocument();
	$domDocument->load($log_path.'pc_manager.xml');

	//DOMXPath to filter by url: ext .xml(page of GS) or .NMG(post of news manager)
	$xpath = new DOMXPath($domDocument);
	if ($pcintegNM == 1 and isset($_GET['post'])){
		$postNM = $_GET['post'];
		$verNodeU =$xpath->query("page[url='$postNM.NMG']");	
	} else {
		$verNodeU =$xpath->query("page[url='$fich.xml']");
	}
        $num = 	$verNodeU->length;
        if ($num>0){
	    $dNdList = $verNodeU->item(0)->getElementsByTagName( "com" );
            $com = $dNdList->item(0)->nodeValue;
        }
    } 

    if ($com =='Y') {
			echo '<br />';
			sv_pages('', false);
    }

}

function pc_comments($content){
       /* TO DO */
       /*  
       $pos=strpos($content,'(%comments%)');
       if ($pos!=false){
              echo str_replace('(%comments%)','',$content);
              sv_pages('', true);    
              
      }*/
}


function sv_pages($usu='', $callcom=false){

  ///////////////////////////////////////////////////////////   
  //    check session
  ///////////////////////////////////////////////////////////   

	if (!isset($_SESSION)){ 
		session_start();
	}

  ///////////////////////////////////////////////////////////   
  //   main function that shows comments of a page
  ///////////////////////////////////////////////////////////   

	global $EMAIL;
	global $SITEURL;
	global $i18n;
	global $LANG;
	global $PRETTYURLS;

	$server_name = getenv ("SERVER_NAME");       // Server Name
	$request_uri = getenv ("REQUEST_URI");       // Requested URI

///////////////////////////////////////////////////////////////////////////////////////
	$err = '';
	$fich = return_page_slug();
	$log_path = GSDATAOTHERPATH.'pages_comments/';
	$ncm_file = $log_path.'pc_settgs.xml';
	$titleform = '';
	$dateseg = '';

///////////////////////////////////////////////////////////////////////////////////////
	//values by default: $EMAIL $LANG
	if (file_exists(GSDATAPATH.'users/'.$usu.'.xml')) {
		$data = getXML(GSDATAPATH.'users/'.$usu.'.xml');
		$EMAIL = $data->EMAIL;
		$LANG = $data->LANG;
	}

///////////////////////////////////////////////////////////////////////////////////////

  //data of settings
	if ( file_exists($ncm_file) ) {
		$domDocument = new DomDocument();
		$domDocument->load($ncm_file);

		//DOMXPath to filter
		$xpath = new DOMXPath($domDocument);
		$filex = 1;
		$num=0;
		$n=0; 
		$verN = $xpath->query('sett');			
		$num = $verN->length;
		if ($num > 0){	
			$dNdList = $verN->item(0)->getElementsByTagName( "nclang" );
			$LANG = $dNdList->item(0)->nodeValue;
			$dNdList = $verN->item(0)->getElementsByTagName( "ncemail" );
			$EMAIL = $dNdList->item(0)->nodeValue;
			$dNdList = $verN->item(0)->getElementsByTagName( "ncffpost" );
			$ffpost = substr($dNdList->item(0)->nodeValue,2);
			$dNdList = $verN->item(0)->getElementsByTagName( "phpmailer" );
			$phpmailer = $dNdList->item(0)->nodeValue;
			$dNdList = $verN->item(0)->getElementsByTagName( "pcuserdel" );
			$pcuserdel = $dNdList->item(0)->nodeValue;
			$dNdList = $verN->item(0)->getElementsByTagName( "integ_NM" );
			$pcintegNM = $dNdList->item(0)->nodeValue; 
			$dNdList = $verN->item(0)->getElementsByTagName( "page_NM" );
			$pcpageNM = $dNdList->item(0)->nodeValue; 
			$dNdList = $verN->item(0)->getElementsByTagName( "integ_GS" );
			$pcintegGS = $dNdList->item(0)->nodeValue; 
			$dNdList = $verN->item(0)->getElementsByTagName( "page_GS" );
			$pcpageGS = $dNdList->item(0)->nodeValue; 
			if ($callcom== true ){ 
				$dNdList = $verN->item(0)->getElementsByTagName( "emot" );
				$vemot = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "capt" );
				$capt = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "moder" );
				$moder = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "nmusr" );
				$ncusr = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "npag" );
				$cada = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "ord" );
				$eleg = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_nwords" );
				$pcomnwords = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_public" );
				$pcompublic = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_reply" );
				$pcomreply = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_reply_hd" );
				$pcomreplyhd = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_nwords" );
				$pcomnwords = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_blacklist" );
				$pcomblacklist = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_words_bl" );
				$pcomwordsbl = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_rating" );
				$pcomrating = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_rating_tp" );
				$pcomratingtp = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_social" );
				$pcomsocial = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_social_tp" );
				$pcomsocialtp = $dNdList->item(0)->nodeValue;  
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_report_ina" );
				$pcomreportina = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_report_lab" );
				$pcomreportlab = $dNdList->item(0)->nodeValue; 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_notify" );
				$pcomnotify = $dNdList->item(0)->nodeValue; 
			}
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
	//i18n compatible
	global $language;
	if (isset($_GET['setlang'])){
		$LANG = $_GET['setlang']. '_'.strtoupper($_GET['setlang']);
	}
	if (isset($language)){
		$LANG = $language. '_'.strtoupper($language);
	}
    
	//i18n lang
        i18n_merge('pages_comments', $LANG) || i18n_merge('pages_comments','en_US'); 

///////////////////////////////////////////////////////////////////////////////////////////
	//check url by prettyurls
	global $idpret;
	$idpret = find_url($fich,'');

	if ($PRETTYURLS =='') {
		if ($fich == 'index'){
			$idpret = $idpret.'?';
		}
	} else {
			$idpret = $idpret.'?';		
	}

//////////////////////////////////////////////////////////////////////////////////////
	//To integrate with NEWS MANAGER
	$urlsearch = $fich.'.xml';
	$post = '';
	if ($pcintegNM == 1 && $fich == $pcpageNM){
		$post = isset($_GET['post']) ? $_GET['post'] : '';
		$urlsearch = isset($_GET['post']) ? $post.'.NMG' : $fich.'.xml';
		if($PRETTYURLS =='') {
			$idpret = isset($_GET['post']) ? $idpret.'&amp;post='.$post : $idpret;
		} else {
			$idpret = isset($_GET['post']) ? $idpret.'post/'.$post : $idpret;
		}
	}
	$log_name = $fich.$post.'.log'; 
	$log_file = $log_path . $log_name;

///////////////////////////////////////////////////////////////////////////////////////
	//Include GSCONFIG
	if (file_exists('gsconfig.php')) {
		include_once('gsconfig.php');
	}

///////////////////////////////////////////////////////////////////////////////////////
	// Debugging
	if (defined('GSDEBUG')){
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 1);
	} else {
		error_reporting(0);
		@ini_set('display_errors', 0);
	}

///////////////////////////////////////////////////////////////////////////////////////
	//check folder $log_path = GSDATAOTHERPATH.'pages_comments/';
	//if do not exists > create.
	if(!is_dir(GSDATAOTHERPATH.'pages_comments')){
		@mkdir(GSDATAOTHERPATH.'pages_comments', 0755);
	}

///////////////////////////////////////////////////////////////////////////////////////
	//data of manager
	if (file_exists($log_path.'pc_manager.xml') and $callcom==false) {
		$domDocument = new DomDocument();
		$domDocument->load($log_path.'pc_manager.xml');

		//DOMXPath to filter by url
		$xpath = new DOMXPath($domDocument);
		$verNodeU =$xpath->query("page[url='$urlsearch']");

		foreach($verNodeU as $node) {
			//$dNdList = $node->getElementsByTagName( "*" );
			$cada = $node->getElementsByTagName( "npag" )->item(0)->nodeValue;
			$capt = $node->getElementsByTagName( "capt" )->item(0)->nodeValue;
			$vemot = $node->getElementsByTagName( "emot" )->item(0)->nodeValue;
			$eleg = $node->getElementsByTagName( "ord" )->item(0)->nodeValue;
			$moder = $node->getElementsByTagName( "moder" )->item(0)->nodeValue;
			$ncusr = $node->getElementsByTagName( "nmusr" )->item(0)->nodeValue;
			$titleform = $node->getElementsByTagName( "titleform" )->item(0)->nodeValue;
			$pcomnwords = $node->getElementsByTagName( "pcom_nwords" )->item(0)->nodeValue;
			$pcompublic = $node->getElementsByTagName( "pcom_public" )->item(0)->nodeValue;
			$pcomreply = $node->getElementsByTagName( "pcom_reply" )->item(0)->nodeValue;
			$pcomreplyhd = $node->getElementsByTagName( "pcom_reply_hd" )->item(0)->nodeValue;
			$pcomblacklist = $node->getElementsByTagName( "pcom_blacklist" )->item(0)->nodeValue;
			$pcomwordsbl = $node->getElementsByTagName( "pcom_words_bl" )->item(0)->nodeValue;
			$pcomrating =  $node->getElementsByTagName( "pcom_rating" )->item(0)->nodeValue;
			$pcomratingtp = $node->getElementsByTagName( "pcom_rating_tp" )->item(0)->nodeValue;
			$pcomsocial = $node->getElementsByTagName( "pcom_social" )->item(0)->nodeValue;
			$pcomsocialtp = $node->getElementsByTagName( "pcom_social_tp" )->item(0)->nodeValue;
			$pcomreportina = $node->getElementsByTagName( "pcom_report_ina" )->item(0)->nodeValue;
			$pcomreportlab = $node->getElementsByTagName( "pcom_report_lab" )->item(0)->nodeValue;
			$pcomnotify = $node->getElementsByTagName( "pcom_notify" )->item(0)->nodeValue;
		}
	}

//////////////////////////////////////////////////////////////////////////////////// 
  //	check if submit form
  //	$mi_array =  data passed in form if exists error in captcha or empty fields
  //	$mi_arrayp = if !error in captcha writes correspondient form with data =""
////////////////////////////////////////////////////////////////////////////////////

	global $mi_array;
	$mi_array = array();
	$idf = 0;
	if (isset($_POST['guest-submit'])) {
		include ("pages_comments/check.php");
	}

	global $mi_arrayp;
	$mi_arrayp= array(
		"nombre" =>  '',
		"email" =>  '',
		"city" =>  '',
		"comentario" =>  '',
		"subject" =>  '',
	);


///////////////////////////////////////////////////////////////////////////////////////////
	//array blacklist if there is control
	$array_blacklist = '';
	if ($pcomblacklist == 'Y') {
		$array_blacklist = explode(',', $pcomwordsbl);	
	}

///////////////////////////////////////////////////////////////////////////////////////////
	//call function users controls if page has user control
	if ($ncusr == 'Y'){
		// pcusers($ncusr);    
		//data of memberonly of page
		$xmldata = GSDATAPAGESPATH.$fich.'.xml';
		$dataslug = getXML($xmldata);

		//Check if page is for members
		if($dataslug->memberonly == "yes"){
			if (!isset($_SESSION["LoggedIn"])){ 
				exit;     
			}
		}
	}

///////////////////////////////////////////////////////////////////////////////////////////
	//DELETE POST BY USER
	if (isset($_SESSION["LoggedIn"]) and file_exists($log_file)){ 
		if (@$_GET['iddelpost']!= '') {
			$domDocument = new DomDocument();
			$domDocument->preserveWhiteSpace = FALSE;
			$domDocument->load($log_file);
			$xpath = new DOMXPath($domDocument);
			$q = 0;
			if (@$_GET['sidelpost'] > 0 ){
				//remove only this comment
				$verNodeU = $xpath->query("entry[Id=".@$_GET['iddelpost']." and SubId=".@$_GET['sidelpost']."]");
				$delcomus='S'; 
			} else {
				if ($pcuserdel == 'Y'){
					//if delete main comment, remove replies too 
					$delcomus='S';
					$verNodeU = $xpath->query("entry[Id=".@$_GET['iddelpost']."]");
				} else if ($pcuserdel == 'N'){
					$verNodeU = $xpath->query("entry[Id=".@$_GET['iddelpost']." and answ='y']");
					$dnlLen= $verNodeU->length;
					if ($dnlLen > 0){
						//Do not remove main comment becuase has replies.
						$delcomus='N';
?>
						<script type="text/javascript">
							alert("<?php echo i18n_r('pages_comments/pc_delcommain'); ?>");
							location.href="<?php echo $idpret; ?>";
							exit;
						</script> 
<?php 
					} else {
						$delcomus='S';                            
						$verNodeU = $xpath->query("entry[Id=".@$_GET['iddelpost']."]");
					}   
				}              
			}
			if ($delcomus == 'S'){
				$dnlLen= $verNodeU->length;
				$auth= $verNodeU->item(0)->getElementsByTagName( "Nb" );
				$auth= $auth->item(0)->nodeValue;
				if ($_SESSION["Username"] == stripslashes(html_entity_decode($auth))){
					for ($q; $q<$dnlLen; $q++){
						$ndL = $verNodeU ->item($q)->parentNode;
						$ndL -> removeChild($verNodeU ->item($q));
					}
				$domDocument->save($log_file);
				//redirect for deleting end of line
?>    
				<script type="text/javascript">
					location.href="<?php echo $idpret; ?>";
					exit;
				</script>   
<?php 
				}
			}  
		}
	}
///////////////////////////////////////////////////////////////////////////////////////////

	//recall data 
	if(file_exists($log_file)) {
		$log_data = getXML($log_file);
	}

///////////////////////////////////////////////////////////////////////////////////////////
	//array emoticons and set variables
	$array_emot = news_emot('array','id');
	$myfile=''; 
	$count = 1;
	$at = 0;
	$mi='';
	$pagi=1;
	$id=1;

///////////////////////////////////////////////////////////////////////////////////////////
	//if file of comment's log exist and comments are publics => show comments :)
	if(file_exists($log_file) && $pcompublic == 'Y'){
		$domDocument = new DomDocument();
		$domDocument->load($log_file);
		//DOMXPath to filter by answ=n
		$xpath = new DOMXPath($domDocument);
		//looking for number for pagination
		$verN = $xpath->query("entry[answ='n' and moder='Y']");
		$numpag = $verN->length;
		//looking for number for main comments
		$verN = $xpath->query("entry[answ='n']");
		$num = 	$verN->length;
		if ($num > 0){
			if ($ncusr != 'Y'){
				echo '<div id="hd_cm">';
					echo i18n_r('pages_comments/cmm');
					//mark with 'pcom' to link directly with comments
					echo '<a style="background-color: transparent; color: transparent; font-weight: normal; text-decoration: none; float: right; line-height: 10; width: 0px; height: 10px;" id="pcom">.</a>';
				echo '</div>';
			} else {
				//mark with 'pcom' to link directly with comments
				echo '<a style="background-color: transparent; color: transparent; font-weight: normal; text-decoration: none; float: right; line-height: 10; width: 0px; height: 10px;" id="pcom">.</a>';
			}
		} else {
			$num = 1;
		}

		///////////////////////////////////////////////////////////////////////////
		//search $id, is parameter that define number of new comment(answ='n') that is saved in log.
		$verNodeU = $xpath->query("entry/Id[../answ='n']");
		$dnlLen=$verNodeU->length;
		if ($dnlLen > 0){
			$id= ($verNodeU->item($num - 1)->nodeValue) + 1;
			$verNodeU = $xpath->query("entry");
			$dnlLen= $verNodeU->length;

			///////////////////////////////////////////////////////////////////////////
			//pagination
		    include ("pages_comments/pcpagination.php");
		}

		////////////////////////////////////////////////////////////////////////////
		//order
		if ($eleg != 'A' && $eleg != 'D'){$eleg = 'A'; }
		if ($eleg == 'A'){
			$num = 0;
		} else if ($eleg =='D') {
			$num = $dnlLen;
		}

		////////////////////////////////////////////////////////////////////////////
        //See COMMENTS
		echo  '<div class="cbcomment">';
		for ($q= 0; $q< $dnlLen+1; $q++){
			$verNodeU =$xpath->query("entry[answ='n' and position()=".$num."]");
			foreach($verNodeU as $node) {
				if ($count <= ($cada * $pagi) && $count > ($cada * ($pagi-1))) {
					$lin2='';
					$dNdList = $node->getElementsByTagName( "*" );
					$at = $node->getAttribute('id');
					//search how many replies have a main comment
					$verNodeU_r = $xpath->query("entry[Id=$at and moder='Y' and answ='y']");
					$dnlLen_r=$verNodeU_r->length;
                    
					//start         
					$linauth = '';   
					$linrpl = '';
					$pc_node_rating = '';	
					$linrating = '';

					foreach($dNdList as $node) {
						$name= $node->nodeName;
						$d = $node->nodeValue;
						$d = filtro(stripslashes(html_entity_decode($d)));
						$n = strtolower($name);

						//check if its an email address
						if (check_email_address($d)) {
							$d = '<a href="mailto:'.$d.'">&nbsp;'. $d.'</a>';
						}

						//check first line
						if ($n == 'nb') {
							$linauth = stripslashes(html_entity_decode($d));
							$linrpl = '';
							//if user is logged in then is possible delete post
							if (isset($_SESSION["LoggedIn"]) and $ncusr == 'Y'){ 
								if ($_SESSION["Username"] == stripslashes(html_entity_decode($d))) {
									$linrpl = '<a class="replyblg" href="'.$idpret.'&amp;iddelpost='.$at.'&amp;sidelpost=0"  onClick="return confirmar(&quot;'.i18n_r('pages_comments/pc_delcom').'&quot;)">'.i18n_r('pages_comments/pc_del').'</a>';
								}
								$linrpl .= '<a class="replyblg" href="javascript:Insertcom(&quot;form'.$count.'&quot;,&quot;'.$count.'&quot;,&quot;'.$count.'&quot;)">'.i18n_r('pages_comments/mf').'</a>';
							}
							if ($ncusr == 'N'){
								$linrpl .= '<a class="replyblg" href="javascript:Insertcom(&quot;form'.$count.'&quot;,&quot;'.$count.'&quot;,&quot;'.$count.'&quot;)">'.i18n_r('pages_comments/mf').'</a>';
							}    
						} else if ($n == 'date'){
							$d = strtotime(substr($d,0,strpos($d,'+')-1));
							$d = strftime($ffpost, $d);
							$fftime = $d;
							$pcyear = strftime('%Y', strtotime($d));  //year 4 digits
							$pcmon = strftime('%h',strtotime($d));  //month 3 letters
							$pcday = strftime('%d', strtotime($d));  //day 2 numbers
							$pctime =  strftime('%R', strtotime($d));  //HH:MM

						} else if ($n == 'dateseg'){
							$anchor = ($d == $dateseg) ? 'id="pc"' : '';
						} else if ($n == 'cm') {  //comments  
							$comment = BBcodeN($d);
							if ($pcomblacklist == 'Y'){
								$comment = pcblacklist($comment, $array_blacklist);
							}
							$comment = str_replace(htmlentities('<br />', ENT_QUOTES, "UTF-8"), '<br />', $comment);
							foreach  ($array_emot as $key => $val) { 
								$comment = str_replace($array_emot[$key], '<img src="'.$SITEURL.'plugins/pages_comments/images/img_emot/'.substr($array_emot[$key],1, strlen($array_emot[$key])-2).'.gif" alt="'.$array_emot[$key].'" />', $comment);    
							}	
						} else if ($n == 'ct'){
							$ct = '';   
							if (trim($d) != ''){
								$ct =  htmlentities(i18n_r('pages_comments/WHO'), ENT_QUOTES, "UTF-8").' '.$d;
							}
						} else if ($n == 'captcha'){}
						else if ($n === 'em'){}
						else if ($n == 'ip_address'){}
						else if ($n == 'answ'){}	
						else if ($n == 'moder'){$accept_main=$d;}	
						else if ($n == 'id') {}
						else if ($n == 'subid') {}
						else if ($n == 'subj'){
							$lin2= $d;
						} else if ($n == 'pcrating'){		
							if ($pcomrating == "Y" ) {
								$pc_node_rating = $d;
								$linrating = '<img name="vote" class="rating" src="'.$SITEURL.'plugins/pages_comments/images/rating/'.$pcomratingtp.'_up.png" title="'.i18n_r('pages_comments/pc_ratingup').'" onclick="getVote(&quot;+1&quot;,&quot;'.$log_file.'&quot;,&quot;'.$at.'&quot;,&quot;0&quot;,&quot;'.$count.'&quot;,&quot;'.$SITEURL.'&quot;)" />';
								$linrating .= '<img name="vote" class="rating" src="'.$SITEURL.'plugins/pages_comments/images/rating/'.$pcomratingtp.'_down.png" title="'.i18n_r('pages_comments/pc_ratingdown').'" onclick="getVote(&quot;-1&quot;,&quot;'.$log_file.'&quot;,&quot;'.$at.'&quot;,&quot;0&quot;,&quot;'.$count.'&quot;,&quot;'.$SITEURL.'&quot;)" />';
								$linrating .= '<span id="pcrating'.$count.$at.'0" class="rating">'.$pc_node_rating.'</span>';
							}
						}
					} //end second foreach

					//only if comments are accepts by moderator then they are shown. 
					echo '<div>';
					if ($accept_main == 'Y'){
						echo '<div class="fullcomment">';
						echo '<div class="tabla">';
						echo '<div class="commain" onMouseOver="shwimg(&quot;imgcontentmain'.$at.'&quot;)" onMouseOut="Nshwimg(&quot;imgcontentmain'.$at.'&quot;)">';
						echo '<div class="commainblg" '.$anchor.'>';
							echo '<div class="subjblg">';
								echo '<img src="'.$SITEURL.'plugins/pages_comments/images/etq.png" alt="" />';
								echo '<span class="author">'.$linauth.'</span>';
								if (trim($lin2) != ''){
									echo ': '.$lin2;
								}
								echo '<div class="blq">';     
									echo '<a href="javascript:Insertcom(&quot;contentmain'.$at.'&quot;,&quot;'.$at.'&quot;,&quot;'.$count.'&quot;)"><img style="visibility: hidden;" src="'.$SITEURL.'plugins/pages_comments/images/ico-minor.png" id="imgcontentmain'.$at.'" alt="'.i18n_r('pages_comments/pc_collapse').'" /></a>';
									echo $linrating;
								echo '</div>';
								echo '<span class="arrow fill"></span>';
							echo '</div>';
							//clean float
							echo '<div class="clear"></div>';
							echo '<div id="contentmain'.$at.'" style="display: block;">';
								echo '<div class="comtblg"><p class="text">'.$comment.'</p></div>';
									echo '<div class="lastline">';
										echo '<div class="buttoncom">';
											echo '<div class="fftimeblg">';
												echo htmlentities(i18n_r('pages_comments/pubd'), ENT_QUOTES, "UTF-8").' '.$fftime;
												if ($ct != ''){
													echo ', '.$ct;
												} 
											echo '</div>';
										echo '</div>';
										//only if replies are allowed displays form 'reply'
										if ($pcomreply == 'Y'){	
											echo '<div class="replyblg">';
												if ($dnlLen_r > 0){  
													echo '<a href="javascript:Insertcom(&quot;tablar'.$at.'&quot;,&quot;'.$at.'&quot;,&quot;'.$count.'&quot;)" title="'.i18n_r('pages_comments/pc_view').' '.$dnlLen_r.' '.i18n_r('pages_comments/pc_comc').'" id="bctablar'.$at.'">'.i18n_r('pages_comments/pc_comc').' ('.$dnlLen_r.')'.'</a>';
												}
												echo '&nbsp;&nbsp;&nbsp;';
												echo $linrpl;
											echo '</div>';
										}
									echo '</div>';
								echo '</div>'; 
							echo '</div>'; 
						echo '</div>';
						echo '</div>';
						//clean float
						//echo '<div class="clear"></div>';

////////////////////////////////////////////////////////////////////////
		//insert replies to this comments if reply = Y
		if ($pcomreply == 'Y'){
		    $displayrp = 'none';
		    if ($pcomreplyhd == 'N' || $idf == $at){
			    $displayrp = 'block';
		    }
			
			echo '<div class="tablardisplay" id="tablar'.$at.'" style="display: '.$displayrp.';">';
		    $verNodeU_r = $xpath->query("entry[Id=$at and answ='y']");
		    $dnlLen_r=$verNodeU_r->length;
                    $lindelete = ''; 
                    $sidel = '0'; 
		    if ($dnlLen_r ==0){ echo '<br />'; }
			$countr=1;
			foreach($verNodeU_r as $node) {
			   $dNdList_r = $node->getElementsByTagName( "*" );
			   $at_r = $node->getAttribute('id');
			   foreach($dNdList_r as $node) {
				 $name= $node->nodeName;
				 $d =$node->nodeValue;
				 $d = filtro(stripslashes(html_entity_decode($d)));
				 $n = strtolower($name);
				 //check if its an email address
				 if (check_email_address($d)) {
				     $d = '<a href="mailto:'.$d.'">&nbsp;'.$d.'</a>';
				 }

				 //check first line
				 if ($n == 'nb') {
                                     $namerpl = $d; 
                                     if ($ncusr == 'Y'){
                                         if (isset($_SESSION["LoggedIn"])){ 
                                               if ($_SESSION["Username"] == stripslashes(html_entity_decode($namerpl))) {
                                                   $lindelete = '<a href="'.$idpret.'&amp;iddelpost='.$at.'&amp;sidelpost='.$sidel.'"  onClick="return confirmar(&quot;'.i18n_r('pages_comments/pc_delcom').'&quot;)">'.i18n_r('pages_comments/pc_del').'</a>'; //class="replyblg"
                                               }
                                         }
                                     }
				 } else if ($n == 'date'){
				     $d = strtotime(substr($d,0,strpos($d,'+')-1));
				     $timerpl = strftime($ffpost, $d);
				 } else if ($n == 'dateseg'){
					$anchor = ($d == $dateseg) ? 'id="pc"' : '';
				 } else if ($n == 'cm') {  //comment
                                      $commentrpl = BBcodeN($d);
				      if ($pcomblacklist == 'Y'){
					  $commentrpl = pcblacklist($commentrpl, $array_blacklist);
				      }
                                      $commentrpl = str_replace(htmlentities('<br />', ENT_QUOTES, "UTF-8"), '<br />', $commentrpl);
                                      foreach ($array_emot as $key => $val) { 
                                             $commentrpl=str_replace($array_emot[$key], '<img src="'.$SITEURL.'plugins/pages_comments/images/img_emot/'.substr($array_emot[$key],1, strlen($array_emot[$key])-2).'.gif" alt="'.$array_emot[$key].'" />', $commentrpl);    
				      }
				 }
				 else if ($n == 'ct'){
                                      $ctrpl = ''; 
                                      if (trim($d) != ''){
                                          $ctrpl =  htmlentities(i18n_r('pages_comments/WHO'), ENT_QUOTES, "UTF-8").' '.$d;
                                      }
                                 }
				 else if ($n == 'captcha'){}
				 else if ($n == 'em'){}
   				 else if ($n == 'ip_address'){}
				 else if ($n == 'answ'){}
				 else if ($n == 'moder'){$accept_rp = $d;}	
				 else if ($n == 'id'){}
                                 else if ($n == 'subid'){ $sidel = $d; }
				 else if ($n == 'pcrating'){
					$pc_node_rating = '';	
					$linrating_rp = '';			
					if ($pcomrating == "Y" ) {
						$pc_node_rating = $d;
						$linrating_rp = '<img name="vote" class="rating" src="'.$SITEURL.'plugins/pages_comments/images/rating/'.$pcomratingtp.'_up.png" title="'.i18n_r('pages_comments/pc_ratingup').'" onclick="getVote(&quot;+1&quot;,&quot;'.$log_file.'&quot;,&quot;'.$at.'&quot;,&quot;'.$sidel.'&quot;,&quot;'.$count.'&quot;,&quot;'.$SITEURL.'&quot;)" />';
						$linrating_rp .= '<img name="vote" class="rating" src="'.$SITEURL.'plugins/pages_comments/images/rating/'.$pcomratingtp.'_down.png" title="'.i18n_r('pages_comments/pc_ratingdown').'" onclick="getVote(&quot;-1&quot;,&quot;'.$log_file.'&quot;,&quot;'.$at.'&quot;,&quot;'.$sidel.'&quot;,&quot;'.$count.'&quot;,&quot;'.$SITEURL.'&quot;)" />';
						$linrating_rp .= '<span id="pcrating'.$count.$at.$sidel.'" class="rating">'.$pc_node_rating.'</span>';
					}
                           	 }	
			    } //end second foreach of replies
                            //start replies of comments
                            if ($accept_rp == 'Y'){
                            echo '<div class="comrepl" '.$anchor.'>';
                              echo '<div class="comreplblg">';
                                 echo '<div class="subjblg">';
                                      echo '<div class="img">';
                                           echo '<img height="18" src="'.$SITEURL.'plugins/pages_comments/images/ico-reply.png" alt="" />';
                                      echo '</div>';
                                      echo '<span class="author">'.stripslashes(html_entity_decode($namerpl)).'</span>'; 
				      //echo '<span class="arrow border"></span>';
				      echo '<span class="arrow fill"></span>'; 
                                 echo '<div class="rating-wrapper">';
                                   echo $linrating_rp;
                                 echo '</div>';
                                 echo '</div>';      
                                 echo '<div class="comtblg"><p class="text">'.$commentrpl.'</p></div>';
                                 echo '<div class="lastline">';
                                      echo '<div class="buttoncom">';
                                           echo '<div class="fftimeblg">';
                                                echo htmlentities(i18n_r('pages_comments/pubd'), ENT_QUOTES, "UTF-8").' '.$timerpl;
                                                if ($ctrpl != ''){
                                                    echo ', '.$ctrpl;
                                                }
                                           echo '</div>';
                                      echo '</div>';
                                      echo '<div class="replyblg">';
									  // echo $linrpl;
                                      		echo $lindelete; 
				      				  echo '</div>';
                                 echo '</div>';
                              echo '</div>';
                            echo '</div>';
                            } //end if $public_rp == N (comments of replies are not moderated)
			    //echo '</div>';  //end tabla replies
			    $countr++;
			} //end first foreach of replies
                        echo '</div>'; //end replay tabla
			}
                    echo '</div>'; //end fullcomment;

//////////////////
			echo '<div class="fullform">';
                        //Integrate frontend users plugin
                        if ($ncusr=='Y'){        
                            if (isset($_SESSION["LoggedIn"])){ 
                                    news_form('none', 'y', $log_file, $EMAIL, $count, $at, $capt,$vemot, $moder, $ncusr, $phpmailer, $pcomnwords, i18n_r('pages_comments/pc_reply'), $sidel);
                            }
                            echo '</div>'; 
                        } else {
                            news_form('none', 'y', $log_file, $EMAIL, $count, $at, $capt, $vemot, $moder, $ncusr,
$phpmailer, $pcomnwords, i18n_r('pages_comments/pc_reply'), $sidel);
                            echo '</div>'; 
                        }
                    echo '</div>'; //end div line826
			//clean float
			//echo '<div class="clear"></div>';

                    } //end if $accept_main == Y  (main comments are not moderated)
		}			
		$count++;
			
	    } //end first foreach of comment main

   	    //add or substract if is order ascending or descending
  	   if ($eleg == 'A'){
	       $num++;
    	   } else {
	       $num--;
	   }
	}  //end for ($q= 0; $q< $dnlLen; $q++)
	echo '</div>';
   }  //end if exist log and publics comments


//----------------------------------------------------------
//
//              include javascripts
    include ("pages_comments/inc/pagecomments.js");
    include ("pages_comments/inc/placeholders.min.js");
//
//----------------------------------------------------------

	//clean float
	//echo '<div class="clear"></div>';

	//The last form
	echo '<div class="lastform">';
	//Set Titleform for last form
	$titleform = ($titleform =='') ? i18n_r('pages_comments/news') : $titleform;

        //only shows form if page has comments
        //Integrate form with front end users plugin
        if ($ncusr=='Y'){
           if (isset($_SESSION["LoggedIn"])){ 
    	        if ($capt!='' and $vemot!='' and $moder!=''){ 
        	   //form to end 
	           news_form('block','n', $log_file, $EMAIL, $id, $id, $capt, $vemot, $moder, $ncusr, $phpmailer,  $pcomnwords, $titleform, '0');
 	        }
           }
        } else { 
    	    if ($capt!='' and $vemot!='' and $moder!=''){
        	 //form to end
	         news_form('block','n', $log_file, $EMAIL, $id, $id, $capt, $vemot, $moder, $ncusr, $phpmailer, $pcomnwords, $titleform, '0');
 	    }
        }
	echo '</div>';
    }

    function news_form($dpl, $nn, $myfile, $email, $count, $at, $capt, $vemot, $moder,$ncusr,$phpmailer, $pcomnwords, $titleform, $sidf) {
		global $SITEURL;
		global $mi_arrayp;
		global $mi_array;
		global $idpret;

		//last captcha  
		$imfin = $count;

		//if error in captcha, or..., show entries write in form
		$mi_arrayq = $mi_arrayp;
		if (array_key_exists('q_count', $mi_array)){
			if ($mi_array['q_count'] == $count){
				$mi_arrayq = $mi_array;
				$dpl ='block';
			}
		}

		if (!isset($_GET['pag'])) {
			$pagi= '';
		} else {
			$pagi= "&amp;pag=".@$_GET['pag'];
		}

		//control uri 
		$request_uri = getenv ("REQUEST_URI");      

		//charge emoticons
		$s_emot = news_emot("img","form".$imfin);

		//Titleform
		echo '<div class="titleform">';
			echo '<span class="mh3" id="formtitle'.$imfin.'" style="display:'.$dpl.';">'.$titleform.'</span>';
		echo '</div>';

		//show cancel reply link
		if ($dpl == 'none'){
			echo '<a id="forma'.$imfin.'" style="display: none;" href="">Cancel reply</a>'."\n";
		}
  
		//Show Form
		echo '<form class="formsp" style="display:'.$dpl.';" name="formulario" id="form'.$imfin.'" action="'.$idpret.$pagi.'#pc" method="post">';
			include ("pages_comments/templateform/pcform.php");
			echo '<div style="visibility: hidden;">';
				echo '<input type="hidden" name="guest[q_file]" value="'.$myfile.'">';
				echo '<input type="hidden" name="guest[q_uri]" value="'.$request_uri.'">';
				echo '<input type="hidden" name="guest[q_count]" value="'.$count.'">';
				echo '<input type="hidden" name="guest[q_ans]" value="'. $nn.'">';
				echo '<input type="hidden" name="guest[q_idf]" value="'. $at.'">';
				echo '<input type="hidden" name="guest[q_sidf]" value="'. $sidf.'">';
				echo '<input type="hidden" name="guest[q_tp]" value="pc">';
				if (!isset($_SESSION)){session_start(); }
				$_SESSION["pc_token".$count]  = sha1(microtime() + 'cumbe20122013');
				echo '<input type="hidden" name="guest[q_token'.$count.']" value="'.$_SESSION["pc_token".$count].'">';
			echo '</div>';
			echo '</div>';
		echo '</form>';

	}

function news_emot($opcion, $id){
  global $SITEURL;
  $ruta = GSPLUGINPATH.'pages_comments/images/img_emot/';
  $array_emot= array();
  $s_emot = '';
  if (is_dir($ruta)) {
      if ($dh = opendir($ruta)){
         while (($file = readdir($dh)) !== false){
         	if($file!="." AND $file!=".." AND $file!=".htaccess" AND is_dir($ruta . $file)== false AND strtolower(substr($file,-4))=='.gif'){
			$array_emot[substr($file, 0, strlen($file)-4)] = '['.substr($file, 0, strlen($file)-4).']';
                        $s_emot = $s_emot.' <a href="javascript:Smile(&quot;'.$id.'&quot;,&quot;['.substr($file, 0, strlen($file)-4).']&quot;)"><img src="'.$SITEURL.'plugins/pages_comments/images/img_emot/'.substr($file, 0, strlen($file)-4).'.gif" alt="'.substr($file, 0, strlen($file)-4).'" /></a>';
                }
         }
         closedir($dh);
      }
  }
  if ($opcion == 'array'){
              return $array_emot;
  }
  elseif ($opcion == 'img') {
              return $s_emot;
  }

}

function filtro ($texto) {
    $htmlf = array("script", "<", ">");
    $filtrado = array(" scrpt", "&lt;", "&gt;");
    $final = str_replace($htmlf, $filtrado, $texto);

    return $final;
}

function pcblacklist($texto, $array_blacklist){
	$texton = str_replace($array_blacklist, "x..x", $texto);
	return $texton;
}


function BBcodeN($texton){ 
    $BBcodeN = array(	 
       "/\<(.*?)>/is",
       "/\[i\](.*?)\[\/i\]/is",
       "/\[b\](.*?)\[\/b\]/is",
       "/\[u\](.*?)\[\/u\]/is",
       "/\[img\](.*?)\[\/img\]/is",
       "/\[url=(.*?)\](.*?)\[\/url\]/is",
       "/\[color=(.*?)\](.*?)\[\/color\]/is"
   );

   $html = array(
	 
	"<$1>",
	"<span style=\"font-style: italic;\">$1</span>",
	"<span style=\"font-weight:bold;\">$1</span>",
	"<span style=\"text-decoration:underline;\">$1</span>",
	"<img src=\"$1\" />",
	"<a href=\"$1\" target=\"_blank\">$2</a>",
	"<span style=\"color:$1\">$2</span>"
   );

    $BBcodeNn = array(	 
       "/\[url\](.*?)\[\/url\]/is",
   );

   $htmln = array(
	"<a href=\"$1\" target=\"_blank\">$1</a>",
   );

	$texton = preg_replace($BBcodeN, $html, $texton);
	$texton = preg_replace($BBcodeNn, $htmln, $texton);
	return $texton;
}


 function pcusers($ncusr){
  ///////////////////////////////////////////////////////
  //   function that integrate front end users plugin
  ///////////////////////////////////////////////////////

          if (isset($_SESSION["LoggedIn"])){ 
             echo '<p> '. welcome_message_login().'</p>';
          }
          else {
             echo '<p>'.i18n_r('pages_comments/wrtusr').'<a id="ahrefregisterform" href="javascript:Insertcom(&quot;registerform&quot;,&quot;0&quot;,&quot;0&quot;)">'.i18n_r('pages_comments/nc_rgs').'</a></p>';
             echo '<p> '. show_login_box().'</p>';
	     echo '<p id="userloginregistertitle" style="display: none;">'.i18n_r('pages_comments/nc_rgsr').'</p>';
             echo '<p> '. user_login_register().'</p>';
         }

 }

?>
