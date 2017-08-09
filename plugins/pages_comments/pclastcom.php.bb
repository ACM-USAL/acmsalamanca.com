<?php
    echo '<h3 style="font-size: 16px;">'.i18n_r('pages_comments/pc_lastcom').'</h3>';
    $log_file = $log_path . 'pc_lastcom.xml';

    //delete one registry: entry
    if (@$_GET['dateseg'] != ''){
          $dataseg = @$_GET['dateseg'];
          $pc_url =  @$_GET['pc_url'];

          $domDoc = new DomDocument();
          $domDoc->preserveWhiteSpace = FALSE;

          //search comment by dateseg and remove
          //load data of xml of log
          $domDoc->load($log_path . $pc_url);
          //DOMXPath to filter
          $xpathDoc = new DOMXPath($domDoc);
          $log_data = $xpathDoc->query("entry[dateseg='".$dataseg."']");
                //remove item
                $ndL = $log_data ->item(0)->parentNode;
	    	$ndL -> removeChild($log_data ->item(0));
	        //save new modified document
   	        $domDoc->save($log_path . $pc_url);

          //search comment by dateseg
          //load data of xml of pc_lastcom.xml
          $domDoc->load($log_file);
          //DOMXPath to filter 
          $xpathDoc = new DOMXPath($domDoc);    
          $log_data = $xpathDoc->query("entry[dateseg='".$dataseg."']"); 
                //remove item
                $ndL = $log_data ->item(0)->parentNode;
	    	$ndL -> removeChild($log_data ->item(0));
	        //save new modified document
   	        $domDoc->save($log_file);

 	  echo '<span style="font-size: 20px; text-align: center; text-decoration: blink;"><b>--- '.i18n_r('pages_comments/nm_delreg').' ---</b></span><br /><br /><br /><br />';

?>
            <script type="text/javascript">
                    setTimeout(function(){window.location="load.php?id=pages_comments&action=pc_last"},2000);
                    exit;
            </script>
<?php

    }  

//// START see logs

if(file_exists($log_file)) {

      $domDocument = new DomDocument();

      //search number of last comments in settings
      $domDocument->load($log_path.'pc_settgs.xml');
      //DOMXPath to filter 
      $xpath = new DOMXPath($domDocument);    
      $verN = $xpath->query('sett/ncom_rctpost'); 
      $rctpost = $verN->item(0)->nodeValue;

      //search last comments in pc_lastcom.xml
      $domDocument->load($log_file);
      //DOMXPath to filter 
      $xpath = new DOMXPath($domDocument);

      $verN = $xpath->query('entry');
      $num = $verN->length;
      if ($num < $rctpost){
          $rctpost = $num;
      }

      for ($q= $num; $q>=($num-$rctpost+1) ; $q--){
           $verN = $xpath->query("entry[position()=".$q."]") ;      
                 $pc_url = $verN->item(0)->getElementsByTagName("filelog")->item(0)->nodeValue;
                 $log_time =$verN->item(0)->getElementsByTagName("dateseg")->item(0)->nodeValue;
                 $pc_urlxml = substr($pc_url ,0,-4).'.xml';

           if (file_exists(GSDATAPAGESPATH.$pc_urlxml)) {
                    $data = getXML( GSDATAPAGESPATH.$pc_urlxml);
                    $title = html_entity_decode(stripslashes($data->title));
           }


           echo '<span style="color: #000000; text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: normal;">'.i18n_r('pages_comments/nm_pgttl').': '.$title.'</span>';

          //load data of xml
          $domDoc = new DomDocument();

          //search last comment
          $domDoc->load($log_path . $pc_url);
          //DOMXPath to filter 
          $xpathDoc = new DOMXPath($domDoc);    
          $log_data = $xpathDoc->query("entry[dateseg='".$log_time."']"); 

          foreach($log_data as $node) {
    		            $verNDList = $node->getElementsByTagName( "*" );
                            $atrib = $node->getAttribute('id');
			    foreach ($verNDList as $node1){		
		                          $name = $node1->nodeName;

		                          $d = $node1->nodeValue;
	                                  $n = strtolower($name);
                                          //echo 'name: '.$name.' - $value(d):'.$d.'<br />'; 
					  $ip_regex = '/^(?:25[0-5]|2[0-4]d|1dd|[1-9]d|d)(?:[.](?:25[0-5]|2[0-4]d|1dd|[1-9]d|d)){3}$/';

					  //check if its an email address
					  if (check_email_address($d)) {
					  	$d = '<a href="mailto:'.$d.'">'.$d.'</a>';
					  }

					  //check if its an ip address
                                          if (substr($name, 0 , 2) == 'ip') {
	                                        if ($d == $_SERVER['REMOTE_ADDR']) {
					  		$ipcom = $i18n['THIS_COMPUTER'].' (<a href="http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress='. $d.'" target="_blank" >'.$d.'</a>)';
					  	} else {
							$ipcom = '(<a href="http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress='. $d.'" target="_blank" >'.$d.'</a>)';
					  	}
					  }

                                          if ($name == 'Id'){
					      $idcom = $d;
					  }

                                          if ($name == 'SubId'){
					      $sidcom = $d;
					  }

                                          if ($name == 'Nb'){
					      $namecom = $d;
					  }

                                          if ($name == 'Cm'){
	                                      $comcom = BBcodeN($d);
	                                      $comcom = str_replace(htmlentities('<br />', ENT_QUOTES, "UTF-8"), '<br />', $comcom);
	                                      $comcom = stripslashes($comcom);
					  }

                                          if ($name == 'Subj'){
					      $subcom = $d;
					  }

                                          if ($name == 'Ct'){
					      $ctcom = $d;
					  }

                                          if ($name == 'Em'){
					      $emailcom = $d;
					  }

                                          if ($name == 'answ'){
					      $answcom = $d;
					  }

					  //check if its a date
					  if ($n === 'date') {
						$d = substr($d,0,strpos($d,'+')-1);
					  	$datecom = lngDate($d);
					  }

					  //check Moderación
					  if ($n === 'moder') {
                                                $moderat = $d;
					  }
                            } //for each
	  } //for each

                //comments
        	echo '<table style="line-height: 11px; margin-bottom: 0px; width: 100%;">';
         	    echo '<tr style="border-bottom: none;">';
         	          echo '<td style="border-bottom: none;width: 65px; padding: 0px; line-height: 11px;"><b>Atributo Id: </b>'.$atrib.'</td>';
                          echo '<td style="border-bottom: none;width: 65px; padding: 0px; line-height: 11px;"><b>Id: </b>'.$idcom.'</td>';
                          echo '<td style="border-bottom: none;width: 65px; padding: 0px; line-height: 11px;"><b>SubId: </b>'.$sidcom.'<a style="padding-left: 25px;" title="'.i18n_r('pages_comments/ndel').'" onClick= "return confirm(&quot;'.i18n_r('pages_comments/ndel').'&quot;)" href="load.php?id=pages_comments&action=pc_last&pc_url='.$pc_url.'&dateseg='.$log_time.'">'.i18n_r('pages_comments/ndel').'</a></td>';
        	echo '</table>';
        	echo '<table style="line-height: 11px; width: 100%;">';
                    echo '</tr>';
                    echo '<tr>';
                          echo '<td style="text-align: right; width: 45px; padding: 0px; line-height: 11px;"><b>'.i18n_r('pages_comments/Nb').':</b></td><td style="border-right: 1px solid #EEEEEE;text-align: left; width: 180px; padding: 0px; line-height: 11px;">'. html_entity_decode($namecom).'</td>';
                          echo '<td style="text-align: right;width: 80px; padding: 1px; line-height: 14px;"><b>'.i18n_r('pages_comments/pubd').':</b></td><td style="padding: 1px; line-height: 14px;">'.$datecom.'</td>';
                    echo '</tr>';
                    echo '<tr>';
                          echo '<td style="text-align: right;width: 45px; padding: 1px; line-height: 14px;"><b>'.i18n_r('pages_comments/Em').':</b></td><td style="border-right: 1px solid #EEEEEE;text-align: left; width: 180px; padding: 1px; line-height: 14px;">'.$emailcom.'</td>';
                          echo '<td style="text-align: right;width: 80px; padding: 1px; line-height: 14px;"><b>'.i18n_r('pages_comments/Sub').':</b></td><td style="padding: 1px; line-height: 14px;">'. html_entity_decode($subcom).'</td>';
                    echo '</tr>';
                    echo '<tr>';
                          echo '<td style="text-align: right; width: 45px; padding: 1px; line-height: 14px;"><b>'.i18n_r('pages_comments/Ct').':</b></td><td style="border-right: 1px solid #EEEEEE;text-align: left; width: 180px; padding: 1px; line-height: 14px;">'. html_entity_decode($ctcom).'</td>';
                          echo '<td style="text-align: right; width: 80px; padding: 1px; line-height: 14px;"><b>Ip address:</b></td><td style="padding: 1px; line-height: 14px;">'.$ipcom.'</td>';
                    echo '</tr>';			
                    echo '<tr>';
                          echo '<td style="text-align: right; width: 45px; padding: 1px; line-height: 14px;"><b>'.i18n_r('pages_comments/Cm').':</b></td><td colspan="3" style="padding: 1px; line-height: 14px;">'. html_entity_decode($comcom).'</td>';
                    echo '</tr>';
                    echo '<tr style="border-bottom: 2px solid #CCCCCC;">';
                          echo '<td style="text-align: right; width: 45px; padding: 1px; line-height: 13px;"><b>'.i18n_r('pages_comments/answ').':</b></td><td style="border-right: 1px solid #EEEEEE;text-align: left; width: 180px; padding: 1px; line-height: 13px;">'.$answcom.'</td>';
                          echo '<td style="text-align: right; width: 80px; padding: 1px; line-height: 13px;"><b>'.i18n_r('pages_comments/moder').':</b></td><td style="padding: 1px; line-height: 13px;">'.$moderat.'</td>';
                    echo '</tr>';
                echo '</table>';
		echo '</p>';

      }  //for
} else {   //end If file does not exist

		echo '<label style="font-size: 16px;">'.i18n_r('pages_comments/pc_notcomment').'</label>';

}
    


?>
