<?php

/*
/////////////////////////////////////////////
///  For CHECK, after delete
echo '<b>$_POST[guest]:</b><br />';
print_r($_POST['guest']);
echo '<br />';
echo '<br />';
echo '$capt: '.$capt.'<br />';
echo 'tp: '.$tp.'<br />';
echo '<b>$_SESSION:</b><br />';
print_r($_SESSION);
echo '<br />';
echo '<br />';
echo '$_POST[guest]["q_token".$_POST[guest][q_count]] : '.$_POST['guest']["q_token".$_POST['guest']['q_count']].'<br />';
echo '$_SESSION["pc_token".$_POST[guest][q_count]]: '.$_SESSION["pc_token".$_POST['guest']['q_count']].'<br />';

//
/////////////////////////////////////////////
*/

/*

		    $verNodeU_r = $xpath->query("entry[Id=$at and answ='y']");
		    $dnlLen_r=$verNodeU_r->length;
                    $lindelete = ''; 
		    if ($dnlLen_r ==0){ echo '<br />'; }
			$countr=1;
			foreach($verNodeU_r as $node) {
			   //echo '<div class="tablar">';
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
				 else if ($n == 'moder'){$accept_rp=$d;}	
				 else if ($n == 'id'){}
                                 else if ($n == 'subid'){ $sidel=$d; }
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
*/
?>
