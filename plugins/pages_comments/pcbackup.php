<?php
  global $SITEURL;
  $ruta = GSDATAOTHERPATH.'pages_comments/pages_comments_bak/';

  if (@$_GET['createbak'] == 'y') {   
     $pc_url= @$_GET['pc_url'];
     //first-> backup: guestbook_bak/guestbook.log+date+time+.bak
     copy ($log_path.$pc_url, $ruta.$pc_url.'_'.date('dmy_His').'.bak');    
     echo '<h2>'.i18n_r('pages_comments/backup_cd').'.</h2>';
  }


  if (@$_GET['delbak'] != '') {   
     unlink($ruta.@$_GET['delbak']);
     echo '<h2>"'.@$_GET['delbak'].'": '.i18n_r('pages_comments/deltd_log').'</h2>';
  }
  
  if (@$_GET['recbak'] != '') {   
     //first-> backup: guestbook_bak/guestbook.log+date+time+.bak
     $pc_url= substr(@$_GET['recbak'],0, strlen(@$_GET['recbak'])-18);
     copy ($log_path.$pc_url, $ruta.$pc_url.'_'.date('dmy_His').'.bak'); 
     copy ($ruta.@$_GET['recbak'], $log_path.$pc_url);    
     echo '<h2>"'.@$_GET['recbak'].'": '.i18n_r('pages_comments/recpd_log').'.</h2>';
  }

?>
	<script type="text/javascript">
        <!--
	function confirmar(formObj,count,msge,bck) {
	    if(!confirm(msge)) { 
                   return false; 
            } else {
                   if (bck == 'back'){
                           window.location="load.php?id=pages_comments&action=pc_backup&delbak=" + count + "";
                   } 
                   return false;
            }    
        }

        -->
	</script> 


<?php


  if (is_dir($ruta)) {
      if ($dh = opendir($ruta)){
        // echo '<h2>'.i18n_r('pages_comments/backup').'</h2><span style="float: right; margin-top: -35px;"><a style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; padding: 3px 5px; text-decoration: none;" href="load.php?id=pages_comments&action=pc_backup&createbak=y">'.i18n_r('pages_comments/backup_c').'</a></span> ';
         echo '<h3 style="margin-bottom: 3px;">'.i18n_r('pages_comments/listbackup').'</h3>';     
         echo '<ol class="more" >';
         while (($file = readdir($dh)) !== false){
         	if($file!="." AND $file!=".." AND $file!=".htaccess" AND is_dir($ruta . $file)== false AND strtolower(substr($file,-4))=='.bak'){
   	             echo '<li>';      
                     echo $file;
                     echo '<span style="margin-left: 20px;"><a title="'.i18n_r('pages_comments/rec_log').'" href="load.php?id=pages_comments&action=pc_backup&recbak='.$file.'">'.i18n_r('pages_comments/rec_log').'</a></span>';
                     echo '<span style="margin-left: 20px;"><a title="'.i18n_r('pages_comments/ndelf').'" href="load.php?id=pages_comments&action=pc_backup" onClick="return confirmar(this,&quot;'.$file.'&quot;,&quot;'.i18n_r('pages_comments/ndelfc').'\''.$file.'\'. '.i18n_r('pages_comments/delsure').'&quot;,&quot;back&quot;)"><b>X</b></a></span>'; 
                     echo '</li>';
                }
         }
         closedir($dh);
      }
  } 


?>
