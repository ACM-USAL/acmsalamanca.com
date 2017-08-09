<?php
  global $i18n;
  $page_file= @$_GET['pc_url'];
  if (substr($page_file, -4) == '.NMG'){
	$file_com= $pcpageNM.substr($page_file,0,-4);
  } else {
  	$file_com= substr($page_file,0,-4);
  }
  $pcm_file= GSDATAOTHERPATH.'pages_comments/pc_manager.xml';
  if (file_exists(GSDATAPAGESPATH.$page_file) && substr($page_file, -4) != '.NMG') {
        $data = getXML(GSDATAPAGESPATH.$page_file);
        $title = stripslashes($data->title);
  } else {
        if (substr($page_file, -4) == '.NMG'){
		$title = substr($page_file, 0, -4).'</span><span style="color:#777777;"> ('.i18n_r('pages_comments/pc_postsNM').')';
	}
  }
  echo '<br /><h3 style="font-size: 16px;">'.i18n_r('pages_comments/pc_del').'</h3>';
  /*  echo '<span style="float: right; margin-top: -15px;">';
    echo '<ul style= "list-style: none outside none; margin: 0 0 5px 5px;">';
    echo '<li style = "margin: 0 0 5px;"><a href="load.php?id=pages_comments" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_NEWS').'">'.i18n_r('pages_comments/pc_view').' '.i18n_r('pages_comments/pc_NEWS').'</a></li>';
    echo '<li style = "margin: 0 0 5px;"><a href="load.php?id=pages_comments&action=pcsettings" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_mng').'">'.i18n_r('pages_comments/pc_view').' '.i18n_r('pages_comments/pc_mng').'</a></li>';
    echo '</ul>';
    echo '</span>';*/
  echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_pg').': '.$title.'</span><br /><br />';
  if (isset($_POST['del-submit'])) {
      if ($_POST['q_del_page']=='Y') {
            	//Eliminamos un registro: page
            	$domDocument = new DomDocument();
            	$domDocument->preserveWhiteSpace = FALSE;
            	$domDocument->load($pcm_file);
	//Creamos DOMXPath para filtrar
	$xpath = new DOMXPath($domDocument);			
        $num=0;
	$verN = $xpath->query('page[url="'.$page_file.'"]');		
  	$num = $verN->length;
	if ($num > 0){
		$tagdel= $verN->item(0);
		$ParentTagdel = $tagdel->parentNode;
		$ParentTagdel->removeChild($tagdel);
	        echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 14px; font-weight: bold;">&nbsp;&nbsp;-'.i18n_r('pages_comments/pc_delyapag').'</span><br />';
        }


  	        $domDocument->save($pcm_file);
        //fin eliminar registro
        } else {
              echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 14px; font-weight: bold;">&nbsp;&nbsp;-'.i18n_r('pages_comments/pc_ndelpag').'</span><br />';
        }
        //delete log file of comments if exists
        if (is_file(GSDATAOTHERPATH.'pages_comments/'.$file_com.'.log')){
            if ($_POST['q_del_fil']=='Y') {
               unlink(GSDATAOTHERPATH.'pages_comments/'.$file_com.'.log');
	       echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 14px; font-weight: bold;">&nbsp;&nbsp;-'.i18n_r('pages_comments/pc_delyafil').'</span><br />';

                       //remove all entries of pc_lastcom.xml
            	       $domDocument = new DomDocument();
            	       $domDocument->preserveWhiteSpace = FALSE;
   		       $domDocument->load(GSDATAOTHERPATH.'pages_comments/pc_lastcom.xml');
                       $xpathDoc = new DOMXPath($domDocument);    
                       $log_data = $xpathDoc->query("entry[filelog='".$file_com.'.log'."']"); 
                       //remove items
                       foreach ($log_data as $node){
                       		$ndL = $node->parentNode;
	    	                $ndL -> removeChild($node);  
                       }     
	               //save new modified document
   	               $domDocument->save(GSDATAOTHERPATH.'pages_comments/pc_lastcom.xml');


            } else {
               echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 14px; font-weight: bold;">&nbsp;&nbsp;-'. i18n_r('pages_comments/pc_ndelfil').'</span><br />';

            }
        }

     } else {
        echo '<form style="" name="formulario" id="formdel" action="load.php?id=pages_comments&action=del_pages&pc_url='.$page_file.'" method="post">';
	   echo '<input type="hidden" name="q_uri" value="'.$page_file.'">';
           echo i18n_r('pages_comments/pc_delpag').':';
           echo '<ul style=" margin-top: 5px;">';
           echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_del_page" value="Y">&nbsp;&nbsp;'.i18n_r('pages_comments/YES').'</li>';
	       echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_del_page" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>';
           echo '</ul>';
           echo i18n_r('pages_comments/pc_delfil').': ';
           if (is_file(GSDATAOTHERPATH.'pages_comments/'.$file_com.'.log')){
                 echo '<ul style=" margin-top: 5px;">';
                 echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_del_fil" value="Y">&nbsp;&nbsp;'.i18n_r('pages_comments/YES').'</li>';
	             echo '<li><INPUT style="vertical-align: sub;" type=radio name="q_del_fil" value="N" checked="">&nbsp;&nbsp;'.i18n_r('pages_comments/NO').'</li>';
                 echo '</ul>';
           } else {
                 echo i18n_r('pages_comments/pc_delext').'<br />';
           }
  	       echo '<br /><input type="submit" style="margin-left: 7px; width: 60px;" value="'.i18n_r('pages_comments/Ev').'" id="delform" name="del-submit" />';

        echo '</form>';

     }
?>
