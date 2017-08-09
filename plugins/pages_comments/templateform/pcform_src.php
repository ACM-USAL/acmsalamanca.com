<?php
           echo '<div class="form">';
               if (isset($_SESSION['LoggedIn']) && $ncusr=='Y'){
	           echo '<input type="hidden" name="guest[nombre]" value="'.$_SESSION['Username'].'">';
                   if ($nn == 'n'){
                       echo '<div class="textright"><label>'.i18n_r('pages_comments/Sub').'</label></div><div class="input">';
                             echo '<input type="text" class="text" name="guest[subject]"  value="'.$mi_arrayq["subject"].'" />';
                       echo '</div>';
                       echo '<div class="clear"></div>';
                   }         
               } else {
                   echo '<div class="textright"><label><b>'.i18n_r('pages_comments/red').'</b> '.i18n_r('pages_comments/Nb').'</label></div>';
                         echo '<div class="input"><input type="text" class="text" name="guest[nombre]" value="'.$mi_arrayq["nombre"].'" />';
                   echo '</div>';
                   echo '<div class="clear"></div>';
                   echo '<div class="textright"><label>'.i18n_r('pages_comments/Em').'</label></div>';
                        echo '<div class="input"><input class="email" type="text" name="guest[email]" value="'.$mi_arrayq["email"].'" />';
                   echo '</div>';
                   echo '<div class="clear"></div>';
                   echo '<div class="textright"><label>'.i18n_r('pages_comments/Ct').'</label></div>';
                        echo '<div class="input"><input type="text" class="text" name="guest[city]"  value="'.$mi_arrayq["city"].'" />';
                   echo '</div>';
                   echo '<div class="clear"></div>';
	           echo '<input type="hidden" name="guest[subject]" value="">';
               }

               echo '<div class="textright"><label><b>'.i18n_r('pages_comments/red').'</b> '.i18n_r('pages_comments/Cm').'</label>';
                    echo '<div class="bbcode"><b>bbcode:</b><br /><span title="bold: [b]text[/b]">[b][/b]</span>&nbsp;&nbsp;<span title="Italic: [i]text[/i]">[i][/i]</span>&nbsp;&nbsp;<span title="underline: [u]text[/u]">[u][/u]</span><br />[img]link image[/img]<br />[color=colour][/color]<br />[url]link[/url]<br />[url=link]title[/url]</div>';
               echo '</div>';
               echo '<div class="textarea">';
			if ($pcomnwords > 0){
				echo '<div class="nwords">'.i18n_r('pages_comments/pc_numword_dp').': '.$pcomnwords.'</div>';
			}
			echo '<textarea id="textarea'.$count.'" class="text" name="comentario" onblur="javascript:ctrl_crt(&quot;'.$count.'&quot;,&quot;'.$pcomnwords.'&quot;,&quot;'.i18n_r('pages_comments/MSG_charERR').'&quot;)">'.$mi_arrayq["comentario"].'</textarea>';
	
               echo '</div>';
               echo '<div class="clear"></div>';       
               if ($vemot =='Y'){
                    echo '<div class="smail">'.$s_emot.'</div>';       
               }
               if ($capt =='Y'){
                   echo '<div class="captch"> ';
                       echo '<img alt="" class="capt" id="captcha'.$imfin.'" src="'.$SITEURL.'plugins/pages_comments/img_cpt.php?url='.GSPLUGINPATH.'pages_comments/" /><input type="button" value="'.i18n_r('pages_comments/reload').'" onClick="javascript:rec_cpt(&quot;captcha'.$imfin.'&quot;,&quot;'.$SITEURL.'plugins/pages_comments/img_cpt.php?url='.GSPLUGINPATH.'pages_comments/&quot;)" />&nbsp;<span class="msgavs">'.i18n_r('pages_comments/rl').'</span>';
                  echo '</div>';
                  echo '<div class="cap_input">';
                       echo '<input type="text"  value="" name="guest[pot]" /><span><b>'.i18n_r('pages_comments/red').'</b> '.i18n_r('pages_comments/Cpt').'</span>';
                  echo '</div>';
               }	
               echo '<div class="submit">';  
               	  echo '<input type="submit" value="'.i18n_r('pages_comments/Ev').'" name="guest-submit" />';
                  echo '<span><b>'.i18n_r('pages_comments/red').'</b> '.i18n_r('pages_comments/Rf').'</span>';
               echo '</div>';

?>
