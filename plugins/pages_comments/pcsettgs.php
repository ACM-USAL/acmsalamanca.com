<?php
  $page_file= @$_GET['pc_url'];
  global $SITEURL;
  $ruta = GSDATAPAGESPATH;
  $pcm_file= GSDATAOTHERPATH.'pages_comments/pc_settgs.xml';
  $num = 0;
  $filex =0;

  $ndVal = array();

  //check if file pc_settgs exits; if don't save by default

  if ( !file_exists($pcm_file) ) {
       $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><entry></entry>');
       $thislog = $xml->addChild('sett');
            $cdata = $thislog->addChild('nclang','en_US');   //ndVal=0 nc language
            $cdata = $thislog->addChild('ncemail',' ');  //ndVal=1 email where send notify
            $cdata = $thislog->addChild('ncffpost', '1-%d.%m.%Y %T'); //ndVal=2 format date for post or comments
            $cdata = $thislog->addChild('phpmailer','N');  //ndVal=3 class phpmailer
            $cdata = $thislog->addChild('emot','Y');   //ndVal=4 emoticons Yes
            $cdata = $thislog->addChild('capt','Y');   //ndVal=5 captcha Yes
            $cdata = $thislog->addChild('ord','D');    //ndVal=6  order 'D':Descending or 'A':Ascending
            $cdata = $thislog->addChild('moder','N');   //ndVal=7 comments moderateds
            $cdata = $thislog->addChild('npag','4');   //ndVal=8 number comments by page
            $cdata = $thislog->addChild('nmusr','N');   //ndVal=9 works with front end user
            $cdata = $thislog->addChild('pcuserdel','N');  //ndVal=10 del comments by user
            $cdata = $thislog->addChild('ncom_rctpost','20');  //ndVal=11 number of comments for recent posts, by default 20
            $cdata = $thislog->addChild('pcom_public','Y');  //ndVal=12 public comments, by default Yes
            $cdata = $thislog->addChild('pcom_reply','Y');  //ndVal=13 allow replies, by default Yes 
            $cdata = $thislog->addChild('pcom_reply_hd','Y');  //ndVal=14 Replies hidden, by default Yes 
            $cdata = $thislog->addChild('pcom_nwords','0');  //ndVal=15 nº of words for comments (0 no limits)
            $cdata = $thislog->addChild('pcom_blacklist','N');  //ndVal=16 blacklist, by default No  
            $cdata = $thislog->addChild('pcom_words_bl',' ');  //ndVal=17 words separated by comma of blacklist
            $cdata = $thislog->addChild('pcom_rating','N');  //ndVal=18 comments ratings, by default No
            $cdata = $thislog->addChild('pcom_rating_tp','hand1');  //19 type of comments ratings, by default: hand1
            $cdata = $thislog->addChild('pcom_social','N');  //ndVal=20 support social media, by default N
            $cdata = $thislog->addChild('pcom_social_tp',' ');  //ndVal=21 type of social media separated by comma
            $cdata = $thislog->addChild('pcom_report_ina','N');  //ndVal=22 report inappropriate comment, by default N
            $cdata = $thislog->addChild('pcom_report_lab',' ');  //ndVal=23 label for inappropriate comment
            $cdata = $thislog->addChild('pcom_notify','N');  //ndVal=24 get notification if update, by default N
            $cdata = $thislog->addChild('integ_NM','N');  //ndVal=25 to integrate with News Manager
            $cdata = $thislog->addChild('page_NM',' ');  //ndVal=26 page of News Manager
            $cdata = $thislog->addChild('integ_GS','N');  //ndVal=27 to integrate with GS
            $cdata = $thislog->addChild('page_GS',' ');  //ndVal=28 page of GS
            XMLsave($xml, GSDATAOTHERPATH.'pages_comments/pc_settgs.xml');            

  }

   //call data of settings
		$domDocument = new DomDocument();
		$domDocument->load($pcm_file);

		//filter with DOMXPath
		$xpath = new DOMXPath($domDocument);
		$filex = 1;
		$num=0;		$verN = $xpath->query('sett');			
		$num = $verN->length;
		if ($num > 0){	
			$dNdList = $verN->item(0)->getElementsByTagName( "*" );
			foreach ($dNdList as $node){
				$ndVal[$node->nodeName]= $node->nodeValue;
			}
		}



    echo '<br /><h3 style="font-size: 16px;">'.strtoupper(i18n_r('pages_comments/pc_settl')).'</h3>'."\n";


////////////////////////////////////////
//      Edit SETTINGS                 //
////////////////////////////////////////
	if (@$_GET['sett']== 'y' and isset($_POST['sett-submit'])) {
		$ndVal = $_POST['q_sett'];
		if ($num > 0){	
			foreach ($dNdList as $node){				$node->nodeValue = trim($ndVal[$node->nodeName]);
			}
			$domDocument->save($pcm_file);
			if ($ndVal['ncemail'] == '') {
?>
				<script type="text/javascript">			
					<!--
						alert ("<?php echo i18n_r('pages_comments/pc_emptyemail'); ?>");
					-->
				</script>
<?php
			}
			echo '<span style="float: right; text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_chsav').'</span><br />'."\n";
		}
	}


  //select of lang
  $rutalang = GSPLUGINPATH.'pages_comments/lang/';
  $option = '';
  if (is_dir($rutalang)) {
      if ($dh = opendir($rutalang)){
         while (($file = readdir($dh)) !== false){
         	if($file!="." AND $file!=".." AND $file!=".htaccess" AND is_dir($ruta . $file)== false AND strtolower(substr($file,-4))=='.php'){
                    if (substr($file,0,-4) == $ndVal['nclang']){
                         $option.= '<OPTION VALUE="'.substr($file,0,-4).'" SELECTED>'.substr($file,0,-4).'</OPTION>'."\n";
                    } else {
                         $option.= '<OPTION VALUE="'.substr($file,0,-4).'">'.substr($file,0,-4).'</OPTION>'."\n";
                    }
                }
         }
         closedir($dh);
      }
  }


  //select with 5 date formats:
  //'%d.%m.%Y %T'= 29.11.2011 22:41:49
  //lngDate = %B %dth, %Y - %R %p = November 12th, 2011 - 12:32 AM 
  //%a, %d %b %Y %T =Tue, 29 Nov 2011 22:41:49
  //shtDate = %b $d, %Y= Nov 29, 2011
  //'%d.%m.%Y'= dd.mm.yyyy 
  $dt= time();
  $optiondt='';
  $optiondt1='';

  $fmdt[1]= "%d.%m.%Y %T";
  $fmdt[2]= "%B %dth, %Y - %R %p";
  $fmdt[3]= "%a, %d %b %Y %T";
  $fmdt[4]= "%b %d, %Y";
  $fmdt[5]= "%d.%m.%Y";

  for ($q=1; $q<=5; $q++){
      if ($q == substr($ndVal['ncffpost'],0,1)){ 
          $optiondt.='<OPTION VALUE="'.$q.'-'.$fmdt[$q].'" SELECTED>'.strftime($fmdt[$q], $dt).'</OPTION>'."\n";
      } else {
          $optiondt.='<OPTION VALUE="'.$q.'-'.$fmdt[$q].'">'.strftime($fmdt[$q], $dt).'</OPTION>'."\n";
      }
  }

 //SELECT of Notification by email
  $optionemailnotif = '';
  $notif[1] = "st";
  $notif[2] = "mailer";
  $notif[3] = "N";
  $trans_notif[1]= i18n_r('pages_comments/pc_mailst');
  $trans_notif[2]= i18n_r('pages_comments/pc_phpmailer');
  $trans_notif[3]= i18n_r('pages_comments/pc_nonotifmail');
  for ($q=1; $q<=3; $q++){
      if ($q == substr($ndVal['phpmailer'],0,1)){ 
          $optionemailnotif.='<OPTION VALUE="'.$q.'-'.$notif[$q].'" SELECTED>'.$trans_notif[$q].'</OPTION>'."\n";
      } else {
          $optionemailnotif.='<OPTION VALUE="'.$q.'-'.$notif[$q].'">'.$trans_notif[$q].'</OPTION>'."\n";
      }
  }

//////////////////////////
//   FORM of SETTINGs
//////////////////////////
    echo '<form name="formulario" action="load.php?id=pages_comments&amp;action=pc_setting&amp;sett=y" method="post">'."\n";

           echo '<div style="float: left; width: 50%;">'."\n";
           	echo '<p><label>'.i18n_r('pages_comments/pc_lang').'</label>'."\n";
	        echo '<SELECT NAME="q_sett[nclang]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 90%;">'."\n";
        	        echo $option;
        	echo '</SELECT></p>'."\n";
	   echo '</div>'."\n";        
           echo '<div style="float: left; width: 50%;">'."\n";
	        echo '<p><label>'.i18n_r('pages_comments/Em').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; width: 88%; text-align: left;" type="text" name="q_sett[ncemail]" value="'.$ndVal['ncemail'].'"></p>'."\n";
	   echo '</div>'."\n";

	   echo '<div style="clear: left;"></div>'."\n";

           echo '<div style="float: left; width: 50%;">'."\n";
           	echo '<p><label>'.i18n_r('pages_comments/pc_ffpost').':</label>'."\n";
           	echo '<SELECT NAME="q_sett[ncffpost]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 90%;">'."\n";
                	echo $optiondt;
           	echo '</SELECT></p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="float: left; width: 50%;">'."\n";
           	echo '<p><label>'.i18n_r('pages_comments/pc_notifmail').':</label>'."\n";
           	echo '<SELECT NAME="q_sett[phpmailer]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 90%;">'."\n";
                	echo $optionemailnotif;
           	echo '</SELECT></p>'."\n";
	   echo '</div>'."\n";

	   echo '<div style="clear: left;"></div>'."\n";

           echo '<div style="float: left; width: 50%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_comments_rct').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_sett[ncom_rctpost]" value="'.$ndVal['ncom_rctpost'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

	   echo '<div style="clear: left;"></div>'."\n";

//DEFAULT VALUES FOR COMMENTS
           echo '<div style="margin: 15px 0; width: 100%; height: auto; border: 1px solid rgb(200, 200, 200); text-align: center; font-family: georgia; font-size: 14px; padding: 5px 0pt; text-transform: uppercase;">'.i18n_r('pages_comments/pc_default').'</div>'."\n";
           //public comments
           echo '<div style="float: left; width: 33%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_publish').'</label>'."\n";
	           echo '<ul>'."\n";
        	   if ($ndVal['pcom_public']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_public]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_public]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_public]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_public]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   }
        	   echo '</ul>'."\n";
	   echo '</div>'."\n";       
           //allow replies  
           echo '<div style="float: left; width: 33%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_allowrep').'</label>'."\n";
	           echo '<ul>'."\n";
        	   if ($ndVal['pcom_reply']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   }
        	   echo '</ul>'."\n";
	   echo '</div>'."\n";   

           //Replies: Hidden or No
           echo '<div style="float: left; width: 33%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_replies_hd').'</label>'."\n";
	           echo '<ul>'."\n";
        	   if ($ndVal['pcom_reply_hd']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply_hd]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply_hd]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply_hd]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcom_reply_hd]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   }
        	   echo '</ul>'."\n";
	   echo '</div>'."\n"; 

	   echo '<div style="clear: left;"></div>'."\n";

           //emoticons
           echo '<div style="float: left; width: 25%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_emotic').':</label>'."\n";
	           echo '<ul>'."\n";
        	   if ($ndVal['emot']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[emot]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[emot]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[emot]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[emot]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   }
        	   echo '</ul>'."\n";
           echo '</div>'."\n";
           
	   //captcha
           echo '<div style="float: left; width: 25%;">'."\n";
	           echo '<label>'.i18n_r('pages_comments/pc_cpt').':</label>'."\n";
	           echo '<ul style=" margin-top: 5px;">'."\n";
	           if ($ndVal['capt']=='Y') {
	               echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[capt]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[capt]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
	           } else {
	               echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[capt]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[capt]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
	           }
	           echo '</ul>'."\n";
           echo '</div>'."\n";
	
	   //order: ascending or descending
           echo '<div style="float: left; width: 25%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_ord').':</label>'."\n";
	        echo '<ul style=" margin-top: 5px;">'."\n";
          	if ($ndVal['ord']=='D') {
               		echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[ord]" value="D" checked>&nbsp;&nbsp'.i18n_r('pages_comments/dcrs').'</li>'."\n";
	       		echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[ord]" value="A">&nbsp;&nbsp;'.i18n_r('pages_comments/icrs').'</li>'."\n";
	         } else {
        	        echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[ord]" value="D">&nbsp;&nbsp'.i18n_r('pages_comments/dcrs').'</li>'."\n";
	       		echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[ord]" value="A" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/icrs').'</li>'."\n";
	         }
        	 echo '</ul>'."\n";
           echo '</div>'."\n";

	   //comments moderates
           echo '<div style="float: left; width: 25%;">'."\n";
        	   echo '<label>'.i18n_r('pages_comments/pc_cmoder').':</label>'."\n";
        	   echo '<ul style=" margin-top: 5px;">'."\n";
        	   if ($ndVal['moder']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[moder]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[moder]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[moder]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[moder]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
   	           }
	           echo '</ul>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";
	   //Number of comments for page (pagination)
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_ncommc').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; width: 88%; text-align: left;" type="text" name="q_sett[npag]" value="'.$ndVal['npag'].'">';
           	echo '</p>'."\n";
           echo '</div>'."\n";
	   //Number of characters by comment
           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_numword').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_sett[pcom_nwords]" value="'.$ndVal['pcom_nwords'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";
//Black list
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_blackls').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[pcom_blacklist]" value="N"';
                   if ($ndVal['pcom_blacklist'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[pcom_blacklist]" value="Y"';
                   if ($ndVal['pcom_blacklist'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_blackls_w').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_sett[pcom_words_bl]" value="'.$ndVal['pcom_words_bl'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";
//Rating
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_rating').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[pcom_rating]" value="N"';
                   if ($ndVal['pcom_rating'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[pcom_rating]" value="Y"';
                   if ($ndVal['pcom_rating'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

	   //select of img rating
?>

	<script type="text/javascript">
<!--
		function img_rating(rtimg){
			var select = document.getElementById('rating_tp');
			rtimg = rtimg + select.options[select.selectedIndex].value
			document.getElementById('rating_up').src = rtimg + "_up.png";
			document.getElementById('rating_down').src = rtimg + "_down.png";
		}
-->
	</script>

<?php

           echo '<div style="float: left; width: 45%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_rating_tp').':</label>'."\n";
	   	$ratingimg_folder = GSPLUGINPATH.'pages_comments/images/rating';
		$option = '';
		$ratingimg = array();
	   	if (is_dir($ratingimg_folder)) {
			if ($dh = opendir($ratingimg_folder)){
				while (($file = readdir($dh)) !== false){
					if($file != "." AND $file != ".." AND $file != ".htaccess" AND !is_dir($ratingimg_folder.'/'.$file) AND !array_key_exists(substr($file,0,strpos($file,"_")), $ratingimg)){
						$ratingimg[substr($file,0,strpos($file,"_"))] = substr($file,0,strpos($file,"_")) ;
					}
				}
				closedir($dh);
			}
	   	}
		$img_up = 'hand1_up.png';
		$img_down = 'hand1_down.png';
		sort($ratingimg);
		foreach($ratingimg as $key=>$value){	
			if ($value == $ndVal['pcom_rating_tp']){
				$option.= '<OPTION VALUE="'.$value.'" SELECTED>'.$value.'</OPTION>'."\n";
				$img_up = $value.'_up.png';
				$img_down = $value.'_down.png';
			} else {
				$option.= '<OPTION VALUE="'.$value.'">'.$value.'</OPTION>'."\n";
			}
		}

           	echo '<SELECT id="rating_tp" NAME="q_sett[pcom_rating_tp]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 45%;" onChange="javascript:img_rating(&quot;'.$SITEURL.'/plugins/pages_comments/images/rating/&quot;)" >'."\n";
                	echo $option;
           	echo '</SELECT>'."\n";
		echo '<img style="margin-left: 5px;" id="rating_up" src="'.$SITEURL.'/plugins/pages_comments/images/rating/'.$img_up.'" />';
		echo '<img style="margin-left: 5px;" id="rating_down" src="'.$SITEURL.'/plugins/pages_comments/images/rating/'.$img_down.'" /> ';
		echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

//Social Media
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_socialmed').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[pcom_social]" value="N"';
                   if ($ndVal['pcom_social'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[pcom_social]" value="Y"';
                   if ($ndVal['pcom_social'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_socialmed_tp').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_sett[pcom_social_tp]" value="'.$ndVal['pcom_social_tp'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

//Report inappropriate comments
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_inappro').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[pcom_report_ina]" value="N"';
                   if ($ndVal['pcom_report_ina'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[pcom_report_ina]" value="Y"';
                   if ($ndVal['pcom_report_ina'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_inappro_tp').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_sett[pcom_report_lab]" value="'.$ndVal['pcom_report_lab'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

//Notify if update comment
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_notify_upd').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[pcom_notify]" value="N"';
                   if ($ndVal['pcom_notify'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[pcom_notify]" value="Y"';
                   if ($ndVal['pcom_notify'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

           echo '<div style="border: 1px solid #C8C8C8; height: 5px; width:100%;"><p></p></div>';

//WORK WITH FRONT END USER PLUGIN
           echo '<div><label style="border-style: none none solid; border-width: 1px; border-color: #AAAAAA; padding-bottom: 3px; margin-bottom: 3px; padding-top: 7px;">FRONTEND USER PLUGIN</label>'."\n";
           	echo '<div style="float: left; width: 35%;">'."\n";
           		echo '<label>'.i18n_r('pages_comments/pc_wuser').':</label>'."\n";
	        		echo '<ul style=" margin-top: 5px;">'."\n";
           			if ($ndVal['nmusr']=='Y') {
               				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[nmusr]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
	       				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[nmusr]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
           			} else {
               				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[nmusr]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
	       				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[nmusr]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
           			}
           			echo '</ul>'."\n";
		echo '</div>'."\n"; 

           	echo '<div style="float: left; width: 65%;">'."\n";
           		echo '<label>'.i18n_r('pages_comments/pc_userdel').':</label>'."\n";
           			echo '<ul style=" margin-top: 5px;">'."\n";
           			if ($ndVal['pcuserdel']=='Y') {
           			    echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcuserdel]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
	   			    echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcuserdel]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
           			} else {
           			    echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcuserdel]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
	   			    echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_sett[pcuserdel]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
           			}
           			echo '</ul>'."\n";
           	echo '</div>'."\n"; 
                echo '<div style="clear: left;"></div>'."\n";
           echo '</div>'."\n";

//INTEGRATE WITH BLOG's PLUGIN
           echo '<div style="margin: 15px 0; width: 100%; height: auto; border: 1px solid rgb(200, 200, 200); text-align: center; font-family: georgia; font-size: 14px; padding: 5px 0pt; text-transform: uppercase;">'.i18n_r('pages_comments/pc_integrate').'</div>'."\n";

           echo '<div style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; padding: 5px 0 0 5px;"><label>NEWS MANAGER PLUGIN</label>'."\n";
           	echo '<div style="float: left; width: 35%;">'."\n";
           		echo '<label>'.i18n_r('pages_comments/pc_integrNM').':</label>'."\n";
			echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[integ_NM]" value="0"';
                   	if ($ndVal['integ_NM'] != '1') {
                       		echo 'CHECKED';
                   	}
               		echo ' />'.i18n_r('pages_comments/NO');
	 		echo '<span style="margin-left: 30px;">&nbsp;</span>';
			echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[integ_NM]" value="1"';
                   	if ($ndVal['integ_NM'] == '1') {
                       		echo 'CHECKED';
                   	}
                	echo ' />'.i18n_r('pages_comments/YES');
           		echo '</p>'."\n";
		echo '</div>'."\n"; 

           	echo '<div style="float: left; width: 65%;">'."\n";
           		echo '<p>';
	           	echo '<label>'.i18n_r('pages_comments/pc_integrNM_pg').':</label>'."\n";
           		echo '<SELECT NAME="q_sett[page_NM]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 90%;">'."\n";
                        	$pages_nm = $ndVal['page_NM'];
                        	$pages = get_available_pages();
                       		foreach ($pages as $page) {
                             		if ($pages_nm == $page['slug']) {
          				  echo '<option value="'.$page['slug'].'" SELECTED>'.$page['slug'].'</option>'."\n";
		             		} else {
			        	  echo '<option value="'.$page['slug'].'">'.$page['slug'].'</option>'."\n";
      			     		}
 				}
	           	echo '</SELECT></p>'."\n";
           	echo '</div>'."\n"; 
                echo '<div style="clear: left;"></div>'."\n";
           echo '</div>'."\n";  

           echo '<div style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; padding: 5px 0 0 5px;"><label>GS BLOG PLUGIN</label>'."\n";
           	echo '<div style="float: left; width: 35%;">'."\n";
           		echo '<label>'.i18n_r('pages_comments/pc_integrGS').':</label>'."\n";
			echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_sett[integ_GS]" value="0"';
                   	if ($ndVal['integ_GS'] != '1') {
                       		echo 'CHECKED';
                   	}
                	echo ' />'.i18n_r('pages_comments/NO');
	 		echo '<span style="margin-left: 30px;">&nbsp;</span>';
			echo '<INPUT style="vertical-align: middle;" type="radio" name="q_sett[integ_GS]" value="1"';
                   	if ($ndVal['integ_GS'] == '1') {
                       		echo 'CHECKED';
                   	}
                	echo ' />'.i18n_r('pages_comments/YES');
           		echo '</p>'."\n";
		echo '</div>'; 

           	echo '<div style="float: left; width: 65%;">'."\n";
           		echo '<label>'.i18n_r('pages_comments/pc_integrGS_pg').':</label>'."\n";
           		echo '<SELECT NAME="q_sett[page_GS]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 90%;">'."\n";
                        	$pages_gs = $ndVal['page_GS'];
                        	$pages = get_available_pages();
                       		foreach ($pages as $page) {
                             		if ($pages_gs == $page['slug']) {
          				  echo '<option value="'.$page['slug'].'" SELECTED>'.$page['slug'].'</option>'."\n";
		             		} else {
			        	  echo '<option value="'.$page['slug'].'">'.$page['slug'].'</option>'."\n";
      			     		}
 				}
	           	echo '</SELECT></p>'."\n";
           	echo '</div>'."\n"; 
                echo '<div style="clear: left;"></div>'."\n";
           echo '</div>'."\n"; 



           echo '<br />&nbsp;<input type="submit" style="margin-left: 7px; width: 60px;" value="'.i18n_r('pages_comments/Save').'" id="settform" name="sett-submit" />'."\n";
    echo '</form>'."\n";
    echo '</span>'."\n";  
?>
