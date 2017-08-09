<?php

//InsertForm (reply, log_file, email, count, at, capt, vemot, moder, ncusr, phpmailer, pcomnwords, titleform, siteurl, lang, miarrayp, miarray){

       global $siteurl;
       global $lang;
       global $mi_arrayp;
       global $mi_array;

	$reply = $_REQUEST['reply'];
	$logfile = $_REQUEST['logfile'];
	$email = $_REQUEST['email'];
	$count = $_REQUEST['count'];
	$at = $_REQUEST['at'];
	$capt = $_REQUEST['capt'];
	$vemot = $_REQUEST['vemot'];
	$moder = $_REQUEST['moder'];
	$ncusr = $_REQUEST['ncusr'];
	$phpmailer = $_REQUEST['phpmailer'];
	$pcomnwords = $_REQUEST['pcomnwords'];
	$titleform = $_REQUEST['titleform'];
	$siteurl = $_REQUEST['siteurl'];
	$lang = $_REQUEST['lang'];
	$miarrayp = $_REQUEST['miarrayp'];
	$miarray = $_REQUEST['miarray'];

	$GSPLUGINPATH = substr($logfile, 0 , strpos($logfile, '/data/other')).'/plugins/';	

	//set LANG
	include ("./lang/".$lang.".php");
	$lang1 = $i18n;
	if ($lang != 'en_US'){
		include ("./lang/en_US.php");
		$lang2 = $i18n;
		$i18n = array_merge($lang2, $lang1);
	}

	//set titleform
	if ($reply == "y"){		
		$titleform = $i18n["pc_reply"];
	}

	$imfin = $count;
	$dpl = 'block';

	//if error in captcha, or..., show entries write in form
	if ($mi_array['q_count'] == $count){
		$mi_arrayq = $mi_array;
		$dpl ='block';
	} else {
		$mi_arrayq = $mi_arrayp;
	}

	//value of text email   
	if ($mi_array['email'] ==''){
		// $mi_arrayq['email'] = i18n_r('pages_comments/em_text');
	}

	//control uri 
	$request_uri = getenv ("REQUEST_URI");      
	$posmiar= strpos($request_uri, 'miarr=');
	if ($posmiar){
		$request_uri = substr($request_uri, 0, $posmiar - 1);	 
	}  
	$mGSPLUGINPATH = str_replace("\\", "/", $GSPLUGINPATH);
	$mGSPLUGINPATH = substr($mGSPLUGINPATH, 0, -1);

	//charge emoticons
	$s_emot = news_emot("img","form".$imfin);

	//titleform
	echo '<span class="mh3">'.$titleform.'</span>';

	//cancel form style="display: none;"
	echo '<a id="forma'.$imfin.'" href="javascript:cancelForm(&quot;formid'.$imfin.'&quot;)">Cancel reply</a>'."\n";
  
	//Show Form
	echo '<form class="formsp" style="display:'.$dpl.';" name="formulario" id="form'.$imfin.'" action="'.$siteurl.'plugins/pages_comments/check.php?tp=guest&amp;ans='.$nn.'&amp;idf='.$at.$back.'" method="post">';
		echo '<div class="form">';
			include ("templateform/pcform.php");
			echo '<div style="visibility: hidden;">';
				echo '<input type="hidden" name="guest[q_email]" value="'.$email.'">';
				echo '<input type="hidden" name="guest[q_file]" value="'.$logfile.'">';
				echo '<input type="hidden" name="guest[q_uri]" value="'.$request_uri.'">';
				echo '<input type="hidden" name="guest[q_count]" value="'.$count.'">';
				echo '<input type="hidden" name="guest[q_lang]" value="'.$LANG.'">';
				echo '<input type="hidden" name="guest[q_capt]" value="'.$capt.'">';
				echo '<input type="hidden" name="guest[q_moder]" value="'.$moder.'">';
				echo '<input type="hidden" name="guest[q_phpmailer]" value="'.$phpmailer.'">';
				echo '<input type="hidden" name="guest[q_nwords]" value="'. $pcomnwords.'">';
				//if (!isset($_SESSION)){session_start(); }
				$_SESSION["pc_token".$count]  = sha1(microtime() + 'cumbe20122013');
				echo '<input type="hidden" name="guest[q_token'.$count.']" value="'.$_SESSION["pc_token".$count].'">';
			echo '</div>';
		echo '</div>';
	echo '</form>';

function news_emot($opcion, $id){
  global $siteurl;
  $ruta = './images/img_emot/';
  $array_emot= array();
  $s_emot = '';
  if (is_dir($ruta)) {
      if ($dh = opendir($ruta)){
         while (($file = readdir($dh)) !== false){
         	if($file!="." AND $file!=".." AND $file!=".htaccess" AND is_dir($ruta . $file)== false AND strtolower(substr($file,-4))=='.gif'){
			$array_emot[substr($file, 0, strlen($file)-4)] = '['.substr($file, 0, strlen($file)-4).']';
                        $s_emot = $s_emot.' <a href="javascript:Smile(&quot;'.$id.'&quot;,&quot;['.substr($file, 0, strlen($file)-4).']&quot;)"><img src="'.$siteurl.'plugins/pages_comments/images/img_emot/'.substr($file, 0, strlen($file)-4).'.gif" alt="'.substr($file, 0, strlen($file)-4).'" /></a>';
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

?>
