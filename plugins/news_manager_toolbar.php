<?php
/*

"News Manager Toolbar" plugin

Integrates News Manager with the SA GS Admin Toolbar. Adds top level links to:
- News Manager main admin page
- Create new post
- Edit post (if viewing a post in the frontend)

Requires:
- SA GS Admin Toolbar <http://get-simple.info/extend/plugin/sa-gs-admin-toolbar/483>
- News Manager <http://get-simple.info/extend/plugin/news-manager-updated/541/>

*/
	
# register plugin
$thisfile = basename(__FILE__, ".php");
register_plugin(
	$thisfile,
	'News Manager Toolbar',
	'0.1.2',
	'Carlos Navarro',
	'http://www.cyberiada.org/cnb/',
	'SA GS Admin Toolbar + News Manager integration'
);

add_action('sa_toolbar_disp','nm_sa_toolbar');

function nm_sa_toolbar(){
	global $SATB_MENU_STATIC, $fullpath, $GSADMIN, $NMPAGEURL, $id;
	$SATB_MENU_STATIC['news'] = array('title'=> i18n_r('news_manager/PLUGIN_NAME'),'url'=>$fullpath.$GSADMIN.'/load.php?id=news_manager');
	$SATB_MENU_STATIC['add_news'] = array('title'=> '+ '.i18n_r('news_manager/NEW_POST'),'url'=>$fullpath.$GSADMIN.'/load.php?id=news_manager&edit');
	if ($id == $NMPAGEURL) {
		unset($SATB_MENU_STATIC['edit']);
		if (isset($_GET['post'])) {
			$slug = htmlentities($_GET['post']); // simple filter
			# no path traversal and post exists?
			if (dirname(realpath(NMPOSTPATH.$slug.'.xml')) == realpath(NMPOSTPATH) && file_exists(NMPOSTPATH.$slug.'.xml')) {
				$SATB_MENU_STATIC['edit_news'] = array('title'=> i18n_r('news_manager/EDIT_POST'),'url'=>$fullpath.$GSADMIN.'/load.php?id=news_manager&edit='.$slug);
			}
		}
	}
}
