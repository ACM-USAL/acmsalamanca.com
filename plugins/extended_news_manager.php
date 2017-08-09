<?php

/*
Plugin Name: GetSimple Extended News Manager 
Description: Extended News Manager with FANCY URL support, News RSS Feed,  News RSS Feed Menu, Latest News Menu, News Pagination. Orginal author Rogier Koppejan extended by Site Info Service Development Team
Version: 2.0
Author: Site Info Service Development Team
Author URI: http://siteinfoservice.org
*/


# get correct id for plugin
$thisfile = basename(__FILE__, '.php');

# register plugin
register_plugin(
  $thisfile,
  'Extended News Manager',
  '2.0',
  'Site Info Service Development Team',
  '#',
  'Extended News Manager with FANCY URL support, News RSS Feed,  News RSS Feed Menu, Latest News Menu, News Pagination. Orginal author Rogier Koppejan extended by Site Info Service Development Team',
  'pages',
  'extended_news_manager'
);


# hooks
add_action('theme-header', 'news_css');
add_action('header', 'admin_js');
add_action('pages-sidebar', 'createSideMenu', array($thisfile, 'Extended News Manager'));




# definitions
define('NEWS_DATA', GSDATAPATH  . 'news/'); 
define('NEWS_RSS',  GSDATAOTHERPATH . 'news_feed/feed.xml');
define('NEWS_RSS_DIR',  GSDATAOTHERPATH . 'news_feed/');
define('SETTINGS_PATH', GSDATAOTHERPATH  . 'website.xml');
define('PLUGIN_PATH', GSPLUGINPATH . 'extended_news_manager/');
define('RSS_SETTINGS', GSPLUGINPATH . 'extended_news_manager/settings.xml');
define('SITE_URL', $SITEURL);
define('PRETTY_URLS', $PRETTYURLS);
define('SITE_NAME', $SITENAME);
define('COMMENT_PROV', $SITEURL.'plugins/extended_news_manager/comm_prov/');


#load internationalzation
i18n_merge('extended_news_manager') || i18n_merge('extended_news_manager','en_US');

############################### ADMIN FUNCTIONS ################################

/*******************************************************
 * @function extended_news_manager
 * @action main function, creates, edits, deletes news. Admin Settings...
 */
function extended_news_manager() {
	
	if (!file_exists(NEWS_DATA)) {
		$create_success = create_directory(NEWS_DATA);
	 	sleep(5);
		
		if($create_success == false){		
			echo '<h3>'. i18n_r('extended_news_manager/PLUGINTITLE') . '</h3><p>'. i18n_r('extended_news_manager/PLUGINCREATE_DIR_FAULT') .'</p>';			
		 }else{		 
			echo '<h3>'. i18n_r('extended_news_manager/PLUGINTITLE') . '</h3><p>'. i18n_r('extended_news_manager/CREATE_DIR_SUCC') .'</p>'; 
		 }
	}
	if (isset($_GET['edit'])) {
		$id = empty($_GET['edit']) ? uniqid() : $_GET['edit'];
		edit_news($id);
	} elseif (isset($_GET['delete'])) {
		$id = $_GET['delete'];
		delete_news($id);
	} elseif (isset($_POST['submit'])) {
		save_news();
	} elseif (isset($_GET['settings'])) {
		settings();
	} elseif (isset($_POST['save_settings'])) {
		settings_save();
	} else {
		news_overview();
	}
 
}

/*******************************************************
 * @function create_directory
 * @action create directory if not exist
 */
function create_directory($data){
	$local_copy_path = trim($data, "\/");
	$dir = preg_replace("/\//us", "\\",  $local_copy_path);

	$check = true;
	$checkDir = is_dir($data);

	if(!$checkDir){
		$check = mkdir($dir,  0755, true);		
	}
	
	if(!$check){	
		return false;	
	}else{		
		return true;
	}
}

/*******************************************************
 * @function news_overview
 * @action list news and provide options for editing
 */
function news_overview() {
	$news = array_reverse(get_news(NEWS_DATA));
	
	$text = '
	<label>'. i18n_r('extended_news_manager/PLUGINTITLE') . '</label>
	<div class="edit-nav" >
		<a href="load.php?id=extended_news_manager&edit">'. i18n_r('extended_news_manager/NEWS_CREATE') .'</a>
		<a href="load.php?id=extended_news_manager&settings">'. i18n_r('extended_news_manager/NEWS_SETTINGS') .'</a>
		<a href="load.php?id=extended_news_manager">'. i18n_r('extended_news_manager/NEWS_OVERVIEW') .'</a>
		<div class="clear"></div>
	</div>';
	
	if (!empty($news)) {
		$text .= '<table class="highlight">';
		foreach ($news as $news_item) {
			$id = basename($news_item, ".xml");
			$file = NEWS_DATA . $news_item;
			$data = @getXML($file);
			$date = $data->date;
			$title = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');

			$text .= '<tr>
			<td>
			  <a href="load.php?id=extended_news_manager&edit='. $id .'" title="'. i18n_r('extended_news_manager/NEWS_EDIT') . $title .'">
				'. $title .'
			  </a>
			</td>
			<td style="text-align: right;">
			  <span>'. $date .'</span>
			</td>
			<td class="delete">
			  <a href="load.php?id=extended_news_manager&delete='. $id .'" class="delconfirm" title="'. i18n_r('extended_news_manager/NEWS_DELETE') .  $title .'?">
				X
			  </a>
			</td>
	  </tr>';
		}
		
		$text .= '</table>';
	}
	
	$text .= '<p>'. i18n_r('extended_news_manager/NEWS_NUMBER') .'<b>' . count($news) . '</b></p>';
	echo $text;
	create_news_rss(); 
}


/*******************************************************
 * @function edit_news
 * @param $id - unique news name
 * @action edit or create  news
 */
function edit_news($id) {
	$file_name = urlencode($id);
	$file = NEWS_DATA . $file_name . '.xml';
	$data = @getXML($file);
	$title = @stripslashes($data->title);
	$content = @stripslashes($data->content);
	$excerpt = @stripslashes($data->excerpt);
	
	$text = '
    <div class="edit-nav" >
		<a href="load.php?id=extended_news_manager&edit">'. i18n_r('extended_news_manager/NEWS_CREATE') .'</a>
		<a href="load.php?id=extended_news_manager&settings">'. i18n_r('extended_news_manager/NEWS_SETTINGS') .'</a>
		<a href="load.php?id=extended_news_manager">'. i18n_r('extended_news_manager/NEWS_OVERVIEW') .'</a>
    <div class="clear"></div>
	</div>
	<h3>';
	
	if(empty($data)){
		$text .= i18n_r('extended_news_manager/NEWS_CREATE');
	}else{
		$text .= i18n_r('extended_news_manager/NEWS_EDIT');
	}   
    $text .= '</h3>
	<form class="largeform" action="load.php?id=extended_news_manager" method="post" accept-charset="utf-8">
		<p><input name="id" type="hidden" value="'. $id .'" />
    	<p><input class="text title" name="post-title" type="text" value="'. $title .'" /></p>
		<p><textarea class="extended_news_manager" name="post-content">'. $content .'</textarea></p>
		<p>
			<b>'. i18n_r('extended_news_manager/NEWS_EXCEPT') .'</b><br />
			<textarea class="extended_news_manager" name="post-excerpt" style="height: 50px">'. $excerpt .'</textarea>
			<span style="color: #999;">'. i18n_r('extended_news_manager/NEWS_EXCEPT_HELP') .'</span>
		</p>
		<p><input name="submit" type="submit" class="submit" value="'. i18n_r('extended_news_manager/NEWS_SAVE') .'" />&nbsp;&nbsp;or&nbsp;&nbsp;
		   <a href="load.php?id=extended_news_manager" class="cancel" title="'. i18n_r('extended_news_manager/NEWS_CANCEL') .'">'. i18n_r('extended_news_manager/NEWS_CANCEL') .'</a>
		</p>
	</form>';

  // Include ckeditor
  global $HTMLEDITOR;
  if (isset($_GET['id']) and $_GET['id'] == "extended_news_manager") {
    if (isset($HTMLEDITOR) && $HTMLEDITOR != '') {
      $TOOLBAR = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],'/',  ['Styles','Format','Font','FontSize'],";
      $EDLANG = defined('GSEDITORLANG') ? GSEDITORLANG : 'en';
    $text .= ' <script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
      <script type="text/javascript">
        var editor = CKEDITOR.replace( "post-content", {
          skin : "getsimple",
          forcePasteAsPlainText : true,
          language : "'. $EDLANG .'",
          defaultLanguage : "'. $EDLANG .'",
          entities : true,
          uiColor : "#FFFFFF",
          height: "300",
		  baseHref : "'. SITE_URL .'",

          toolbar :
          [
          '. $TOOLBAR .'
          ],
	      tabSpaces:10,
	      filebrowserBrowseUrl : "filebrowser.php?type=all",
		  filebrowserImageBrowseUrl : "filebrowser.php?type=images",
	      filebrowserWindowWidth : "730",
	      filebrowserWindowHeight : "500"
        });
		var editor1 = CKEDITOR.replace( "post-excerpt", {
          skin : "getsimple",
          forcePasteAsPlainText : true,
          language : "'. $EDLANG .'",
          defaultLanguage : "'. $EDLANG .'",
          entities : true,
          uiColor : "#FFFFFF",
          height: "300",
		  baseHref : "'. SITE_URL .'",
          toolbar :
          [
          '. $TOOLBAR .'
          ],
	      tabSpaces:10,
	      filebrowserBrowseUrl : "filebrowser.php?type=all",
		  filebrowserImageBrowseUrl : "filebrowser.php?type=images",
	      filebrowserWindowWidth : "730",
	      filebrowserWindowHeight : "500"
        });
      </script>';
    }
  }
  echo $text;
}


/******************************************************* 
 * @function save_news
 * @action write $_POST data to a file
 */
function save_news() {
	$id = $_POST['post-title'];
	$file_name = urlencode($id);
	$file = NEWS_DATA . $file_name . '.xml';
	$title = htmlentities($_POST['post-title'], ENT_QUOTES, 'UTF-8');
	$content = htmlentities($_POST['post-content'], ENT_QUOTES, 'UTF-8');
	$excerpt = htmlentities($_POST['post-excerpt'], ENT_QUOTES, 'UTF-8');
	
	if (!file_exists($file)) {
		$date = date('j M Y');
	}else{
		$data = @getXML($file);
		$date = $data->date;
	}
	
	$xml = @new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><item></item>');
	$xml->addChild('title', empty($title) ? '(no title)' : $title);
	$xml->addChild('date', $date);
	$xml->addChild('content', $content);
	$xml->addChild('excerpt', $excerpt);
	XMLsave($xml, $file);
	
	if (!is_writable($file)){
		$text = '<div class="error">'. i18n_r('extended_news_manager/NEWS_SAVE_ERROR') .'</div>';
	}else{
		$text = '<div class="updated">'. i18n_r('extended_news_manager/NEWS_SAVE_SUCC') .'</div>';
	}
	echo $text;
	news_overview();
}


/*******************************************************
 * @function delete_news
 * @param $id - unique news id
 * @action deletes the requested news
 */
function delete_news($id) {
	$file_name = urlencode($id);
	$file = NEWS_DATA . $file_name . '.xml';
	if (file_exists($file)){
		unlink($file);
		$text = '';
		if (file_exists($file)){
			$text .= '<div class="error">'. i18n_r('extended_news_manager/NEWS_DELETE_ERROR') .'</div>';
		}else{
			$text .= '<div class="updated">'. i18n_r('extended_news_manager/NEWS_DELETE_SUCC') .'</div>';
		}
	}
	echo $text;
	news_overview();
}

/*******************************************************
 * @function settings
 * @action Extended News Manager Settings
 */
function settings(){
	
	$settings = getXML(RSS_SETTINGS);

	$text = '
	  	<label>'. i18n_r('extended_news_manager/PLUGINTITLE') . i18n_r('extended_news_manager/SETTINGS') .'</label>
	<div class="edit-nav" >
		<a href="load.php?id=extended_news_manager&edit">'. i18n_r('extended_news_manager/NEWS_CREATE') .'</a>
		<a href="load.php?id=extended_news_manager&settings">'. i18n_r('extended_news_manager/NEWS_SETTINGS') .'</a>
		<a href="load.php?id=extended_news_manager">'. i18n_r('extended_news_manager/NEWS_OVERVIEW') .'</a>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<div id="metadata_window" >
		<form class="largeform" action="load.php?id=extended_news_manager" method="post" accept-charset="utf-8"><hr />
		<div class="leftopt">
			<h3>'. i18n_r('extended_news_manager/S_MENU_T_SETTINGS') .'</h3>
			<p><label for="menu_title">'. i18n_r('extended_news_manager/S_MENU_TITLE') .':</label>
			<input type="text" value="'.$settings->menu_title.'" name="menu_title" id="menu_title" class="text short">'. i18n_r('extended_news_manager/S_NEWS_MTITLE') .'
			</p>
			<p><label for="menu_title_lenght">'. i18n_r('extended_news_manager/S_MENU_TITLE_LEN') .':</label>
			<input type="text" value="'.$settings->menu_title_lenght.'" name="menu_title_lenght" id="menu_title_lenght" class="text short">'. i18n_r('extended_news_manager/S_CHARNUM') .'
			</p>
			<p><label for="menu_content_lenght">'. i18n_r('extended_news_manager/S_MENU_SHORT_LEN') .':</label>
			<input type="text" value="'.$settings->menu_content_lenght.'" name="menu_content_lenght" id="menu_content_lenght" class="text short">'. i18n_r('extended_news_manager/S_CHARNUM') .'
			</p>
			 <p><label for="menu_news_num">'. i18n_r('extended_news_manager/S_MENU_NEWS_NUM') .':</label>
			 <select class="text" id="menu_news_num" name="menu_news_num">';
			 
			 	$news_menu = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
				$menu_cnt = count($news_menu);
			 	for($i=0;$i<$menu_cnt;$i++){	
					if($news_menu[$i] == $settings->menu_news_num){
						$text .= '<option value="'.$news_menu[$i].'" selected="">'.$news_menu[$i].'</option>';
					}else{
						$text .= '<option value="'.$news_menu[$i].'">'.$news_menu[$i].'</option>';
					}
				}
			$text .= ' </select>'. i18n_r('extended_news_manager/S_MENU_DEF') .' 	
			</p>
			<p><label for="menu_man">'. i18n_r('extended_news_manager/S_MENU_ITEMS') .' :</label>
			<select class="text" id="menu_man" name="menu_man">
				<option value="warp" ';
				
		if($settings->menu_man == 'warp'){
			$text .= 'selected';
		}
			$text .= '>'. i18n_r('extended_news_manager/S_MENU_WARP') .'</option>	
			<option value="cut"';
			
		if($settings->menu_man == 'cut'){
			$text .= 'selected';
		}			
			
			$text .= '>'. i18n_r('extended_news_manager/S_MENU_CUT') .'</option>	
			</select> 
			'. i18n_r('extended_news_manager/S_MENU_WARPCUT_MES') .'
			</p>
		</div>
		<div class="rightopt">  
			<h3>'. i18n_r('extended_news_manager/S_NEWS_PAGE') .'</h3>
			<p><label for="news_page">'. i18n_r('extended_news_manager/S_NEWS_HOLDER') .':</label>	
			<select class="text" id="news_page" name="news_page">';

			
			$pages_list = get_news(GSDATAPAGESPATH);
			$count = count($pages_list);
			
			for($i=0;$i<$count;$i++){
				$page_name =  basename($pages_list[$i], ".xml");
				if($page_name == $settings->news_page){
					$text .= '<option value="'.$page_name.'" selected="">'.$page_name.'</option>';
				}else{
					$text .= '<option value="'.$page_name.'">'.$page_name.'</option>';
				}
			}
			
			$text .= '</select></p>
			 <p><label for="news_page_num">'. i18n_r('extended_news_manager/S_NEWS_NUM') .':</label>
			 <select class="text" id="news_page_num" name="news_page_num">';
			 	$news_numbers = array('5', '6', '7', '8', '9', '10', '15', '20', '30');
				$news_cnt = count($news_numbers);
			 	for($i=0;$i<$news_cnt;$i++){	
					if($news_numbers[$i] == $settings->news_page_num){
						$text .= '<option value="'.$news_numbers[$i].'" selected="">'.$news_numbers[$i].'</option>';
					}else{
						$text .= '<option value="'.$news_numbers[$i].'">'.$news_numbers[$i].'</option>';
					}
				}
			$text .= ' </select>'. i18n_r('extended_news_manager/S_MENU_DEF') .'	 
			</p>
			<p><label for="navigation_type">'. i18n_r('extended_news_manager/S_NEWS_PAG') .':</label>
			<select class="text" id="navigation_type" name="navigation_type">';
					if($settings->navigation_type == 'default'){
						$text .= '<option value="default" selected="">'. i18n_r('extended_news_manager/S_NEWS_PAG_DEF') .'</option>	
								  <option value="paginate">'. i18n_r('extended_news_manager/S_NEWS_PAG_PAG') .'</option>';
					}else{
						$text .= '<option value="default">'. i18n_r('extended_news_manager/S_NEWS_PAG_DEF') .'</option>	
								  <option value="paginate" selected="">'. i18n_r('extended_news_manager/S_NEWS_PAG_PAG') .'</option>';
					}
	$text .= '</select>    
	</p>

			<p id="post-private-wrap" class="inline"><label for="external_comments" style="color: rgb(51, 51, 51);">'. i18n_r('extended_news_manager/S_NEWS_COMM') . '</label> 
			&nbsp;&nbsp;&nbsp;<input type="checkbox" name="external_comments" id="external_comments"';

		if($settings->external_comments == 'on'){
		 $text .= ' checked';
		}
    
		$text .= '>
			</p>
			<p>'. i18n_r('extended_news_manager/S_EXTERNAL') . ' <a href="http://get-simple.info/extend/plugin/external-commenting/73/">'. i18n_r('extended_news_manager/S_NEWS_COMM_INT') . '</a></p>
		</div>
		<div class="clear"></div><hr />
		<h3>'. i18n_r('extended_news_manager/S_RSS') . '</h3>
		<div class="leftopt">
			<p><label for="feed_title">'. i18n_r('extended_news_manager/S_RSS_TITLE') . ':</label>
			<input type="text" value="'.$settings->feed_title.'" name="feed_title" id="feed_title" class="text short">'. i18n_r('extended_news_manager/S_RSS_TITLE_INFO') . '
			</p>
			<p><label for="feed_description">'. i18n_r('extended_news_manager/S_RSS_DESC') . ':</label>
			<textarea name="feed_description" id="feed_description" class="text" >'.$settings->feed_description.'</textarea>'. i18n_r('extended_news_manager/S_RSS_DESC_INFO') . '
			</p>
   		<p><label for="feed_language">'. i18n_r('extended_news_manager/S_RSS_LANG') . ':</label>
		<select class="text" id="feed_language" name="feed_language">';
		
	$lang_handle = opendir(GSLANGPATH) or die("Unable to open ". GSLANGPATH);
	
	while ($lfile = readdir($lang_handle)) {
		if( is_file(GSLANGPATH . $lfile) && $lfile != "." && $lfile != ".." )	{
			$lang_array[] = basename($lfile, ".php");
		}
	}
	
	foreach($lang_array as $key=>$val){
		if($val == strtolower($settings->feed_language)){
			$text .= '<option value="'.$val.'" selected="">'.$val.'</option>';
		}else{
			$text .= '<option value="'.$val.'">'.$val.'</option>';
		}
	}
	
	$text .= '
		</select> 
		'. i18n_r('extended_news_manager/S_RSS_LANG_INFO') . '
	</p>
		<p><label for="feed_encoding">'. i18n_r('extended_news_manager/S_RSS_ENC') . ':</label>
	<select class="text" id="feed_encoding" name="feed_encoding">
		<option value="UTF-8" selected="">UTF-8</option>			
	</select>    
	</p>
	<p><label for="feed_num">'. i18n_r('extended_news_manager/S_RSS_IT_NUM') . ':</label>
		 <select class="text" id="feed_num" name="feed_num">';
			 	$feed = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '15', '20', '30', '40');
				$feed_cnt = count($feed);
			 	for($i=0;$i<$feed_cnt;$i++){	
					if($feed[$i] == $settings->feed_num){
						$text .= '<option value="'.$feed[$i].'" selected="">'.$feed[$i].'</option>';
					}else{
						$text .= '<option value="'.$feed[$i].'">'.$feed[$i].'</option>';
					}
				}
	$text .= ' </select>'. i18n_r('extended_news_manager/S_MENU_DEF') . ' 
	</p>
	<p><label for="feed_generator">'. i18n_r('extended_news_manager/S_RSS_GEN') . ':</label>
	<input type="text" value="'.$settings->feed_generator.'" name="feed_generator" id="feed_generator" class="text short">
	</p>
		
						
			
		</div>
   		<div class="rightopt">
		
			<p><label for="feed_image">'. i18n_r('extended_news_manager/S_RSS_IMG') . ':</label>
			<select class="text" id="feed_image" name="feed_image">';	

			$images_list = getFiles(GSDATAPATH . '/uploads');
			$count_images = count($images_list);
			if($count_images > 3){
				for($i=0;$i<$count_images;$i++){
					$path_info = pathinfo($images_list[$i]);
					
					$im_link_thumb =  SITE_URL . 'data/thumbs/thumbsm.'; 
					
					if($path_info['extension'] == 'jpg' || $path_info['extension'] == 'jpeg' || $path_info['extension'] == 'png' || $path_info['extension'] == 'gif'){
					
						$images_name =  basename($images_list[$i]);

						if($images_name ==  basename($settings->feed_image)){
							$text .= '<option value="'. $images_name.'" selected="">'.$images_name.'</option>';
							$selected_image_link =  $im_link_thumb . $images_name; 
							
						}else{
							$text .= '<option value="'. $images_name.'">'.$images_name.'</option>';
						}
					}
				}
				
			}else{
				$text .= '<option value="default">Please first upload site image.</option>';
			}
			
			$text .= '</select>
			<p style="text-align: center; height: 60px; padding: 40px 0">
			<img id="feed_im_prew" src="'.$selected_image_link.'" alt="rss-icon" />
			</p>
			</p>
		
	<p><label for="feed_icon">'. i18n_r('extended_news_manager/S_RSS_ICO') . ':</label>
	<select class="text" id="feed_icon" name="feed_icon">';

		$files = glob(PLUGIN_PATH . "icons/*.{jpg,jpeg,png,gif,bmp}", GLOB_BRACE);
		$count_images = count($files);
		if($count_images > 0){
			$icon_rss = basename($settings->feed_icon);
			$ic_link = SITE_URL . 'plugins/extended_news_manager/icons/';			
			for ($i=0; $i<$count_images; $i++){
				
				$images_name =  basename($files[$i]);
				if($images_name ==  $icon_rss){
					$text .= '<option value="'. $ic_link . $images_name .'" selected="">'.$images_name.'</option>';
					$selected_icon_link =  $ic_link. $images_name; 

				}else{
					$text .= '<option value="'. $ic_link . $images_name .'">'.$images_name.'</option>';
				}				
			}
		}else{
			$text .= '<option value="default">Please first upload RSS Icons.</option>';
		}
	
			$text .= '</select>
			<p style="text-align: center; height: 60px; padding: 40px 0">
			<img id="feed_ico_prew" src="'.$selected_icon_link.'" alt="rss-icon" />
			</p>	
	
	
	
	<br />
	<p  class="inline"><label for="feed_enable" style="color: rgb(51, 51, 51);">'. i18n_r('extended_news_manager/S_RSS_ENABLE') . ' </label> 
	&nbsp;&nbsp;&nbsp;<input type="checkbox" name="feed_enable" id="feed_enable"';

	if($settings->feed_enable == 'on'){
		$text .= ' checked';
	}
    
	$text .= '></p>
	</div>
	<div class="clear"></div><br />
		<p id="submit_line"><span><input type="submit" value="'. i18n_r('extended_news_manager/S_SAVE') . '" name="save_settings" id="save_settings"  class="submit" style="width: 200px;"></span> 
		&nbsp;&nbsp;'. i18n_r('extended_news_manager/S_OR') . '&nbsp;&nbsp; <a href="load.php?id=extended_news_manager" class="cancel">'. i18n_r('extended_news_manager/S_CANCEL') . '</a>
		</p>
</form>
</div>
';
	echo $text ;
}

/*******************************************************
 * @function settings_save
 * @action Extended News Manager Settings Save
 */
function settings_save(){
	$menu_title = htmlentities($_POST['menu_title'], ENT_QUOTES, 'UTF-8');
	if(empty( $menu_title)){
		$menu_title = 'Latest News';
	}
	
	$menu_title_lenght = htmlentities($_POST['menu_title_lenght'], ENT_QUOTES, 'UTF-8');
	if(empty( $menu_title_lenght)){
		$menu_title_lenght = '30';
	}
	
	$menu_content_lenght = htmlentities($_POST['menu_content_lenght'], ENT_QUOTES, 'UTF-8');
	if(empty( $menu_content_lenght)){
		$menu_content_lenght = '100';
	}
	
	$menu_man = htmlentities($_POST['menu_man'], ENT_QUOTES, 'UTF-8');
	if(empty( $menu_man)){
		$menu_man = 'warp';
	}
	
	$menu_news_num = htmlentities($_POST['menu_news_num'], ENT_QUOTES, 'UTF-8');
	if(empty( $menu_news_num)){
		$menu_news_num = '5';
	}
	
	$news_page_num = htmlentities($_POST['news_page_num'], ENT_QUOTES, 'UTF-8');
	if(empty( $news_page_num)){
		$news_page_num = '5';
	}
	$navigation_type = htmlentities($_POST['navigation_type'], ENT_QUOTES, 'UTF-8');
	if(empty( $navigation_type)){
		$navigation_type = 'default';
	}
	
	$news_page = htmlentities($_POST['news_page'], ENT_QUOTES, 'UTF-8');
	if(empty( $news_page)){
		$news_page = 'news';
	}
	
	$external_comments = htmlentities($_POST['external_comments'], ENT_QUOTES, 'UTF-8');
	if(empty( $external_comments)){
		$new_page = 'off';
	}
	
	$feed_title = htmlentities($_POST['feed_title'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_title)){
		$feed_title = SITE_NAME;
	}
	
	$feed_description = htmlentities($_POST['feed_description'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_description)){
		$feed_description = get_site_name();
	}

	$feed_image =   SITE_URL . 'data/uploads/' . $_POST['feed_image'];
	if(empty( $feed_image)){
		$feed_image =  SITE_URL . "data/uploads/site_logo.jpg";
	}
	
	$feed_language = htmlentities($_POST['feed_language'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_language)){
		$feed_language  = "en-us";
	}
	$feed_encoding = htmlentities($_POST['feed_encoding'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_encoding)){
		$feed_encoding  = "UTF-8";
	}
	
	$feed_num = htmlentities($_POST['feed_num'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_num)){
		$feed_encoding  = 5;

	}
	$feed_generator = htmlentities($_POST['feed_generator'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_generator)){
		$feed_generator = "getsimple_rss_generator";
	}
	
	$feed_icon = htmlentities($_POST['feed_icon'], ENT_QUOTES, 'UTF-8');
	if(empty( $feed_icon)){
		$feed_icon =  SITE_URL . "plugins/extended_news_manager/icons/rss_feed.gif";
	}
  	$feed_enable = htmlentities($_POST['feed_enable'], ENT_QUOTES, 'UTF-8');

  	$xml = @new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><item></item>');
  
	$xml->addChild('menu_title', $menu_title);
	$xml->addChild('menu_title_lenght', $menu_title_lenght);
	$xml->addChild('menu_content_lenght', $menu_content_lenght);
	$xml->addChild('menu_news_num', $menu_news_num);
	$xml->addChild('menu_man', $menu_man);
	$xml->addChild('news_page_num', $news_page_num);
	$xml->addChild('navigation_type', $navigation_type);
	$xml->addChild('news_page', $news_page);
	$xml->addChild('external_comments', $external_comments);
	$xml->addChild('feed_title', $feed_title);
	$xml->addChild('feed_image', $feed_image);
	$xml->addChild('feed_description', $feed_description);
	$xml->addChild('feed_language', $feed_language);
	$xml->addChild('feed_encoding', $feed_encoding);
	$xml->addChild('feed_num', $feed_num);
	$xml->addChild('feed_generator', $feed_generator);
	$xml->addChild('feed_icon', $feed_icon);
	$xml->addChild('feed_enable', $feed_enable);

	XMLsave($xml, RSS_SETTINGS . 'settings.xml');
	if (!is_writable(RSS_SETTINGS . 'settings.xml'))
    	echo '<div class="error">'. i18n_r('extended_news_manager/S_SAVE_ERROR') . '</div>';
  	else
    	echo '<div class="updated">'. i18n_r('extended_news_manager/S_SAVE_SUCC') . '</div>';
		
	create_news_rss();
	settings();
	
}

/*******************************************************
 * @function create_news_rss
 * @param $num - number of news items in RSS Feed 
 * @action create feed.xml
 */ 
function create_news_rss(){

	$news = get_news(NEWS_DATA);
	$news = array_values($news);
	if(!file_exists(NEWS_RSS)){
		$create_success = create_directory(NEWS_RSS_DIR);
	 	if(!$create_success){
			echo '<h3>'. i18n_r('extended_news_manager/PLUGINTITLE') .'</h3><p>'. i18n_r('extended_news_manager/RSS_C_DIR_ERROR') .'</p>';
	 	}
	 	
	}
	
	$feed_settings = getXML(RSS_SETTINGS . 'settings.xml');

	if($feed_settings->feed_enable == 'on'){
		
		$feed_title = $feed_settings->feed_title;
		$feed_description = $feed_settings->feed_description;
		$feed_date =  urlencode(date("D, d M Y H:i:s T", time()));
		$feed_image =  $feed_settings->feed_image;
		$encoding  = $feed_settings->feed_encoding;
		$language  = $feed_settings->feed_language;
		$feed_link = NEWS_RSS;
		$generator = $feed_settings->feed_generator;
		$version   = "2.0";
		$num = $feed_settings->feed_num;
	
		if($num < count($news)){
			$news =  array_slice($news, 0, $num);
		}
		$rss = "";
		// header
		$rss .= "<?xml version=\"1.0\" encoding=\"".$encoding."\"?>\n";
		$rss .= "<rss version=\"".$version."\">\n";
		$rss .= "\t<channel>\n";
		$rss .= "\t\t<title><![CDATA[".$feed_title."]]></title>\n";
		$rss .= "\t\t<description><![CDATA[".$feed_description."]]></description>\n";
		$rss .= "\t\t<link>". $feed_link ."</link>\n";
		// header image
		$rss .= "\t\t<image>\n";
		$rss .= "\t\t\t<title>". $feed_title ."</title>\n";
		$rss .= "\t\t\t<link>". $feed_link ."</link>\n";
		$rss .= "\t\t\t<url>". $feed_image ."</url>\n";
		$rss .= "\t\t</image>\n";
	
		$rss .= "\t\t<language>". $language ."</language>\n";
		$rss .= "\t\t<date>". $feed_date ."</date>\n";
		$rss .= "\t\t<generator>". $generator ."</generator>\n";
		
		//items

		foreach($news as $news_item) {
		$link = '';
		    $id = basename($news_item, '.xml');
			$data=getXML(NEWS_DATA . $news_item );
	
			if(PRETTY_URLS == '1'){	 
			 	$link = urlencode($data->title);
			 }else{
				 $link = "news=$id";
			 }

			$url = url_builder($link, $feed_settings->news_page);

       		$title = stripslashes(htmlspecialchars_decode($data->title, ENT_QUOTES));
       		$excerpt = stripslashes(htmlspecialchars_decode($data->excerpt, ENT_QUOTES));
			
		 	if(empty($excerpt)){
				$excerpt = substr($data->content, 0, 500).' ...';
			}
	
		    $rss.="\t\t<item>\n";
			$rss .= "\t\t\t<title><![CDATA[". $title."]]></title>\n";
		    $rss.="\t\t\t<description><![CDATA[". $excerpt ."]]></description>\n";
			$rss.="\t\t\t<pubDate>". $data->date ."</pubDate>\n";
			$rss.="\t\t\t<link>". $url ."</link>\n";
			$rss .= "\t\t</item>\n";
		}

		//footer
		$rss .= "\t</channel>\n";
		$rss .= "</rss>\n";

	  	if (!file_put_contents(NEWS_RSS, $rss)){
    		echo '<div class="">'. i18n_r('extended_news_manager/RSS_FILE_ERROR') .'</div>';
	  	}
	}
}


############################### SITE FUNCTIONS #################################

/*******************************************************
 * @function news_css
 * @action include css file depending of theme used, if css file do not exist use plugin default css file
 */
function news_css(){
	
	$data = @getXML(SETTINGS_PATH);
	$theme = @stripslashes($data->TEMPLATE);
	$css = 'plugins/extended_news_manager/css/extended_news_'.$theme.'.css';
	$theme_css = SITE_URL . 'plugins/extended_news_manager/css/extended_news_'.$theme.'.css';
	$default_css = SITE_URL .  'plugins/extended_news_manager/css/extended_news.css';
	
	if(file_exists($css)){
		echo '<link href="'. $theme_css .'" rel="stylesheet">';
	}else{
		echo '<link href="'. $default_css .'" rel="stylesheet">';
	}	
}

function admin_js(){
	
	echo '<script type="text/javascript" src="'.SITE_URL.'plugins/extended_news_manager/js/extended_news_manager.js"></script>';
	
}
/*******************************************************
 * @function show_news
 * @param $n - number of news per page
 * @action displays news news on theme/site page  
 */
function show_news() {
	if (isset($_GET['news'])){
		
		$id = $_GET['news'];
		$file_name = urlencode($id);
		show_news_item($file_name);
		
	}else{
		
    	$news = get_news(NEWS_DATA);
		$settings = getXML(RSS_SETTINGS . 'settings.xml'); 
		$num_news = stripslashes(htmlspecialchars_decode($settings->news_page_num));
		if (!empty($news)) {
			
			if (isset($num_news) && $num_news > 0) {
				$index = isset($_GET['page']) ? $_GET['page'] : 0;
				$count_pages = count($news);
				$pages = array_chunk($news, $num_news);
			
				if (is_numeric($index) && $index >= 0 && $index < sizeof($pages)){
					$news = $pages[$index];
				}else{
					$news = array();
				}
			}
			
			foreach ($news as $news_item) {
				$id = basename($news_item, '.xml');
				show_news_item($id, TRUE);
			}
			
			if (!empty($news) && isset($pages) && sizeof($pages) > 1){
				show_navigation($index, sizeof($pages), $count_pages);
			}
  		}
	}
}

/*******************************************************
 * @function show_news_item
 * param $id - unique news id
 * param $excerpt - if TRUE, print only a short news summary
 * @action prints the news with given id on theme/site page
 */
function show_news_item($id, $excerpt=FALSE) {

  $file = NEWS_DATA . $id. '.xml';
  $plugin_settings = getXML(RSS_SETTINGS . 'settings.xml');

  $data = getXML($file);
  if (!empty($data)) {
    $date = $data->date;
    $title = stripslashes(htmlspecialchars_decode($data->title, ENT_QUOTES));
    $content = stripslashes(htmlspecialchars_decode($data->content, ENT_QUOTES));

	$link = urlencode($title);
    if ($excerpt && !empty($data->excerpt)) {
      $content = '<p>' . stripslashes(htmlspecialchars_decode($data->excerpt, ENT_QUOTES)) . '</p>';
    }

	if(PRETTY_URLS == '1'){
		$link = urlencode($data->title);
	}else{
		$link = "news=$id";
	}
	$url = url_builder($link, $plugin_settings->news_page);
	$text = '
    <div class="ext_news">
		<div class="ext_news_title"><a href="'. $url .'">'. $title .'</a></div>
      	<div class="ext_news_date">'. i18n_r('extended_news_manager/NEWS_DATE') .': '. $date .'</div>
      	<div class="ext_news_content">'. $content .'</div>';  
	  
      if ($excerpt && !empty($data->excerpt)){
        	$text .= '<p class="ext_news_link"><a href="'. $url .'">'. i18n_r('extended_news_manager/NEWS_READ_MORE') .'</a></p>';
	
	  }elseif (!$excerpt){
       		$text .= '<div class="ext_news_permanent"><b>'. i18n_r('extended_news_manager/NEWS_PERMANENT') .':</b> <span class="news_permanent_link"><a href="'. $url .'">'. $url .'</a></span></div>';
	  }
	      $configfile=GSDATAOTHERPATH . 'external_comments.xml';
		  $external_comments = getXML($configfile); 

       if (isset($_GET['news']) && $plugin_settings->external_comments == 'on' && isset($external_comments->provider)) {
            $text .= '<div class="ext_news_comments">
			<div class="ex_comments">'. i18n_r('extended_news_manager/COMMENTS_INFO') .'</div>
			<div id="ext_comments_providers">
<a href="http://facebook.com" >
<img alt="facebook_logo" src="'.COMMENT_PROV.'facebook.gif" style="width: 60px; height: 18px;" /></a>
<a href="http://intensedebate.com/" >
<img alt="intensedebate_logo" src="'.COMMENT_PROV.'intensedebate.gif" style="width: 60px; height: 18px;" /></a>
<a href="http://disqus.com/">
<img alt="disqus_logo" src="'.COMMENT_PROV.'disqus.gif" style="width: 60px; height: 18px;" /></a>
<a href="http://livefyre.com/">
<img alt="livefyre_logo" src="'.COMMENT_PROV.'livefyre.gif" style="width: 60px; height: 18px;" /></a>
<a href="http://vk.com" >
<img alt="template_vke_logo" src="'.COMMENT_PROV.'vk.gif" style="width: 60px; height: 18px;" /></a>
</div>
			'. return_external_comments($plugin_settings->news_page.'-', $url, $title) .'
            </div>';
		}  
    $text .= '</div>';

	} else {
		$text .= ' <p>'. i18n_r('extended_news_manager/NEWS_NOEXIST') .'</p>';
  	}
	echo  $text;
}

/*******************************************************
 * @function show_navigation
 * param $index - current page index
 * param $n - total number of pages
 * @action provides links to navigate between news pages
 */
function show_navigation($index, $n, $count_pages=false) {
  	$settings = getXML(RSS_SETTINGS . 'settings.xml'); 
	$base_url = get_page_url(TRUE);
    $url = preg_match('/\?/', $base_url) ? '&page=' : '?page=';
	$text = '';
	
	if($settings->navigation_type == 'default'){
		$text .= '<div class="ext_news_nav">';
	
		if ($index < $n - 1) {
			$text .= '<div class="alignleft"><a href="';
	  
			if(PRETTY_URLS == '1'){		
				$path = $base_url . "/older-news:page-" . ($index+1); 
			}else{
				$path = $base_url . $url . ($index+1); 
			}
	
			$text .=  $path .'">'. i18n_r('extended_news_manager/NEWS_PAG_OLD') .'</a></div>';
	
		}
	
		if ($index > 0) {
		
			$text .= '<div class="alignright"><a href="';
	  
			if(PRETTY_URLS == '1'){
				$path =  ($index > 1) ? $base_url . "/newer-news:page-" . ($index-1) : $base_url; 
			}else{
				$path = ($index > 1) ? $base_url .$url . ($index-1) : substr($base_url .$url, 0, -6); 
			}
		
			$text .= $path .'">'. i18n_r('extended_news_manager/NEWS_PAG_NEW') .'</a></div>';

  		}
		$text .= '</div><br />';
		
	}else{
		$paginate_num = ceil($count_pages/$settings->news_page_num);
			
		if(PRETTY_URLS == '1'){
			$path =  $base_url . "/news:page-"; 
		}else{
			$path =  $base_url .$url ; 
		}
			
		$text .= '<div class="paginate"><div class="ext_news_paginate">';
		for ($i = 0; $i < $paginate_num; $i++) {

			$text .= '<a  href="'.$path. $i .'"> '.$i.' </a>&nbsp; ';
		}
		$text .= '</div></div>';
		
	}
	echo $text;
}

/*******************************************************
 * @function list_news
 * @param $page_id - id of page to show 
 * @param $n - number of news
 * @action displays news news on theme/site page
 */
function list_news(){
	$plugin_settings = getXML(RSS_SETTINGS . 'settings.xml');
	$news = get_news(NEWS_DATA);
	
    if (!empty($news)) {

	$text = '
     <h2>'.  ucwords($plugin_settings->menu_title) .'</h2>
     <div class="ext_news_latest">
    	<div class="ext_news_list">';
	
      foreach ($news as $news_item) {
		  
        if ($plugin_settings->menu_news_num--<1) break;
        $id = basename($news_item, '.xml');
        $data = getXML(NEWS_DATA . $id . '.xml');
	
        $cut_title = $title = stripslashes(htmlspecialchars_decode($data->title, ENT_QUOTES));

		if (strlen($title) > $plugin_settings->menu_title_lenght){

			if($plugin_settings->menu_man == 'warp'){
				$cut_title = wordwrap($cut_title,  stripslashes(htmlspecialchars_decode($plugin_settings->menu_title_lenght)), "\n<br />", true);
			}else{
				$cut_title = substr($cut_title, 0, stripslashes(htmlspecialchars_decode($plugin_settings->menu_title_lenght))).' ...';
			}
		}

		if(PRETTY_URLS == '1'){
			$link = urlencode($title);
		}else{
			$link = "news=$id";
		}
		
		$url = url_builder($link, $plugin_settings->news_page);

	
        $text .= '<div class="ext_news_list_items">-<a href="'. $url .'">'.$cut_title.'</a></div>';
	   
		if(!empty($data->excerpt)){		
			$cut_except = strip_tags($data->excerpt);
			
	  	 	if (strlen($cut_except) > $plugin_settings->menu_content_lenght){
		
	   			if($plugin_settings->menu_man == 'warp'){
					$cut_except  = wordwrap($cut_except, stripslashes(htmlspecialchars_decode($plugin_settings->menu_content_lenght)), "<br />", true);
				}else{
					$cut_except = substr($cut_except, 0, stripslashes(htmlspecialchars_decode($plugin_settings->menu_content_lenght))).' ...';
				}
	   		}
		
	   		$text .= '<div class="ext_news_short">'.$cut_except.'</div>';
	   
		}
	}
     
  $text .= '</div></div>';
    }
	echo $text;
}

############################### SITE RSS FEED FUNCTIONS #################################

/*******************************************************
 * @function rss_feed
 * @action create rss feed link just add as page template
 */
function rss_feed() {
  	$settings = getXML(RSS_SETTINGS . 'settings.xml'); 
	if($settings->feed_enable == 'on'){
		$rss = file_get_contents(NEWS_RSS);
		echo $rss;
	}else{
		echo i18n_r('extended_news_manager/RSS_DISABLED');
	}
}

/*******************************************************
 * @function rss_link
 * @action show rss icon with link
 */
function rss_link(){
	
  	$settings = getXML(RSS_SETTINGS . 'settings.xml'); 

	if(PRETTY_URLS == '1'){
		$feed_url = SITE_URL . 'rss';
	}else{
		$feed_url = SITE_URL . 'index.php?id=rss';
	}
	echo '<a href="'. $feed_url .'"><img src="'. $settings->feed_icon  .'" alt="rss_feed" /></a>';
}


############################## COMMON FUNCTIONS ################################

/*******************************************************
 * @function get_news
 * @returns a list of news in the folder NEWS_DATA
 */
function get_news($path) {
  $news = array();
  $files = getFiles($path);
  foreach ($files as $file) {
    if (is_file($path . $file) && preg_match("/.xml$/", $file)) {
      $news[] = $file;
    }
  }
  sort($news);
  return $news;
}

/*******************************************************
 * @function url_builder
 * param $url_segment - hods data for URL build
 * @action resolve URL depending of FANCY URL use
 */
function url_builder($segment, $holder_page){

	if(PRETTY_URLS == '1'){
		$url = SITE_URL . $holder_page . '-' . $segment;
	}else{
		$url = SITE_URL . 'index.php?id='.$holder_page .'&amp;' . $segment;
	}
	return $url;
}

