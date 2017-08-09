<?php 
    if(isset($_POST['page']) and isset($_POST['lang'])) {
        $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar.xml');
        $xml->lang = $_POST['lang'];
        $xml->page = $_POST['page'];
        XMLsave($xml, GSDATAOTHERPATH.'/calendar.xml');
        # wyświetlenie komunikatu
		$msg = "";
		$msg .= i18n_r('calendar/settingsChangeSuccess').'!';
        ?>
        <script type="text/javascript">
          $(function() {
            $('div.bodycontent').before('<div class="<?php echo $isSuccess ? 'updated' : 'error'; ?>" style="display:block;">'+
                    <?php echo json_encode($msg); ?>+'</div>');
            $(".updated, .error").fadeOut(500).fadeIn(500);
          });
        </script>
        <?php 
    }
    
    $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar.xml');
    $setLang = $xml->lang;
    $setPage = $xml->page;
    $setPageEvents = $xml->pageEvents;
?>
<form class="largeform" id="settings" action="load.php?id=calendar&settings" method="post" accept-charset="utf-8">
    <div class="leftsec">
        <p>
            <label><?php i18n('calendar/page_name'); ?>:</label>
            <select class="text" name="page">
                <?php
                $pages = get_available_pages();
                foreach ($pages as $page) {
                    $slug = $page['slug'];
                    if ($slug == $setPage)
                        echo '<option value="'.$slug.'" selected="selected">'.$slug.'</option>\n';
                    else
                        echo '<option value="'.$slug.'">'.$slug.'</option>\n';
                }
                ?>
            </select>
        </p>
    </div>
    <div class="rightsec">
        <p>
            <label><?php i18n('calendar/language'); ?>:</label>
            <select class="text" name="lang">
                <?php
                    $dir = opendir('../plugins/calendar/lang');
                    while($file = readdir($dir)) {
                        if($file != '.' and $file != '..' and $file != '.htaccess') {
                            $lang = explode('.', $file);
                            $lang = $lang[0];
                            switch($lang) {
                                case 'pl_PL': $langL = 'Polski';
                                    break;
                                case 'de_DE': $langL = 'Deutsch';
                                    break;
                                case 'en_US': $langL = 'American English';
                                    break;
                                case 'es_ES': $langL = 'Español';
                                    break;
                                case 'fr_FR': $langL = 'Français';
                                    break;
                                case 'it_IT': $langL = 'Italiano';
                                    break;
                                case 'nl_NL': $langL = 'Nederlands';
                                    break;
                                case 'ru_RU': $langL = 'Russisch';
                                    break;
                            }
                            $langL = $langL.' ( '.$lang.' )';
                            if ($lang == $setLang)
                                echo '<option value="'.$lang.'" selected="selected">'.$langL.'</option>\n';
                            else
                                echo '<option value="'.$lang.'">'.$langL.'</option>\n';
                        }
                    }
                    closedir($dir);
                ?>
            </select>
        </p>
    </div>
    <div class="clear"></div>
    <p>
        <span>
            <input class="submit" type="submit"value="<?php i18n('calendar/settingsSave'); ?>" />
        </span>
    </p>
    <br /><br />
    <p style="font-size: 12px;">
        To edit a calendar style edit file: <font style="color: green;">plugins/calendar/css/calendar.css</font><br />
        <br />
        To add calendar just change Page calendar in <font style="color: darkblue;">admin->pages->calendar(in sidebar)->settings.</font><br />
        <br />
        To add a mini calendar just write: <font style="color: darkred;">&lt;?php c_calendarMini(); ?&gt; </font>in your php template file. To edit a mini calendar style edit file: <font style="color: green;">plugins/calendar/css/calendarMini.css</font><br />
        <br />
        To add a events list/table just write: <font style="color: darkred;">&lt;?php c_calendarEvents(); ?&gt; </font>in your php template file. If you can show only ( for example ) first 5 events, you just write: <font style="color: darkred;">&lt;?php c_calendarEvents(5); ?&gt;</font><br />
        <br />
        To edit a events list/table style edit file: <font style="color: green;">plugins/calendar/css/calendarEvents.css</font><br />
    </p>
</form>
