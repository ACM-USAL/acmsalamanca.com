<?php

//-----------------------------------------------------------------//
//--- check.php: action if form is submit                          //
//-----------------------------------------------------------------//

	$err='';
	$temp = $_POST['guest'];
	$temp['comentario'] = $_POST['comentario']; 
	$tp =  $temp['q_tp'];

	if (!isset($_SESSION["imagencadena"]) && $capt =='Y'){
		$mi_array = $temp;
		$tp = 'noenter';
		$seg= 6;
		$msgshw = '*** '.strtoupper(i18n_r('pages_comments/MSG_pcERR')).' ***\n'; 
		$msgshw .= i18n_r('pages_comments/pc_resend'); 
	} elseif ($capt =='Y') {
		$imagenCadena = $_SESSION["imagencadena"]; 
		$pot = trim(strtolower($imagenCadena));
	}
	//check Token  
	if (isset($_POST['guest-submit']) && $tp != 'noenter') {  
		if ($_POST['guest']["q_token".$_POST['guest']['q_count']]  != $_SESSION["pc_token".$_POST['guest']['q_count']]){
			//if doesn't come from form
			$mi_array = $temp;
			$tp = 'noenter2';
			$seg= 6;
			$msgshw = '*** '.strtoupper(i18n_r('pages_comments/MSG_pcERR')).' ***\n';
			$msgshw .= '$_SESSION: '.$_SESSION["pc_token".$_POST['guest']['q_count']].'\n';
			$msgshw .= '$_POST: '.$_POST['guest']["q1_token".$_POST['guest']['q_count']];
		}
	}

//-----------------------------------------------------
//             SAVE DATA OF FORM IN LOG
//-----------------------------------------------------
if ($tp == 'pc') {
	if (isset($_POST['guest-submit'])) {
		$id = $_POST['guest']['q_idf'] ;
		$idf = $id;
		$answ = $_POST['guest']['q_ans'] ;
		$log_file_check= $_POST['guest']['q_file'];
		$posstr= strrpos($log_file_check, "/"); 
		$log_file_red= substr($log_file_check, $posstr+1);    
		$log_path_check = substr($log_file_check,0, $posstr);
		$MICOUNT= $_POST['guest']['q_count'] - 1;
		$MIURL= $_POST['guest']['q_uri'];
		$parent = ($answ == 'n') ? "0" : $id.".".$_POST['guest']['q_sidf'];

		$err1 = '';
		if ($pcomnwords>0 && strlen(trim($_POST['comentario'])) > $pcomnwords ) {
	 		$err1 = i18n_r('pages_comments/MSG_charERR');
			$err = $err1;
		}

		if ($moder == 'Y') {
			$moder = 'N';
		} else {
			$moder = 'Y';
		}

		$err2 = '';
		$captcha = '';
		if ($capt =='Y'){
			if ( $pot == trim(strtolower($_POST['guest']['pot']))) {
				$captcha = strtolower($_POST['guest']['pot']);                
			} else {
				$err2 = i18n_r('pages_comments/MSG_CAPTCHA_FAILED');
				$err = $err2;
			}
		}
		
         $from ='';
		 if ( $_POST['guest']['email'] != '' ) {
			if ($_POST['guest']['email'] != i18n_r('pages_comments/em_text')){
				$from = $_POST['guest']['email'];
			} else {
				$_POST['guest']['email'] = '';
			}

		 }

		 if ( $_POST['guest']['subject'] != '' ) {
                        $subject = $_POST['guest']['subject'];
                 } else {
                        $subject = i18n_r('pages_comments/pc_FORM_SUB').': '.$server_name.$MIURL;
                 } 

       	if ($err == '' && trim($_POST['guest']['nombre']) !='' && trim($_POST['comentario']) !='') {

			$headers = "From: ".$from."\r\n";
			$headers .= "Return-Path: ".$from."\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8 \r\n";

			unset($temp['pot']);
			unset($temp['guest-submit']);
			unset($temp['submit']);
                        
			$body = '';
			$body .= i18n_r('pages_comments/pc_FORM_SUB').' '.i18n_r('pages_comments/WHO').' : <a href="http://'.$server_name.$MIURL.'">'.$server_name.$MIURL.'</a><br />';
			$body .= "-----------------------------------<br /><br />";


				if ( ! file_exists($log_file_check) ) {
					$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
					$sid = 0;
				} else {
					$xmldata = file_get_contents($log_file_check);
				   		$xml = new SimpleXMLExtended($xmldata);
					    //search SubId with xpath
		   		        $domDocument = new DomDocument();
						$domDocument->load($log_file_check);
					    $xpath = new DOMXPath($domDocument);
						//search id when is a new comment
						if ($answ == 'n'){
							$verNode = $xpath->query("entry/Id[../answ='n']");
							$L_vn= $verNode->length;
							if ($L_vn == 0){
								$id = 1;
							} else {
								$id = ($verNode->item($L_vn -1)->nodeValue) + 1;
							}
						}
					    //filter by value of nodo entry/Id= $id
					    //to know value of SubId and add 1 if is necesary.
						if ($answ != 'n'){
							$verNode = $xpath->query("entry/SubId[../Id=$id]");
							$L_vn= $verNode->length;
							if ($L_vn == 0){
								$sid = 0;
							} else {
								$sid = ($verNode->item($L_vn -1)->nodeValue) + 1;
							}
						} else {
							$sid = 0;   
						} 
				}

				//save data
				$thislog = $xml->addChild('entry');
                                $thislog->addAttribute('id', $id);
				$cdata = $thislog->addChild('Id');
				       $cdata->addCData($id);				
				$cdata = $thislog->addChild('SubId');
				       $cdata->addCData($sid);   
				$cdata = $thislog->addChild('parent');
				       $cdata->addCData($parent);   
 				$cdata = $thislog->addChild('Nb');	
				       $cdata->addCData(html_entity_decode($temp['nombre']));
                                $dater = date('r');
                                $dateseg = strftime('%s', strtotime($dater));
				$thislog->addChild('date', $dater);
				$thislog->addChild('dateseg', $dateseg);
        	                $cdata = $thislog->addChild('Em');
				       $cdata->addCData(html_entity_decode($temp['email']));
        	                $cdata = $thislog->addChild('Ct');
				       $cdata->addCData(html_entity_decode($temp['city']));
				$cdata = $thislog->addChild('Subj');
        	                       $cdata->addCData(html_entity_decode($temp['subject']));
				$cdata = $thislog->addChild('Cm');
                                       $comentario= nl2br($temp['comentario']);
        	                       $cdata->addCData(html_entity_decode($comentario));
				$cdata = $thislog->addChild('captcha');
				       $cdata->addCData($captcha);
				$cdata = $thislog->addChild('ip_address');
				$ip = getenv("REMOTE_ADDR");
				       $cdata->addCData(htmlentities($ip));
				$cdata = $thislog->addChild('answ');
				       $cdata->addCData($answ);
				$cdata = $thislog->addChild('moder');
				       $cdata->addCData($moder);
				$cdata = $thislog->addChild('pcrating');
				       $cdata->addCData('0');
	
				foreach ( $temp as $key => $value ) {
					if (substr($key, 0, 2) != 'q_') {
						$body .= ucfirst(i18n_r('pages_comments/'.$key)) .": ". $value ."<br />";
					}
				}
				
				XMLsave($xml, $log_file);

			//save data en log for last recents comments  
			if ( ! file_exists($log_path.'/pc_lastcom.xml') ) {
               			$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
			} else  {
				$xmldata = file_get_contents($log_path_check.'/pc_lastcom.xml');
		       		$xml = new SimpleXMLExtended($xmldata);
                        }                            
				$thislog = $xml->addChild('entry');   
 				$cdata = $thislog->addChild('filelog');	
				       $cdata->addCData($log_file_red);
				$thislog->addChild('dateseg', $dateseg);
                        
				XMLsave($xml, $log_path_check.'/pc_lastcom.xml');


				//send notication by email

				if ($phpmailer == '1-st') {
					$result = mail($EMAIL,$subject,$body,$headers);
				} else if ($phpmailer == '2-mailer') {
					if (is_dir('../PHPMailer_v5.1') and file_exists('../PHPMailer_v5.1/class.phpmailer.php')){
						require('../PHPMailer_v5.1/class.phpmailer.php');   
						$message = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
                                      $message->CharSet = "utf-8"; 
                                      $message->SMTPDebug = false;    // enables SMTP debug information (for testing)
                                                                   // false = disabled debug
                                                                   // 1 = errors and messages
                                                                   // 2 = messages only

              			      $message->IsSMTP();            // telling the class to use SMTP
                                      $message->SMTPAuth = true;     // enable SMTP authentication
   
                           /*        //GMAIL Configuration
        	     	       	      $message->SMTPSecure = "ssl";            // sets the prefix to the servier
        		   	      $message->Host       = "smtp.gmail.com"; // sets GMAIL as the SMTP se
        		   	      $message->Port       = 465;              // set the SMTP port for the GMAIL server
        		   	      $message->Username   = "youuser@gmail.com"; // GMAIL user account: youuser@gmail.com
        			      $message->Password   = "youpass";         // GMAIL passwordrver 
                                      $message->From   = "youuser@gmail.com";       // you GMAIL email 
                         */          //end GMAIL Configuration

                                   //ONO configuration     
                          /*          $message->SMTPSecure = "";                  // sets the prefix to the servier
        		   	      $message->Host       = "smtp.ono.com";       // sets ONO as the SMTP server
        			      $message->Port       = 25;                   // set the SMTP port for the ONO server
        			      $message->Username   = "username";               // ONO username 
        			      $message->Password   = "pass";            // ONO password
                                      $message->From   = "user@ono.com";           // you ono email 
                          */       //end ONO Configuration

                                   //HOTMAIL configuration     
        			      $message->SMTPSecure = "tls";                // sets the prefix to the servier
        			      $message->Host       = "smtp.live.com";      // sets hotmail as the SMTP server
        			      $message->Port       = 587;                  // set the SMTP port for hotmail server
        			      $message->Username   = "youruser@hotmail.com";  // hotmail user account
        			      $message->Password   = "yourpass";               // hotmail password
                                      $message->From   = "youremail@hotmail.com";      // you hotmail email 
                                   //end HOTMAIL Configuration

        			      $message->AddAddress($EMAIL, '');  //Recipient's address set 
        			      $message->Subject = $subject;
                                      $message->FromName   = $from;
        			      $message->MsgHTML("$body");
			              $result=$message->Send();
					} else {
						echo strtoupper(i18n_r('pages_comments/errphphmail')).'<br />';
					}
				} else if ($phpmailer == '3-N') {
					$result = 1;
				}
			//Send email
			$msgshw = '';
			if ($result=='1') {
				   $msgshw = i18n_r('pages_comments/MSG_pcSUC');
			} else {
				   $msgshw = '*** '.strtoupper(i18n_r('pages_comments/MSG_pcERR')).'. ***\n'; 
			}
			if ($moder == 'N'){
				$seg = 6;
				$msgshw = $msgshw.'<b>'.i18n_r('pages_comments/pc_modcom').' '.i18n_r('pages_comments/pc_admin').'</b>\n';
			} else {
				$seg = 3;
			}
		} else {  //end if err=''
			$mi_array = '';
			$mi_array = $temp;
			//If there is some error.
			$seg= 6;
			if (trim($err) !=''){   
				$msgshw = '';
				if ($err1 != '') {
					$msgshw .= '*** '.strtoupper($err1).' ***\n';
				}
				if ($err2 != '') {
					$msgshw .= '*** '.strtoupper($err2).' ***\n';
					$msgshw .= strtoupper(i18n_r('pages_comments/Cap')).' '.$pot.'\n'.strtoupper(i18n_r('pages_comments/Code')).' '.$_POST['guest']['pot'];
				}	
			} else {
				$msgshw = '*** '.strtoupper(i18n_r('pages_comments/Co')).' ***\n';
			}
		}
	} //finish guest-submit
        

  }   //FINISH GUEST


////////////////////////////////////////////////////////////////
//
//     html page or alert of javascript
//
////////////////////////////////////////////////////////////////
?>
	<script type="text/javascript">
<!--
		alert ("<?php echo $msgshw; ?>");
		// time in milliseconds
		//var strCmd = "document.getElementById('txtcheck').style.display = 'none'";
		//var waitseconds = <?php echo $seg; ?> ;
		//action
		//var timeOutPeriod = waitseconds * 1000;
		//var hideTimer = setTimeout(strCmd, timeOutPeriod);
-->
	</script>
<?php
//	echo '<div id="txtcheck" style="display:block; padding: 20px; border: 4px double; margin: 18%;">';
//		echo '<div id="msgerr">';
//			echo $msgshw;
//		echo '<div />';
//	echo '</div>';
	
?>
