<?php
  global $SITEURL;
  $ruta = GSDATAPAGESPATH;
  $ncm_file= GSDATAOTHERPATH.'pages_comments/pc_manager.xml';
  $num = 0;
  $filex =0;
  	//ckeck if exists entry
  if (file_exists($ncm_file)){
	$domDocument = new DomDocument();
	$domDocument->load($ncm_file);

	//Creamos DOMXPath para filtrar
  	$xpath = new DOMXPath($domDocument);
	$filex = 1;
  }
  echo '<div><h3 style="font-size: 16px; margin:10px;">'.i18n_r('pages_comments/pc_mng').'</h3></div>';
  echo '<div style="margin-bottom: 15px; width: 90%; height: auto; border: 1px solid rgb(200, 200, 200); text-align: center; font-family: georgia; font-size: 14px; padding: 5px 0pt;">'.i18n_r('pages_comments/pc_addpagcomm').'</div>';
  if (is_dir($ruta)) {
	if ($dh = opendir($ruta)){
           $count = 0;
           echo '<table border=0 cellspacing=0 cellpadding=2 style="border-top: 1px dotted #CCCCCC; border-bottom: 1px dotted #CCCCCC; padding: 10px 0px;">';

 	   echo '<tr>';
           echo '<th style="vertical-align: middle; width: 33%;">'.i18n_r('pages_comments/pc_fl').'</th>';
           echo '<th style="vertical-align: middle; width: 33%;">'.i18n_r('pages_comments/pc_pg').'</th>';
           echo '<th style="vertical-align: middle; text-align: center; width: 33%;">'.i18n_r('pages_comments/pc_add').'</th>';

           echo '</tr>';
           while (($file = readdir($dh)) !== false){
               if($file!="." AND $file!=".." AND $file!=".htaccess" AND is_dir($ruta . $file)== false AND strtolower(substr($file,-4))=='.xml'){
                    if (file_exists($ruta.$file)) {
                        $data = getXML($ruta.$file);
		        		$title = stripslashes($data->title);
                        if ($filex == 1){
                            //check if one is inside of pc_manager.xml
                            $verN = $xpath->query('page[url="'.$file.'"]');
                            $num = $verN->length;
                        }
                    }
                    if ($num == 0) {
                    	$htgl = '';      
                    	if ($count % 2 == 1) {
                        	 $htgl = ' style="background: none repeat scroll 0 0 #F7F7F7;"';
                    	}  
	            		echo '<tr '.$htgl.'>';
                    	echo '<td style=" vertical-align: middle;" align="left">';
                    	echo $file;
                    	echo '</td>';
                    	echo '<td style=" vertical-align: middle;" align="left">';
                    	echo $title;
                    	echo '</td>';
                    	echo '<td style=" vertical-align: middle;" align="center">';
                              echo '<a href="load.php?id=pages_comments&action=add_pages&pc_url='.$file.'" title="'.i18n_r('pages_comments/pc_add').'">'.i18n_r('pages_comments/Add').'</a>';
                    	echo '</td>';

                    	echo '</tr>';
                    	$count++;
               	    }
	       }
           } //se acaba while
         echo '</table>';
         closedir($dh);
	} //se acaba if(dh
  } //sa acaba if (isdir

////////////////////////////////////////////////////////////
//
//  Integrate with NEWS MANAGER: $pcintegNM, $pcpageNM
//
////////////////////////////////////////////////////////////

  if ($pcintegNM == 1){
	echo '<div style="margin-bottom: 15px; width: 90%; height: auto; border: 1px solid rgb(200, 200, 200); text-align: center; font-family: georgia; font-size: 14px; padding: 5px 0pt;">'.i18n_r('pages_comments/pc_postsNM').'</div>';  
	$domDocument2 = new DomDocument();
	$domDocument2->load(GSDATAOTHERPATH.'news_manager/posts.xml');          
	$xpath2 = new DOMXPath($domDocument2);		
	$verN2 = $xpath2->query('item/slug');

	echo '<table border=0 cellspacing=0 cellpadding=2 style="border-top: 1px dotted #CCCCCC; border-bottom: 1px dotted #CCCCCC; padding: 10px 0px;">';

	echo '<tr>';
	echo '<th style=" vertical-align: middle; width: 33%;">'.i18n_r('pages_comments/pc_posts').'</th>';
	echo '<th style=" vertical-align: middle; width: 33%;">'.i18n_r('pages_comments/pc_pg').'</th>';
	echo '<th style=" vertical-align: middle; text-align: center; width: 33%;">'.i18n_r('pages_comments/pc_add').'</th>';
	echo '</tr>';
	$count = 0;
	foreach ($verN2 as $node){	
		if ($filex == 1){
                            //check if one is inside of pc_manager.xml= extension .NMG
                            $verN = $xpath->query('page[url="'.$node->nodeValue.'.NMG"]');
                            $num = $verN->length;
		}
		if ($num == 0) {
                    $htgl = '';      
                    if ($count % 2 == 1) {
                         $htgl = ' style="background: none repeat scroll 0 0 #F7F7F7;"';
                    }  
	            echo '<tr '.$htgl.'>';
                    echo '<td style=" vertical-align: middle;" align="left">';
                    echo $node->nodeValue;
                    echo '</td>';
                    echo '<td style=" vertical-align: middle;" align="left">';
                    echo $pcpageNM;
                    echo '</td>';
                    echo '<td style=" vertical-align: middle;" align="center">';
                              echo '<a href="load.php?id=pages_comments&action=add_pages&pc_url='.$node->nodeValue.'.NMG" title="'.i18n_r('pages_comments/pc_add').'">'.i18n_r('pages_comments/Add').'</a>';
                    echo '</td>';

                    echo '</tr>';
                    $count++;
               }

	}
	echo '</table>';

  }
?>
