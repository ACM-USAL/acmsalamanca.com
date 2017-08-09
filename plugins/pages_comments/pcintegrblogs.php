<?php 
    //data of settings for integrate blogs
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
?>
