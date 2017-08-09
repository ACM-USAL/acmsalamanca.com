<?php

////////////////////////////////////////////////////////
/////    SHOW PAGES THAT HAS COMMENTS SYSTEM      //////
////////////////////////////////////////////////////////

    echo '<br /><h3 style="font-size: 16px;">'.i18n_r('pages_comments/pc_NEWS').'</h3>';


// If exists pc_manager.xml
//where are saved the pages that contain comments
if(file_exists($log_file)) {     
        //load data of xml
	$domDocument = new DomDocument();
    	$domDocument->load($log_file);          
	$xpath = new DOMXPath($domDocument);		
        $num=0;
        $n=0; 
        if (isset($_POST['filter-submit']) && @$_GET['filter'] == 'y' && $_POST['q_filter']!=' '){
		$verN = $xpath->query('page[group="'.$_POST["q_filter"].'"]');			
	} else {
		$verN = $xpath->query('page');
	}
  	$num = $verN->length;
               
	echo '<table style="margin-left: -20px; width: 106%;">';
	$count = 1;
        echo '<tr><th></th><th>'.i18n_r('pages_comments/pc_pgttl').'</th><th style="text-align: center; padding: 2px 2px;" title="'.i18n_r('pages_comments/pc_comc').'">'.i18n_r('pages_comments/pc_comc').'</th><th></th><th></th></tr>';

        foreach ($verN as $node1){
		$dNdList = $node1->getElementsByTagName( "url" );	
		if (substr($dNdList->item(0)->nodeValue,-4) == '.xml') {
			$dNdList = $node1->getElementsByTagName( "*" );	
                	$htgl = '';      
                	if ($count % 2 == 0) {
                   		$htgl = ' style="background: none repeat scroll 0 0 #F7F7F7;"';
                	}  
			echo '<tr '.$htgl.'>';
                	echo '<td style="text-align: center; vertical-align: middle;">'.$count.'</td>';
			foreach ($dNdList as $node){
                        	$name = $node->nodeName;
				$d= $node->nodeValue;	
				$ds= '';
				$n = strtolower($name);
                                if ($n == 'url') {
					echo '<td style="vertical-align: middle;">';
					if (file_exists(GSDATAPAGESPATH.$d)){
                                             $data = getXML(GSDATAPAGESPATH.$d);
                                             $nctitle = stripslashes($data ->title);
					} else {
					     $nctitle = $d;
					}
					$url= $d;
					$ds='<a href="load.php?id=pages_comments&amp;action=edt_pages&amp;pc_url='.$url.'" title="'.i18n_r('pages_comments/pc_edopt').': '.$url.'">'.$nctitle.'</a>';
 				        echo $ds;
				        echo '</td>';
                                }

 			        if ($n == 'com'){
					echo '<td style="text-align: center; vertical-align: middle;">';
					if (is_file($log_path.substr($url,0,-4).'.log')){
                                           $domDocument1 = new DomDocument();
    	                                   $domDocument1->load($log_path.substr($url,0,-4).'.log');          
	                                   $xpath1 = new DOMXPath($domDocument1);		
                                           $verN1 = $xpath1->query('entry');
                                           $verlen = $verN1->length;
					   $ds='<a href="load.php?id=pages_comments&amp;action=pc_viewcom&amp;pc_url='.$url.'" title="'.i18n_r('pages_comments/pc_vcom').'">'.$verlen.'</a>'; 
				           echo $ds;
					}
				        echo '</td>';
                                }

			} 
                	echo '<td style="vertical-align: middle;" align="center">';
                	echo '<a style="color: #AAAAAA; font-size: 13px; text-decoration: none;" href="'.find_url(substr($url,0,-4), '').'" title="'.i18n_r('pages_comments/pc_viewpage').': '.$nctitle.'" TARGET="_blank">#</a>';
                	echo '</td>';
                	echo '<td style=" vertical-align: middle;" align="center">';
                	echo '<a style="color: #999999; font-size: 13px; text-decoration: none;" href="load.php?id=pages_comments&amp;action=del_pages&amp;pc_url='.$url.'" title="'.i18n_r('pages_comments/pc_del').'">X</a>';
                	echo '</td>';
                	echo '</tr>';
			if ($pcpageNM == substr($url,0,-4) && $pcintegNM == '1'){
                        	$domDocument2 = new DomDocument();
    	                	$domDocument2->load(GSDATAOTHERPATH.'news_manager/posts.xml');          
	                		$xpath2 = new DOMXPath($domDocument2);		
                        	$verN2 = $xpath2->query('item/slug');
				echo '<tr '.$htgl.'>';
				echo '<td>';
				echo '</td>';
				echo '<td colspan = "4">'.i18n_r('pages_comments/pc_postsNM');
				echo '</td>';
				echo '</tr>';
				$count1 = 1;
				foreach ($verN2 as $node){
					$verN3 = $xpath->query("page/url[../url='$node->nodeValue.NMG']");
					$verlen3 = $verN3->length;
					if ($verlen3 == 1) {
					echo '<tr '.$htgl.' style="border: none;">';
					echo '<td style="border-bottom: 1px solid #EEEEEE; border-top: none; text-align: center; vertical-align: middle;">';
					echo '</td>';
					echo '<td style="border-bottom: 1px solid #EEEEEE; border-top: none;">';
						echo $count.'.'.$count1.' <a href="load.php?id=pages_comments&amp;action=edt_pages&amp;pc_url='.$node->nodeValue.'.NMG" title="'.i18n_r('pages_comments/pc_edopt').': '.$node->nodeValue.'">'.$node->nodeValue.'</a>';
					echo '</td>';
					echo '<td style="border-bottom: 1px solid #EEEEEE; border-top: none; text-align: center; vertical-align: middle;">';
						$logNM = $pcpageNM.$node->nodeValue.'.log';
						if (file_exists($log_path.$logNM)){
                        				$domDocument4 = new DomDocument();
    	                				$domDocument4->load($log_path.$logNM);
	                				$xpath4 = new DOMXPath($domDocument4);		
                        				$verN4 = $xpath4->query('entry');
							$verlen4 = $verN4->length;
							echo '<a href="load.php?id=pages_comments&amp;action=pc_viewcom&amp;pc_url='.$logNM.'" title="'.i18n_r('pages_comments/pc_vcom').'">'.$verlen4.'</a>';
						}
					echo '</td>';
					echo '<td style="border-bottom: 1px solid #EEEEEE; border-top: none; vertical-align: middle;" align="center">';
                				echo '<a style="color: #AAAAAA; font-size: 13px; text-decoration: none;" href="'.find_url(substr($url,0,-4), '');
						if ($PRETTYURLS==''){
							echo '&post='.$node->nodeValue;
						} else {
							echo 'post/'.$node->nodeValue;
						}
						echo '" title="'.i18n_r('pages_comments/pc_viewpage').': '.$node->nodeValue .'" TARGET="_blank">#</a>';
					echo '</td>';
					echo '<td style="border-bottom: 1px solid #EEEEEE; border-top: none; vertical-align: middle;" align="center">';
						echo '<a style="color: #999999; font-size: 13px; text-decoration: none;" href="load.php?id=pages_comments&amp;action=del_pages&amp;pc_url='.$node->nodeValue.'.NMG" title="'.i18n_r('pages_comments/pc_del').'">X</a>';
					echo '</td>';
					echo '</tr>';
					$count1 ++;
					}
				}

			}
			$count++;

		}




		//if ($fic
	} //end foreach log
        echo '</table>';

}
else{   //If file does not exist
                ?>
		    <!--    <label><?php echo $i18n['MISSING_FILE']; ?>: &lsquo;<em><?php echo @$log_name; ?></em>&rsquo;</label> -->
                <?php
}









?>
