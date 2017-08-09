<?php

  $page_file= @$_GET['pc_url'];
  global $SITEURL;
  $ruta = GSDATAPAGESPATH;
  $pcm_file= GSDATAOTHERPATH.'pages_comments/pc_manager.xml';

  if(!is_dir(GSDATAOTHERPATH.'pages_comments')){
	@mkdir(GSDATAOTHERPATH.'pages_comments', 0755);
  }

  //if not exists file of settings=> create this file
  if (!file_exists(GSDATAOTHERPATH.'pages_comments/pc_settgs.xml')) {
       echo 'entro_Arriba_<br />';
       $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><entry></entry>');
       $thislog = $xml->addChild('sett');
            $cdata = $thislog->addChild('nclang','en_US');   //ndVal=0 nc language
            $cdata = $thislog->addChild('ncemail', ' ');  //ndVal=1 email where send notify
            $cdata = $thislog->addChild('ncffpost','1-%d.%m.%Y %T'); //ndVal=2 format date for post or comments
            $cdata = $thislog->addChild('phpmailer','N');  //ndVal=3 class phpmailer
            $cdata = $thislog->addChild('emot','Y');   //ndVal=4 emoticons Yes
            $cdata = $thislog->addChild('capt','Y');   //ndVal=5 captcha Yes
            $cdata = $thislog->addChild('ord','D');    //ndVal=6 order 'D':Descending or 'A':Ascending
            $cdata = $thislog->addChild('moder','N');   //ndVal=7 comments moderateds
            $cdata = $thislog->addChild('npag','4');   //ndVal=8 number comments by page
            $cdata = $thislog->addChild('nmusr','N');   //ndVal=9 works with front end user
            $cdata = $thislog->addChild('pcuserdel','N');  //ndVal=10 del comments by user
            $cdata = $thislog->addChild('ncom_rctpost','20');  //ndVal=11 number of comments for recent posts, by default 20
            $cdata = $thislog->addChild('pcom_public','Y');  //ndVal=12 public comments, by default Yes
            $cdata = $thislog->addChild('pcom_reply','Y');  //ndVal=13 allow replies, by default Yes
            $cdata = $thislog->addChild('pcom_reply_hd','Y');  //ndVal=14 Replies hidden, by default Yes 
            $cdata = $thislog->addChild('pcom_nwords','0');  //ndVal=15 nº of words for comments (0 no limits)
            $cdata = $thislog->addChild('pcom_blacklist','N');  //ndVal=16 blacklist, by default No  
            $cdata = $thislog->addChild('pcom_words_bl',' ');  //ndVal=17 words separated by comma of blacklist
            $cdata = $thislog->addChild('pcom_rating','N');  //ndVal=18 comments ratings, by default No
            $cdata = $thislog->addChild('pcom_rating_tp','hand1');  //19 type of comments ratings, by default hand1
            $cdata = $thislog->addChild('pcom_social','N');  //ndVal=20 support social media, by default N
            $cdata = $thislog->addChild('pcom_social_tp',' ');  //ndVal=21 type of social media separated by comma
            $cdata = $thislog->addChild('pcom_report_ina','N');  //ndVal=22 report inappropriate comment, by default N
            $cdata = $thislog->addChild('pcom_report_lab',' ');  //ndVal=23 label for inappropriate comment
            $cdata = $thislog->addChild('pcom_notify','N');  //ndVal=24 get notification if update, by default N
            $cdata = $thislog->addChild('integ_NM','N');  //ndVal=25 to integrate with News Manager
            $cdata = $thislog->addChild('page_NM',' ');  //ndVal=26 page of News Manager
            $cdata = $thislog->addChild('integ_GS','N');  //ndVal=27 to integrate with GS
            $cdata = $thislog->addChild('page_GS',' ');  //ndVal=28 page of GS
            XMLsave($xml, GSDATAOTHERPATH.'pages_comments/pc_settgs.xml');            

  }


  if (file_exists(GSDATAPAGESPATH.$page_file) && substr($page_file, -4) != '.NMG') {
        $data = getXML(GSDATAPAGESPATH.$page_file);
        $title = stripslashes($data->title);
        $pubdate =  $data->pubDate;     
        $pubdateseg = strftime('%s', strtotime($data->pubDate));
  } else {
        if (substr($page_file, -4) == '.NMG'){
		//search data  in posts.xml of NM
		$domDocument = new DomDocument();
		$domDocument->load( GSDATAOTHERPATH.'news_manager/posts.xml');	
		//DOMXPath to filter
		$xpath = new DOMXPath($domDocument);
		$verN = $xpath->query('item[slug="'.substr($page_file, 0, -4).'"]');
		$title = $verN->item(0)->getElementsByTagName( "title" )->item(0)->nodeValue;
		$title = stripslashes($title).'</span><span style="color:#777777;"> ('.i18n_r('pages_comments/pc_postsNM').')';
		$pubdate = $verN->item(0)->getElementsByTagName( "date" )->item(0)->nodeValue;
		$pubdateseg = strftime('%s', strtotime($pubdate));
	}
  }
 
  echo '<br />';

  echo '<span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_pg').': '.$title.'</span><br /><br />';

  $numerg[0]=0; 
  $vemot = 'Y';
  $capt = 'Y';
  $moder = 'N';
  $ncusr = 'N';
  $cada = '6';
  $eleg = 'D';
  $pcompublic = 'Y';
  $pcomreply = 'Y'; 
  $pcomreplyhd = 'Y';
  $pcomnwords = '0';
  $pcomblacklist = 'N';  
  $pcomwordsbl = ' ';
  $pcomrating = 'N';
  $pcomratingtp = 'hand1';
  $pcomsocial = 'N';
  $pcomsocialtp = ' ';
  $pcomreportina = 'N';
  $pcomreportlab = ' ';
  $pcomnotify = 'N';

  	//read default data for comments of pc_settgs.xml
	if (file_exists(GSDATAOTHERPATH.'pages_comments/pc_settgs.xml')){
			$domDocument = new DomDocument();
			$domDocument->load(GSDATAOTHERPATH.'pages_comments/pc_settgs.xml');
			$xpath = new DOMXPath($domDocument);
			$verN = $xpath->query('sett');			
			$num = $verN->length;
			if ($num > 0){	
				$dNdList = $verN->item(0)->getElementsByTagName( "emot" );
					$vemot = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "capt" );
					$capt = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "moder" );
					$moder = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "nmusr" );
					$ncusr = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "npag" );
					$cada = $dNdList->item(0)->nodeValue;
				$dNdList = $verN->item(0)->getElementsByTagName( "ord" );
					$eleg = $dNdList->item(0)->nodeValue; 
				//$cdata = $thislog->addChild('pcom_public','Y');  //ndVal=12 public comments, by default Yes
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_public" );
					$pcompublic = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_reply','Y');  //ndVal=13 allow replies, by default Yes 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_reply" );
					$pcomreply = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_reply','Y');  //ndVal=14 Replies hidden, by default Yes 
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_reply_hd" );
					$pcomreplyhd = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_nwords','0');  //ndVal=15 nº of words for comments (0 no limits)
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_nwords" );
					$pcomnwords = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_blacklist','N');  //ndVal=16 blacklist, by default No  
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_blacklist" );
					$pcomblacklist = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_words_bl',' ');  //ndVal=17 words separated by comma of blacklist
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_words_bl" );
					$pcomwordsbl = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_rating','N');  //ndVal=18 comments ratings, by default No
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_rating" );
					$pcomrating = $dNdList->item(0)->nodeValue; 
				//  $cdata = $thislog->addChild('pcom_rating_tp','1');  //ndVal=19 type of comments ratings, by default hand1
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_rating_tp" );
					$pcomratingtp = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_social','N');  //ndVal=20 support social media, by default N
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_social" );
					$pcomsocial = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_social_tp',' ');  //ndVal=21 type of social media separated by comma
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_social_tp" );
					$pcomsocialtp = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_report_ina','N');  //ndVal=22 report inappropriate comment, by default N
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_report_ina" );
					$pcomreportina = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_report_lab',' ');  //ndVal=23 label for inappropriate comment
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_report_lab" );
					$pcomreportlab = $dNdList->item(0)->nodeValue; 
				// $cdata = $thislog->addChild('pcom_notify','N');   //ndVal=24 notify if there is an update of comment
				$dNdList = $verN->item(0)->getElementsByTagName( "pcom_notify" );
					$pcomnotify = $dNdList->item(0)->nodeValue; 
			}
	}




  //ckeck if file pc_manager exists
  if ( ! file_exists($pcm_file) ) {
   	 $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><entry></entry>');
  } else {
  	//check if exists entry
	  $domDocument = new DomDocument();
	  $domDocument->load($pcm_file);

	//DOMXPath to filter by group=1 (by default)
  	  $xpath = new DOMXPath($domDocument);

	  $verN = $xpath->query('page[url="'.$page_file.'"]');
	  $num = $verN->length;
	  if ($num > 0){
		  echo i18n_r('pages_comments/pc_pagyasav').'<br />';
		  exit;
	  }

		//read data from pc_manager.xml
	    $xmldata = file_get_contents($pcm_file);
	    $xml = new SimpleXMLExtended($xmldata);
  }
  //save data: add to pc_manager.xml
  // page that has system comments
  $thislog = $xml->addChild('page');
  $cdata = $thislog->addChild('url');
           $cdata->addCData($page_file);
  $thislog->addChild('com','Y');
  $thislog->addChild('emot',$vemot);
  $thislog->addChild('capt',$capt);
  $thislog->addChild('npag',$cada);
  $thislog->addChild('ord',$eleg);
  $thislog->addChild('moder',$moder);
  $thislog->addChild('nmusr',$ncusr);
  $thislog->addChild('pcom_public',$pcompublic);  //ndVal=12 public comments, by default Yes
  $thislog->addChild('pcom_reply',$pcomreply);  //ndVal=13 allow replies, by default Yes 
  $thislog->addChild('pcom_reply_hd',$pcomreplyhd);  //ndVal=14 Replies hidden, by default Yes 
  $thislog->addChild('pcom_nwords',$pcomnwords);  //ndVal=15 nº of words for comments (0 no limits)
  $thislog->addChild('pcom_blacklist',$pcomblacklist);  //ndVal=16 blacklist, by default No  
  $thislog->addChild('pcom_words_bl',$pcomwordsbl);  //ndVal=17 words separated by comma of blacklist
  $thislog->addChild('pcom_rating',$pcomrating);  //ndVal=18 comments ratings, by default No
  $thislog->addChild('pcom_rating_tp',$pcomratingtp);  //ndVal=19 type of comments ratings, by default hand1
  $thislog->addChild('pcom_social',$pcomsocial);  //ndVal=20 support social media, by default N
  $thislog->addChild('pcom_social_tp',$pcomsocialtp);  //ndVal=21 type of social media separated by comma
  $thislog->addChild('pcom_report_ina',$pcomreportina);  //ndVal=22 report inappropriate comment, by default N
  $thislog->addChild('pcom_report_lab',$pcomreportlab);  //ndVal=23 label for inappropriate comment
  $thislog->addChild('pcom_notify',$pcomnotify);  //ndVal=24 get notification if update, by default N

  $cdata = $thislog->addChild('titleform'); //title of form
           $cdata->addCData('');
  $thislog->addChild('pubdate', $pubdate);
  $thislog->addChild('pubdateseg', $pubdateseg);
  XMLsave($xml, $pcm_file);

  echo '<br /><span style=" text-align: center; font-family: Georgia,Times,Times New Roman,serif; font-size: 16px; font-weight: bold;">'.i18n_r('pages_comments/pc_pagsav').'</span><br /><br >';
  echo '<a href="load.php?id=pages_comments&action=edt_pages&pc_url='.$page_file.'" style="background-color: rgb(65, 90, 102); border-radius: 5px 5px 5px 5px; color: #EEE; padding: 3px 5px; text-decoration: none;" title="'.i18n_r('pages_comments/pc_edopt').'">'.i18n_r('pages_comments/pc_edopt').'</a>';

?>
