<?php
           echo '<div class="form">';
		//comment
		if ($vemot =='Y'){
			echo '<div class="smail">';
				echo '<div id="smailer'.$count.'" class="smailer" style="display: none;">'.$s_emot.'</div>';
			echo '</div>';
		}
		echo '<div class="bbcode">';
			echo '<img class="bbc" title="'.i18n_r('pages_comments/pc_bold').'" src="'.$SITEURL.'plugins/pages_comments/images/bbc/bold.gif" onClick="javascript:imgbbcode(&quot;textarea'.$count.'&quot;,&quot;[b]&quot; , &quot;[/b]&quot;)" />';
			echo '<img class="bbc" title="'.i18n_r('pages_comments/pc_italic').'" src="'.$SITEURL.'plugins/pages_comments/images/bbc/italica.gif" onClick="javascript:imgbbcode(&quot;textarea'.$count.'&quot;,&quot;[i]&quot; , &quot;[/i]&quot;)" />';
			echo '<img class="bbc" title="'.i18n_r('pages_comments/pc_underline').'" src="'.$SITEURL.'plugins/pages_comments/images/bbc/underline.gif" onClick="javascript:imgbbcode(&quot;textarea'.$count.'&quot;,&quot;[u]&quot; , &quot;[/u]&quot;)" />';
			echo '<img class="bbc_d" src="'.$SITEURL.'plugins/pages_comments/images/bbc/divider.gif" />';
			echo '<img class="bbc" title="'.i18n_r('pages_comments/pc_link').' '.i18n_r('pages_comments/pc_img').'" src="'.$SITEURL.'plugins/pages_comments/images/bbc/img.gif" onClick="javascript:imgbbcode(&quot;textarea'.$count.'&quot;,&quot;[img]&quot; , &quot;[/img]&quot;)" />';
			echo '<img class="bbc" title="'.i18n_r('pages_comments/pc_link').'" src="'.$SITEURL.'plugins/pages_comments/images/bbc/url.gif" onClick="javascript:imgbbcode(&quot;textarea'.$count.'&quot;,&quot;[url]&quot; , &quot;[/url]&quot;)" />';
			echo '<img class="bbc_d" src="'.$SITEURL.'plugins/pages_comments/images/bbc/divider.gif" />';
			echo '<select name="bbc_color" id="bbc_color'.$count.'" onChange="javascript:imgbbcode(&quot;textarea'.$count.'&quot;,&quot;[color=&quot; , &quot;select&quot;)">';
				echo '<option value="">'.i18n_r('pages_comments/pc_color').'</option>';
				echo '<option value="black">'.i18n_r('pages_comments/pc_black').'</option>';
				echo '<option value="red">'.i18n_r('pages_comments/pc_red').'</option>';
				echo '<option value="yellow">'.i18n_r('pages_comments/pc_yellow').'</option>';
				echo '<option value="pink">'.i18n_r('pages_comments/pc_pink').'</option>';
				echo '<option value="green">'.i18n_r('pages_comments/pc_green').'</option>';
				echo '<option value="orange">'.i18n_r('pages_comments/pc_orange').'</option>';
				echo '<option value="purple">'.i18n_r('pages_comments/pc_purple').'</option>';
				echo '<option value="blue">'.i18n_r('pages_comments/pc_blue').'</option>';
				echo '<option value="beige">'.i18n_r('pages_comments/pc_beige').'</option>';
				echo '<option value="brown">'.i18n_r('pages_comments/pc_brown').'</option>';
				echo '<option value="teal">'.i18n_r('pages_comments/pc_teal').'</option>';
				echo '<option value="navy">'.i18n_r('pages_comments/pc_teal').'</option>';
				echo '<option value="maroon">'.i18n_r('pages_comments/pc_maroon').'</option>';
				echo '<option value="limegreen">'.i18n_r('pages_comments/pc_limegreen').'</option>';
				echo '<option value="white">'.i18n_r('pages_comments/pc_white').'</option>';
			echo '</select>';
			if ($vemot =='Y'){
				echo '<img class="bbc_d" src="'.$SITEURL.'plugins/pages_comments/images/bbc/divider.gif" />';
				echo '<input type="button" class="button" Onclick="javascript:Insertcom(&quot;smailer'.$count.'&quot;,&quot;0&quot; , &quot;0&quot;)" value="' . i18n_r('pages_comments/pc_emotic').'" />';
			}
		echo '</div>'; 
		if ($pcomnwords > 0){
			echo '<div class="nwords">'.i18n_r('pages_comments/pc_numword_dp').': '.$pcomnwords.'</div>';
		}
		echo '<div class="textarea">';
			echo '<textarea id="textarea'.$count.'" class="text" name="comentario" onblur="javascript:ctrl_crt(&quot;'.$count.'&quot;,&quot;'.$pcomnwords.'&quot;,&quot;'.i18n_r('pages_comments/MSG_charERR').'&quot;)" placeholder="'.i18n_r('pages_comments/Cm').'('.i18n_r('pages_comments/Rf').')'.'">'.$mi_arrayq["comentario"].'</textarea>';
	
               echo '</div>';

		//User and subject
		if (isset($_SESSION['LoggedIn']) && $ncusr=='Y'){
			echo '<input type="hidden" name="guest[nombre]" value="'.$_SESSION['Username'].'">';
			if ($nn == 'n'){
				echo '<input type="text" class="text" name="guest[subject]"  value="'.$mi_arrayq["subject"].'" placeholder= "'.i18n_r('pages_comments/Sub').'" />';
			}         
		} else {
			echo '<div class="input"><input type="text" class="text" name="guest[nombre]" value="'.$mi_arrayq["nombre"].'" placeholder= "'.i18n_r('pages_comments/Nb').'('.i18n_r('pages_comments/Rf').')'.'" />';
			echo '</div>';
                        echo '<div class="input"><input class="email" type="text" name="guest[email]" value="'.$mi_arrayq["email"].'" placeholder= "'.i18n_r('pages_comments/Em').': '.i18n_r('pages_comments/em_text').'" />';
			echo '</div>';
			echo '<div class="input"><input type="text" class="text" name="guest[city]"  value="'.$mi_arrayq["city"].'" placeholder= "'.i18n_r('pages_comments/Ct').'" />';
			echo '</div>';
			echo '<input type="hidden" name="guest[subject]" value="">';
		}

               echo '<div class="clear"></div>';       

               if ($capt =='Y'){
                   echo '<div id="captch"> ';
			echo '<img alt="" class="capt" id="captcha'.$imfin.'" src="'.$SITEURL.'plugins/pages_comments/img_cpt.php?url='.GSPLUGINPATH.'pages_comments/" />';
			echo '<input type="button" value="'.i18n_r('pages_comments/reload').'" onClick="javascript:rec_cpt(&quot;captcha'.$imfin.'&quot;,&quot;'.$SITEURL.'plugins/pages_comments/img_cpt.php?url='.GSPLUGINPATH.'pages_comments/&quot;)" />&nbsp;<span class="msgavs">'.i18n_r('pages_comments/rl').'</span>';
		  echo '</div>';
                  echo '<div class="cap_input">';
			echo '<input type="text"  value="" name="guest[pot]" /><span>'.i18n_r('pages_comments/Cpt').'('.i18n_r('pages_comments/Rf').')'.'</span>';
                  echo '</div>';
               }	
               echo '<div class="submit">';  
               	  echo '<input type="submit" value="'.i18n_r('pages_comments/Ev').'" name="guest-submit" />';
               echo '</div>';
               echo '<div class="clear"></div>'; 
/*
*/
?>
