<?php

/////////////////////////////////

// Called to file of summary

/////////////////////////////////

    echo '<br /><h3 style="font-size: 16px;">'.strtoupper(i18n_r('pages_comments/pc_hlp')).'</h3>';
    if (substr(@$_GET['read'],0,6)=='readme'){
        if (file_exists(GSPLUGINPATH.'pages_comments/help_lang/'.@$_GET['read'].'php')){
           include GSPLUGINPATH.'pages_comments/help_lang/'.@$_GET['read'].'php';     
        } else {
           if (file_exists( GSPLUGINPATH.'pages_comments/help_lang/readme_en.php')){
               include GSPLUGINPATH.'pages_comments/help_lang/readme_en.php'; 
           } else {
               echo html_entity_decode(i18n_r('pages_comments/pc_notfilehlp'));
           }    
        }  
    }
    if (!isset($_GET['read'])){
        if (file_exists(GSPLUGINPATH.'pages_comments/help_lang/summary_'.substr($LANG,0, 2).'.php')){
            include GSPLUGINPATH.'pages_comments/help_lang/summary_'.substr($LANG,0, 2).'.php';
        } else {
            if (file_exists(GSPLUGINPATH.'pages_comments/help_lang/summary_en.php')){
               include GSPLUGINPATH.'pages_comments/help_lang/summary_en.php';
            } else {
               echo html_entity_decode(i18n_r('pages_comments/pc_notfilehlp'));
            }    
        }
    }
?>
