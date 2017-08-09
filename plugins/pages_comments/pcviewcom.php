<?php
$f_name = substr(@$_GET['pc_url'],0,-4).'.log';
$lg_file = $log_path . $f_name;
if(file_exists($lg_file)) {
$pc_url=@$_GET['pc_url'];
  if (file_exists(GSDATAPAGESPATH.$pc_url)) {
        $data = getXML( GSDATAPAGESPATH.$pc_url);
        $title = html_entity_decode(stripslashes($data->title));
  }
    echo '<br /><h3 style="font-size: 16px;">'.i18n_r('pages_comments/pc_vcom').'</h3>';
    echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_pgttl').': '.$title.'</span><br />';

	echo '<div class="edit-nav" >';
	echo '<a style="margin-top: -37px;" href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.@$_GET['pc_url'].'&actionp=delete" accesskey="c" title="'.$i18n['CLEAR_ALL_DATA'].' '.$f_name.'">'.$i18n['CLEAR_THIS_LOG'].'</a>';
	echo '<a style="margin-top: -11px;" href="load.php?id=pages_comments&action=pc_backup&pc_url='.$f_name.'&createbak=y" title="'.i18n_r('pages_comments/backup_c').' \''.$f_name.'\'">'.i18n_r('pages_comments/backup_c').'</a>';

	echo '<div class="clear"></div>';
	echo '</div>';


    //delete log complety
    if (@$_GET['actionp'] == 'delete' && strlen($f_name)>0) {
	    echo i18n_r('pages_comments/ndel');
		if (@$_GET['view_del']=='y'){
		        unlink($lg_file);
		   	exec_action('logfile_delete');
			echo '<label>Log '. $f_name. $i18n['MSG_HAS_BEEN_CLR'].'<br />';     
		} else {
			//check that want to delete log
        		echo '&nbsp;&nbsp;&nbsp;<a href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.	$pc_url.'&actionp=delete&view_del=y">'.i18n_r('pages_comments/acpt').'</a>&nbsp;&nbsp;&nbsp;';
		        echo '<a href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.$pc_url.'" >'.i18n_r('pages_comments/canc').'</a><br />';

		}
	    echo '</div>';
    	    echo '</div>';
	    echo '<div id="sidebar" >';
	    include('template/sidebar-plugins.php');
    	    echo '</div>';
	    echo '<div class="clear"></div>';
	    echo '</div>';
	    echo 'get_template("footer")';
        exit;
    }


    //delete one registry: entry
    if (@$_GET['n_del'] != ''){
	$ndel = @$_GET['n_del'];

	if (@$_GET['vdel']=='y'){
    		$domDocument = new DomDocument();
		$domDocument->preserveWhiteSpace = FALSE;

                //remove entry of log
   		$domDocument->load($lg_file);
	    	$domNodeList = $domDocument->documentElement;
       		$domNodeList = $domDocument->getElementsByTagname('entry');
                $dateseg =  $domNodeList ->item($ndel)->getElementsByTagname('dateseg')->item(0)->nodeValue;
   		$ndL = $domNodeList ->item($ndel)->parentNode;
	    	$ndL -> removeChild($domNodeList ->item($ndel));
	        //save new modified document
   	        $domDocument->save($lg_file);

                //remove entry of pc_lastcom.xml
   		$domDocument->load($log_path . 'pc_lastcom.xml');
                $xpathDoc = new DOMXPath($domDocument);    
                $log_data = $xpathDoc->query("entry[dateseg='".$dateseg."']"); 
                //remove item
                $ndL = $log_data ->item(0)->parentNode;
	    	$ndL -> removeChild($log_data ->item(0));
	        //save new modified document
   	        $domDocument->save($log_path.'pc_lastcom.xml');




		echo '<b>'.i18n_r('pages_comments/pc_delreg').'</b>&nbsp;&nbsp;&nbsp;&nbsp;';
	        echo '<a href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.$pc_url.'" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/BACK').'">'.i18n_r('pages_comments/BACK').'</a><br />';

	} else {
		echo '<b>'.i18n_r('pages_comments/ndel').'</b>';
	        echo '&nbsp;&nbsp;&nbsp;<a href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.		$pc_url.'&n_del='.@$_GET['n_del'].'&vdel=y">'.i18n_r('pages_comments/acpt').'</a>&nbsp;&nbsp;&nbsp;';
		echo '<a href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.$pc_url.'">'.i18n_r('pages_comments/canc').'</a><br />';

	        //check that want to delete this registry
		//check if that entry exists
		$domDocument = new DomDocument();
		$domDocument->load($lg_file);

	    	$domNodeList = $domDocument->documentElement;
       		$domNodeList = $domDocument->getElementsByTagname('entry');
		$dNdList = $domNodeList->item($ndel)->getElementsByTagName( "*" );
			
			foreach ($dNdList as $node){
				echo '&nbsp;&nbsp;&nbsp;&nbsp;<b>'.html_entity_decode($node->nodeName).':</b> '.html_entity_decode($node->nodeValue).'<br />';
			}               
	}
    }
/////   end delete



//// START acept comment moderated: MODERATION
    if (@$_GET['acptcom'] == 'Y'){
	$ncommod = @$_GET['n_commod'];

    echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_cmoder').'</span><br /><br />';

   	$domDocument = new DomDocument();
   	$domDocument->load($lg_file);
        $verN = $domDocument->getElementsByTagname('entry');
        $verNDList = $verN->item($ncommod)->getElementsByTagName( "moder" ); 
        $verNDList->item(0)->nodeValue= 'Y';
    	$domDocument->save($lg_file);

     }

    if (@$_GET['acptcom'] == 'N'){
	$ncommod = @$_GET['n_commod'];

    echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_cmoder').'</span><br /><br />';

   	$domDocument = new DomDocument();
   	$domDocument->load($lg_file);
        $verN = $domDocument->getElementsByTagname('entry');
        $verNDList = $verN->item($ncommod)->getElementsByTagName( "moder" ); 
        $verNDList->item(0)->nodeValue= 'N';
    	$domDocument->save($lg_file);

     }

//// END moderation.


//// START see logs
    //load data of xml
    $log_data = getXML($lg_file);


    if (@$_GET['n_del'] == ''){
	//echo '<ol class="more" >'<li>;
	$count = 0;
	foreach ($log_data as $log) {
		echo '<p style="font-size:11px;line-height:15px; margin-bottom: 3px;" ><b style="line-height:20px; text-transform: uppercase;" >'.i18n_r('pages_comments/Cm').'</b><a style="padding-left: 15px;" title="'.i18n_r('pages_comments/ndel').'" href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.@$_GET['pc_url'].'&n_del='.$count.'"><img style="vertical-align: middle;" src="'.$SITEURL.'plugins/pages_comments/images/b_del.png" /></a><br />';
                $atrib = $log->attributes();
		foreach($log->children() as $child) {
					  $name = $child->getName();

					  $d = $log->$name;
					  $n = strtolower($child->getName());
					  $ip_regex = '/^(?:25[0-5]|2[0-4]d|1dd|[1-9]d|d)(?:[.](?:25[0-5]|2[0-4]d|1dd|[1-9]d|d)){3}$/';
					  $url_regex = @"((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|())+[wd:#@%/;$()~_?+-=.&]*)";

					  //check if its an url address
					  if (do_reg($d, $url_regex)) {
					  	$d = '<a href="'. $d .'" target="_blank" >'.$d.'</a>';
					  }

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
					      $comcom = $d;
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
						if ($d == 'N'){
                                                    $moderat = $d.' <span style=" font-style: oblique;">('. i18n_r('pages_comments/pc_modcom').')</span><a style="padding-left: 10px; color: red;" title="'.i18n_r('pages_comments/pc_modcom').'" href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.@$_GET['pc_url'].'&n_commod='.$count.'&acptcom=Y"><b>'.i18n_r('pages_comments/acpt').'</b></a><br />'; 
						} else if ($d == 'Y'){
                                                    $moderat = $d.'<a style="padding-left: 10px; color: red;" title="'.i18n_r('pages_comments/pc_unpub').'" href="load.php?id=pages_comments&action=pc_viewcom&pc_url='.@$_GET['pc_url'].'&n_commod='.$count.'&acptcom=N"><b>'.i18n_r('pages_comments/pc_ocult').'</b></a><br />';
						}
					  }

					  //check rating
					  if ($n === 'pcrating') {
                                                $pcrating = $d;
					  }


	        }

                //comments
        	echo '<table style="line-height: 11px; margin-bottom: 0px; width: 100%;">';
         	    echo '<tr style="border-bottom: none;">';
         	          echo '<td style="border-bottom: none;width: 45px; padding: 0px; line-height: 11px;"><b>Atributo Id: </b>'.$atrib['id'].'</td>';
                          echo '<td style="border-bottom: none;width: 45px; padding: 0px; line-height: 11px;"><b>Id: </b>'.$idcom.'</td>';
                          echo '<td style="border-bottom: none;width: 45px; padding: 0px; line-height: 11px;"><b>SubId: </b>'.$sidcom.'</td>';
			  echo '<td style="border-bottom: none;width: 45px; padding: 0px; line-height: 11px;"><b>'.i18n_r('pages_comments/pc_vote').': </b>'.$pcrating.'</td>';
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
                          echo '<td style="text-align: right; width: 45px; padding: 1px; line-height: 13px;"><b>'.i18n_r('pages_comments/pc_answ').':</b></td><td style="border-right: 1px solid #EEEEEE;text-align: left; width: 180px; padding: 1px; line-height: 13px;">'.$answcom.'</td>';
                          echo '<td style="text-align: right; width: 80px; padding: 1px; line-height: 13px;"><b>'.i18n_r('pages_comments/moder').':</b></td><td style="padding: 1px; line-height: 13px;">'.$moderat.'</td>';
                    echo '</tr>';
                echo '</table>';
		echo '</p>';
		//echo '</p></li>';
		$count++;        } //end foreach

	//echo '</ol>';
    } //end if get[n_del] ==''
} else {   //end If file does not exist

		echo '<label>'.$i18n['MISSING_FILE'].': &lsquo;<em>'.@$lg_file.'</em>&rsquo;</label>';

}
    


?>
