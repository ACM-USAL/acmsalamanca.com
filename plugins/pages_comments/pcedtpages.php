<?php
	$page_file= @$_GET['pc_url'];
	$file_com= substr($page_file,0,-4);
	$pcm_file= GSDATAOTHERPATH.'pages_comments/pc_manager.xml';
	if (file_exists(GSDATAPAGESPATH.$page_file) && substr($page_file, -4) != '.NMG') {
		$data = getXML(GSDATAPAGESPATH.$page_file);
		$title = stripslashes($data->title);
		$pubdate = $data->pubDate;
		$pubdateseg = strftime('%s', strtotime($data->pubDate));
	} else {
		if (substr($page_file, -4) == '.NMG'){
			//search data  in posts.xml of NM
			$domDocument = new DomDocument();
			$domDocument->load( GSDATAOTHERPATH.'news_manager/posts.xml');	
			//DOMXPath to filter
			$xpath = new DOMXPath($domDocument);
			$verN = $xpath->query('item[slug="'.$file_com.'"]');
			$title = $verN->item(0)->getElementsByTagName( "title" )->item(0)->nodeValue;
			$title = stripslashes($title).'</span><span style="color:#777777;"> ('.i18n_r('pages_comments/pc_postsNM').')';
			$pubdate = $verN->item(0)->getElementsByTagName( "date" )->item(0)->nodeValue;
			$pubdateseg = strftime('%s', strtotime($pubdate));
		}
	}
	if (isset($_POST['edt-submit'])){
		$edt = $_POST['q_edt'];
		//UPDATE WITH NEWS DATA
		$num=0;
		$n=0;
		//check if entry exists
		$domDocument = new DomDocument();
		$domDocument->load($pcm_file);	
		//DOMXPath to filter
		$xpath = new DOMXPath($domDocument);

		if ($num == 0){
			$verN = $xpath->query('page[url="'.$page_file.'"]');			
			$num = $verN->length;	
			if ($num > 0){	
				//check number of index 
				$vdn_num = 0;
				$vnd = $domDocument->getElementsByTagName( "url" );	                        
				foreach  ($vnd as $node){
					if ($node->nodeValue == $page_file){
						$dNd = $vdn_num;
					}
					$vdn_num ++;  
				}
 
				//change value
				$dNdList = $verN->item(0)->getElementsByTagName( "*" );
				$tags_array = '';
				foreach ($dNdList as $node){
					if ($n > 0){
						$node->nodeValue = trim($edt[$node->nodeName]);  //$n 
					}
					$n++;
				}
				$domDocument->save($pcm_file);

			}
			echo '<br /><span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">*** '.i18n_r('pages_comments/pc_chsav').' ***</span>';
		} else {
			echo '<br /><span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_notchsav').'</span>';
			echo '&nbsp; &nbsp;&nbsp;';
			echo '<a href="load.php?id=pages_comments&amp;action=edt_pages&amp;pc_url='.$page_file.'" title="'.i18n_r('pages_comments/pc_edopt').'"><span style="color: #CF3805; font-family: Georgia,Times,Times New Roman,serif; font-size: 17px; font-weight: normal;">'.i18n_r('pages_comments/BACK').'</span></a>';
		}

	} //else {
		echo '<br /><br />';
		echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_edt').' '.i18n_r('pages_comments/pc_pg').': '.$title.'</span><br /><br />';

        //read values of page
		$ndVal = array();
		$domDocument = new DomDocument();
		$domDocument->load($pcm_file);
		$xpath = new DOMXPath($domDocument);		
		$num=0;
		$n=0; 
		$verN = $xpath->query('page[url="'.$page_file.'"]');			
		$num = $verN->length;
		if ($num > 0){		
			$dNdList = $verN->item(0)->getElementsByTagName( "*" );
			foreach ($dNdList as $node){
				if ($node->nodeName == 'tag'){
					$ndVal[$node->nodeName] = '';
					foreach ($node->childNodes as $tagch){ 			
						$ndVal[$node->nodeName] .= $tagch->nodeValue.', ';
					}
					$ndVal[$node->nodeName] = substr($ndVal[$node->nodeName],0, -2);
				} else {
				$ndVal[$node->nodeName] = $node->nodeValue;
				}  
			}

		}

?>
    <script type="text/javascript">
    <!--
       function ncselect(id,texto){
              var frm=document.getElementById(id);
              frm.value = texto;
        }
   -->
   </script>
<?php
		echo '<form style="" name="formulario" id="formedt" action="load.php?id=pages_comments&action=edt_pages&pc_url='.$page_file.'" method="post">';
		echo '<input type="hidden" name="q_sv" value="YES">';
		//url of page 0  (filename)           
		echo '<b>'.i18n_r('pages_comments/pc_fl').':</b> '.$ndVal['url'].'<br /><br />'."\n";

		//Comments (Y or N) 1
		echo '<div style="float: left; width: 20%;">'."\n";
			echo '<b>'.i18n_r('pages_comments/pc_comc').':</b> '."\n";
			echo '<ul style=" margin-top: 5px;">'."\n";
				if ($ndVal['com']=='Y'){
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[com]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[com]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				} else {
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[com]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[com]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				}
			echo '</ul>'."\n";
		echo '</div>'."\n"; 
 
		//Publics Comments (Y or N) 1
		echo '<div style="float: left; width: 25%;">'."\n";
			echo '<b>'.i18n_r('pages_comments/pc_publish').':</b> '."\n";
			echo '<ul style=" margin-top: 5px;">'."\n";
				if ($ndVal['pcom_public']=='Y'){
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_public]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_public]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				} else {
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_public]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_public]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				}
			echo '</ul>'."\n";
		echo '</div>'."\n"; 

		//allow replies
		echo '<div style="float: left; width: 25%;">'."\n";
			echo '<label>'.i18n_r('pages_comments/pc_allowrep').'</label>'."\n";
			echo '<ul>'."\n";
				if ($ndVal['pcom_reply']=='Y') {
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				} else {
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				}
			echo '</ul>'."\n";
		echo '</div>'."\n"; 

		//Replies: Hidden or No
		echo '<div style="float: left; width: 30%;">'."\n";
			echo '<label>'.i18n_r('pages_comments/pc_replies_hd').'</label>'."\n";
			echo '<ul>'."\n";
				if ($ndVal['pcom_reply_hd']=='Y') {
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply_hd]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply_hd]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				} else {
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply_hd]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
					echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[pcom_reply_hd]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
				}
			echo '</ul>'."\n";
		echo '</div>'."\n"; 

		echo '<div style="clear: left;"></div>'."\n";

           //emoticons (Y or N) 2
           echo '<div style="float: left; width: 20%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_emotic').':</label>'."\n";
	           echo '<ul>'."\n";
        	   if ($ndVal['emot']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[emot]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[emot]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[emot]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[emot]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   }
        	   echo '</ul>'."\n";
           echo '</div>'."\n";

           //captcha (Y or N) 3
           echo '<div style="float: left; width: 25%;">'."\n";
	           echo '<label>'.i18n_r('pages_comments/pc_cpt').':</label>'."\n";
	           echo '<ul style=" margin-top: 5px;">'."\n";
	           if ($ndVal['capt']=='Y') {
	               echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[capt]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[capt]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
	           } else {
	               echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[capt]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[capt]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
	           }
	           echo '</ul>'."\n";
           echo '</div>'."\n";

           //order ascending, descendind 5
           echo '<div style="float: left; width: 25%;">'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_ord').':</label>'."\n";
	        echo '<ul style=" margin-top: 5px;">'."\n";
          	if ($ndVal['ord']=='D') {
               		echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[ord]" value="D" checked>&nbsp;&nbsp'.i18n_r('pages_comments/dcrs').'</li>'."\n";
	       		echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[ord]" value="A">&nbsp;&nbsp;'.i18n_r('pages_comments/icrs').'</li>'."\n";
	         } else {
        	        echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[ord]" value="D">&nbsp;&nbsp'.i18n_r('pages_comments/dcrs').'</li>'."\n";
	       		echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[ord]" value="A" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/icrs').'</li>'."\n";
	         }
        	 echo '</ul>'."\n";
           echo '</div>'."\n";

           //Moderated comments 9
           echo '<div style="float: left; width: 30%;">'."\n";
        	   echo '<label>'.i18n_r('pages_comments/pc_cmoder').':</label>'."\n";
        	   echo '<ul style=" margin-top: 5px;">'."\n";
        	   if ($ndVal['moder']=='Y') {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[moder]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[moder]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
        	   } else {
        	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[moder]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
		       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[moder]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
   	           }
	           echo '</ul>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

	   //Number of comments for page (pagination)
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_ncommc').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; width: 88%; text-align: left;" type="text" name="q_edt[npag]" value="'.$ndVal['npag'].'">';
           	echo '</p>'."\n";
           echo '</div>'."\n";
	   //Number of characters by comment
           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_numword').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_edt[pcom_nwords]" value="'.$ndVal['pcom_nwords'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

//Black list
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_blackls').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_edt[pcom_blacklist]" value="N"';
                   if ($ndVal['pcom_blacklist'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_edt[pcom_blacklist]" value="Y"';
                   if ($ndVal['pcom_blacklist'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_blackls_w').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_edt[pcom_words_bl]" value="'.$ndVal['pcom_words_bl'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";
//Rating
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_rating').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_edt[pcom_rating]" value="N"';
                   if ($ndVal['pcom_rating'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_edt[pcom_rating]" value="Y"';
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

           	echo '<SELECT id="rating_tp" NAME="q_edt[pcom_rating_tp]" style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; text-align: left; padding-right: 1px; width: 45%;" onChange="javascript:img_rating(&quot;'.$SITEURL.'/plugins/pages_comments/images/rating/&quot;)" >'."\n";
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
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_edt[pcom_social]" value="N"';
                   if ($ndVal['pcom_social'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_edt[pcom_social]" value="Y"';
                   if ($ndVal['pcom_social'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_socialmed_tp').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_edt[pcom_social_tp]" value="'.$ndVal['pcom_social_tp'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

//Report inappropriate comments
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_inappro').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_edt[pcom_report_ina]" value="N"';
                   if ($ndVal['pcom_report_ina'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_edt[pcom_report_ina]" value="Y"';
                   if ($ndVal['pcom_report_ina'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="float: left; width: 60%;">'."\n"; 
           	echo '<p>'."\n";
	        echo '<label>'.i18n_r('pages_comments/pc_inappro_tp').':</label>'."\n".'<INPUT style="border: 1px solid #AAAAAA; border-radius: 2px 2px 2px 2px; margin: 0 0 0 3px; text-align: left; width: 88%;" type="text" name="q_edt[pcom_report_lab]" value="'.$ndVal['pcom_report_lab'].'">'."\n";
           	echo '</p>'."\n";
	   echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

//Notify if update comment
           echo '<div style="float: left; width: 40%;">'."\n";
           	echo '<p>'."\n";
           	echo '<label>'.i18n_r('pages_comments/pc_notify_upd').':</label>'."\n";
		echo '<INPUT style="margin-left: 30px; vertical-align: middle;" type="radio" name="q_edt[pcom_notify]" value="N"';
                   if ($ndVal['pcom_notify'] != 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/NO');
	 	echo '<span style="margin-left: 30px;">&nbsp;</span>';
		echo '<INPUT style="vertical-align: middle;" type="radio" name="q_edt[pcom_notify]" value="Y"';
                   if ($ndVal['pcom_notify'] == 'Y') {
                       echo 'CHECKED';
                   }
                echo ' />'.i18n_r('pages_comments/YES');
           	echo '</p>'."\n";
           echo '</div>'."\n";

           echo '<div style="clear: left;"></div>'."\n";

           echo '<div style="border: 1px solid #C8C8C8; height: 5px; width:100%;"><p></p></div>';

           //Front end user plugin 11

           echo '<div style="float: left; padding-top: 7px; width: 35%;">'."\n";
           		echo '<label>'.i18n_r('pages_comments/pc_wuser').':</label>'."\n";
	        		echo '<ul style=" margin-top: 5px;">'."\n";
           			if ($ndVal['nmusr']=='Y') {
               				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[nmusr]" value="Y" checked>&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
	       				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[nmusr]" value="N">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
           			} else {
               				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[nmusr]" value="Y">&nbsp;&nbsp'.i18n_r('pages_comments/YES').'</li>'."\n";
	       				echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_edt[nmusr]" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>'."\n";
           			}
           			echo '</ul>'."\n";
	   echo '</div>'."\n"; 

           echo '<div style="clear: left;"></div>'."\n";

           //Title To form
           echo '<div style="margin-bottom: 10px;">';
           echo '<b>'.i18n_r('pages_comments/pc_titletoform').'</b>: <INPUT style="width: 400px; text-align: left; padding-left: 3px;" type="text" name="q_edt[titleform]" value="'.$ndVal['titleform'].'">';
           echo '</div>';

           echo '<input type="hidden" name="q_edt[pubdate]" value="'.$pubdate.'">';  //$pubdate 15
           echo '<input type="hidden" name="q_edt[pubdateseg]" value="'.$pubdateseg.'">';  //$pubdate 15

 	   echo '<br /><br /><input type="submit" style="margin-left: 7px; width: 60px;" value="'.i18n_r('pages_comments/Save').'" id="editform" name="edt-submit" />';

        echo '</form>';
  // }

?>
