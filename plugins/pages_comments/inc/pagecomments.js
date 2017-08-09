<script type="text/javascript">
<!--
	function getCookie(namecook){
		var nameCookie, valueCookie, cookie = null, cookies = document.cookie.split(';');
		for (i=0; i<cookies.length; i++){
		valueCookie = cookies[i].substr(cookies[i].indexOf('=') + 1);
		nameCookie = cookies[i].substr(0,cookies[i].indexOf('=')).replace(/^\s+|\s+$/g, '');
		if (nameCookie == namecook)
			cookie = unescape(valueCookie);
		}
		return cookie;
	}

	function getCookieData( name ) { 
		var patrn = new RegExp( "(?:^| )" + name + "=(.*?)(?:;|$)" ); 
		if ( match = (document.cookie.match(patrn) )) return match[1]; return false;
 	}

	function getVote(value, logfile, id, subid, count, siteurl) {

		if (getCookieData (logfile + id + subid)) {
			return;
		}

		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("pcrating"+count+id+subid).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET",siteurl + "plugins/pages_comments/pcvote.php?vote="+value+"&logfile="+logfile+"&id="+id+"&subid="+subid,true);
		xmlhttp.send();
	}

	function InsertForm (reply, log_file, email, count, at, capt, vemot, moder, ncusr, phpmailer, pcomnwords, titleform, siteurl, lang, miarrayp, miarray){
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("formid"+count).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET",siteurl + "plugins/pages_comments/pcinsertform.php?reply="+reply+"&logfile="+log_file+"&email="+email+"&count="+count+"&at="+at+"&capt="+capt+"&vemot="+vemot+"&moder="+moder+"&ncusr="+ncusr+"&phpmailer="+phpmailer+"&pcomnwords="+pcomnwords+"&titleform="+titleform+"&siteurl="+siteurl+"&lang="+lang+"&miarrayp="+miarrayp+"&miarray="+miarray,true);
		xmlhttp.send();

	}

	function cancelForm(id){
		document.getElementById(id).innerHTML="";
	}

	function Insertcom(id, at, count){
		var frm=document.getElementById(id);
		if(frm.style.display=="block"){
			frm.style.display="none";		      
			document.getElementById("formtitle"+count).style.display="none";
			bc="<?php echo i18n_r('pages_comments/pc_view').' '.i18n_r('pages_comments/pc_comc'); ?>";
			bcimgtext="<?php echo i18n_r('pages_comments/pc_expand'); ?>";   
			bcimg = "<?php echo $SITEURL; ?>plugins/pages_comments/images/ico-plus.png"  
			bcdisplay= "none";       
			if (id=="registerform"){
				document.getElementById("userloginregistertitle").style.display="none";
			} 
		}
		else
		if(frm.style.display=="none"){
			if (id=="registerform"){
				document.getElementById("userloginregistertitle").style.display="block";
			} 
			frm.style.display="block";
			if (document.getElementById("form"+count).style.display=="block"){	      
				document.getElementById("formtitle"+count).style.display="block";
			}
			bc="<?php echo i18n_r('pages_comments/pc_hide').' '.i18n_r('pages_comments/pc_comc'); ?>";
			bcimgtext="<?php echo i18n_r('pages_comments/pc_collapse'); ?>";
			bcimg = "<?php echo $SITEURL; ?>plugins/pages_comments/images/ico-minor.png"
			document.getElementById("textarea"+count).focus();
		}
		if (id.substr(0,6)=="tablar"){
			document.getElementById("bc"+id).title=bc;
		}
		if (id.substr(0,11)=="contentmain"){
			document.getElementById("img"+id).title=bcimgtext;
			document.getElementById("img"+id).src=bcimg;
			if (bcdisplay == "none"){
				if (document.getElementById("form"+count) != null){
					document.getElementById("form"+count).style.display="none";
				}
				document.getElementById("tablar"+at).style.display="none";
				if (document.getElementById("bctablar"+at) != null){
					document.getElementById("bctablar"+at).title=bc;
				}    

			} 
		}
                     
	}

	function confirmar(msge) {
		if(!confirm(msge)) { 
			return false; 
		} else {
			return true;
		}    
	}

	function shwimg(id){
		document.getElementById(id).style.visibility = 'visible';
	}
	function Nshwimg(id){
		document.getElementById(id).style.visibility = 'hidden';
	}

	function Smile(id,texto){
		var frm=document.getElementById(id);
		frm.comentario.value = frm.comentario.value + texto;
	}

	function rec_cpt(id,ourl){
		var aleat = Math.random();
		var mf = document.getElementById(id);
		mf.src = ourl + "&amp;" + aleat;
	}

	function ctrl_crt(id, numctres, errmessage) {
		var txt=document.getElementById("textarea"+id);
		var frm=document.getElementById("form"+id);
		if (numctres>0 && txt.value.length > numctres){
			alert (errmessage);
		}
	}


	function imgbbcode(id, action1, action2){
		if (action2 == 'select'){
			var count = id.substr(8, id.lenght);
			var select = document.getElementById('bbc_color'+count);
			if (select.options[select.selectedIndex].value == "") { exit;}
			action1 = action1 + select.options[select.selectedIndex].value + "]";
			action2 = "[/color]";
		}
		var txt=document.getElementById(id);
		if ('selectionStart' in txt) {
			// check whether some text is selected in the textarea: http://help.dottoro.com/ljtfkhio.php
                	if (txt.selectionStart != txt.selectionEnd) {
				//there is selection
				selection = txt.value.substring(txt.selectionStart, txt.selectionEnd);
                    		var newText = txt.value.substring (0, txt.selectionStart) + action1 + txt.value.substring(txt.selectionStart, txt.selectionEnd) + action2 + txt.value.substring(txt.selectionEnd);
				txt.value = newText;
			} else {
				//there is not selection
				if (action2 == '[/img]') {
					var img_link = prompt("<?php echo i18n_r('pages_comments/pc_url'); ?>","http://");
					//if (img_link != null && img_link != "http://") {
					if (img_link != null) {
						action1 = '[img]' + img_link;
					} else {
						action1 = "";
						action2 = "";
					}
				} else if (action2 == '[/url]') {
					var url_link = prompt("<?php echo i18n_r('pages_comments/pc_url'); ?>","http://");
					var url_text = prompt("<?php echo i18n_r('pages_comments/pc_urlbbc'); ?>","");
					if (url_text == null || url_text == ''){
						url_text = url_link;
					}
					if (url_link != null && url_link != "http://"){
						action1 = '[url=' + url_link + ']' + url_text;
					} else {
						action1 = "";
						action2 = "";
						alert ("<?php echo i18n_r('pages_comments/pc_urlbbc_n'); ?>");
					}
				}
				txt.value = txt.value + action1 + action2;
			}
		} else {  // Internet Explorer before version 9
			// create a range from the current selection
                	var textRange = document.selection.createRange ();
                    	// check whether the selection is within the textarea
               		var rangeParent = textRange.parentElement ();
                	if (rangeParent === txt) {
				//there is selection
                    		textRange.text = action1 + textRange.text + action2;
			} else {
				//there is not selection
				if (action2 == '[/img]') {
					var img_link = prompt("URL:?","http://");
					if (img_link != null) {
						action1 = '[img]' + img_link;
					} else {
						action1 = "";
						action2 = "";
					}
				} else if (action2 == '[/url]') {
					var url_link = prompt("<?php echo i18n_r('pages_comments/pc_url'); ?>","http://");
					var url_text = prompt("<?php echo i18n_r('pages_comments/pc_urlbbc'); ?>","");
					if (url_text == null || url_text == ''){
						url_text = url_link;
					}
					if (url_link != null && url_link != "http://"){
						action1 = '[url=' + url_link + ']' + url_text;
					} else {
						action1 = "";
						action2 = "";
						alert ("<?php echo i18n_r('pages_comments/pc_urlbbc_n'); ?>");
					}
				}
				txt.value = txt.value + action1 + action2;
			}
		} 
	}

-->
</script>
