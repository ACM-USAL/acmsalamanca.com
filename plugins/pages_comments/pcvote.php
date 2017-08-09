<?php 

/***************************************************************************************

		File called by function javascript: getvote

/***************************************************************************************/

	$valuevote = $_REQUEST['vote'];
	$logfile = $_REQUEST['logfile'];
	$id = $_REQUEST['id'];
	$subid = $_REQUEST['subid'];

	//cookie time control for log&comment: duration 1day(60sec * 60min * 24h)
	setcookie($logfile.$id.$subid , $id.$subid , time() + (60*60*24), "/"); 

   	if ( file_exists($logfile) ) {
        	$domDocument = new DomDocument();
        	$domDocument->load($logfile);
        	//DOMXPath to filter
        	$xpath = new DOMXPath($domDocument);
        	$verN = $xpath->query("entry/pcrating[../Id=".$id." and ../SubId=".$subid."]");
		if ($verN){	
			$votetotal = (int)$verN->item(0)->nodeValue + (int)$valuevote;
			if ($votetotal > 0 ){
				$sgplus = "+";
			} else {
				$sgplus = "";
			}
			$verN->item(0)->nodeValue = $sgplus.$votetotal;
		}
		$domDocument->save($logfile);
		echo $sgplus.$votetotal;
	}

?>
