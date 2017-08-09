<?php

	$title = empty($title)? '' : $title;
	$date = empty($date)? date('d-m-Y') : $date;
	$contents = empty($contents)? '' : $contents;
	$file = empty($file) ? '' : $file;
	
    if(isset($_POST['title']) and isset($_POST['date']) and isset($_POST['post-content'])) {
        $xml = simplexml_load_file(GSPLUGINPATH.'/calendar/example.xml');
        $xml->title = $_POST['title'];
        $date = explode('-', $_POST['date']);
        $xml->date = $date[0].$date[1].$date[2];
        $xml->contents = $_POST['post-content'];
        $xml->repetition = $_POST['repetition'];
        $file = $_POST['title'];
        $file = stripslashes($file);
        $file = to7bit($file, 'UTF-8');
        $file = clean_url($file);
        $file = $file.$date[0].$date[1].$date[2];
        XMLsave($xml, GSDATAOTHERPATH.'/calendar/'.$file.'.xml');
        $_GET['edit'] = $file;
    }

    if(!empty($_GET['edit'])) {
        $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$_GET['edit'].'.xml');
 		$file = explode('.', $file);
        $file = $file[0];
		$title = $xml->title;
        $repetition = $xml->repetition;
        $date = $xml->date;
        $dateYear = substr($date, 4, 7);
        $dateMonth = substr($date, 2, 2);
        $dateDay = substr($date, 0, 2);
        $date = $dateDay.'-'.$dateMonth.'-'.$dateYear;
        $contents = $xml->contents;
    } else {
        $repetition = 'oneTime';
    }
?>
<form action="load.php?id=calendar&edit=<?php echo $_GET['edit']; ?>" method="POST">
    <p>
        <label><?php i18n('calendar/title'); ?>:</label>
        <input class="text short" type="text" name="title" value="<?php echo $title; ?>" style="width: 250px;" />
    </p>
    <p>
        <label><?php i18n('calendar/date'); ?>:</label>
            <link type="text/css" href="../plugins/calendar/css/jquery-ui-1.8.17.custom.css" rel="stylesheet" />	
            <script type="text/javascript" src="../plugins/calendar/js/jquery-ui-1.8.17.custom.min.js"></script>
            <script type="text/javascript">
                $(function(){
                    // Datepicker
                    $('#datepicker').datepicker({
                        inline: true,
                        dateFormat: 'dd-mm-yy',
                        firstDay: 1,
                        nextText: '<?php i18n('calendar/next'); ?>',
                        prevText: '<?php i18n('calendar/prev'); ?>',
                        dayNames: ['<?php i18n('calendar/Sunday'); ?>', '<?php i18n('calendar/Monday'); ?>', '<?php i18n('calendar/Tuesday'); ?>', '<?php i18n('calendar/Wednesday'); ?>', '<?php i18n('calendar/Thursday'); ?>', '<?php i18n('calendar/Friday'); ?>', '<?php i18n('calendar/Saturday'); ?>'],
                        dayNamesShort: ['<?php i18n('calendar/Su'); ?>', '<?php i18n('calendar/Mo'); ?>', '<?php i18n('calendar/Tu'); ?>', '<?php i18n('calendar/We'); ?>', '<?php i18n('calendar/Th'); ?>', '<?php i18n('calendar/Fr'); ?>', '<?php i18n('calendar/Sa'); ?>'],
                        dayNamesMin: ['<?php i18n('calendar/Su'); ?>', '<?php i18n('calendar/Mo'); ?>', '<?php i18n('calendar/Tu'); ?>', '<?php i18n('calendar/We'); ?>', '<?php i18n('calendar/Th'); ?>', '<?php i18n('calendar/Fr'); ?>', '<?php i18n('calendar/Sa'); ?>'],
                        monthNames: ['<?php i18n('calendar/January'); ?>', '<?php i18n('calendar/February'); ?>', '<?php i18n('calendar/March'); ?>', '<?php i18n('calendar/April'); ?>', '<?php i18n('calendar/May'); ?>', '<?php i18n('calendar/June'); ?>', '<?php i18n('calendar/July'); ?>', '<?php i18n('calendar/August'); ?>', '<?php i18n('calendar/September'); ?>', '<?php i18n('calendar/October'); ?>', '<?php i18n('calendar/November'); ?>', '<?php i18n('calendar/December'); ?>']
                    });
                });
            </script>
        <input class="text short" id="datepicker" type="text" name="date" value="<?php echo $date; ?>" style="width: 150px;" maxlength="10" />
    </p>
    <p>

        <label><?php i18n('calendar/repetition'); ?>:</label>

     
            <select class="text" name="repetition" style="width: 162px;">
                <?php
                $repetitions = array(
                    1 => 'oneTime',
                    2 => 'everyDay',
                    3 => 'everyWeek',
                    4 => 'everyMonth',
                    5 => 'everyYear');
                
                foreach ($repetitions as $key => $times) {
                    if($times == $repetition) 
                        echo '<option value="'.$times.'" selected="selected">'.i18n_r('calendar/'.$times).'</option>';
                    else echo '<option value="'.$times.'">'.i18n_r('calendar/'.$times).'</option>';
                }
                
                ?>
            </select>


    </p>
      <label>
    Se ha modificado el plugin para poder añadir etiquetas html, scripts ... por si alguien quiere hacer una presentacion de un evento un poco mas elaborada.
    </label>
    </p>
    <p>
        <label><?php i18n('calendar/contents'); ?>:</label>
        <script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="template/js/ckeditor/adapters/jquery.js"></script>
		<?php
			if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = i18n_r('CKEDITOR_LANG'); }
		?>
        <textarea name="post-content" id="post-content"><?php echo $contents; ?></textarea>	<script type="text/javascript">
 
	var editor = CKEDITOR.replace( 'post-content', {

	        skin : 'getsimple',

	        forcePasteAsPlainText : true,

	        language : '<?php echo $EDLANG; ?>',

	        defaultLanguage : 'en',

	        	        entities : false,

	        uiColor : '#FFFFFF',

			height: '500px',

			baseHref : '<?php echo $SITEURL; ?>',

	        toolbar : 

	        [

	        ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']			]

			,

					tabSpaces:10,

	        filebrowserBrowseUrl : 'filebrowser.php?type=all',

					filebrowserImageBrowseUrl : 'filebrowser.php?type=images',

	        filebrowserWindowWidth : '730',

	        filebrowserWindowHeight : '500'

    		});

    		CKEDITOR.instances["post-content"].on("instanceReady", InstanceReadyEvent);

				var yourText = $('#post-content').val();

				function InstanceReadyEvent() {

				  this.document.on("keyup", function () {

				  		warnme = true;

				      yourText = CKEDITOR.instances["post-content"].getData();

				      $('#cancel-updates').show();

				  });

				}

	</script> 
    </p>

    <input type="submit" class="submit" value="<?php i18n('calendar/save'); ?>" /> <?php i18n('calendar/or'); ?> <a class="cancel" href="load.php?id=calendar&delete=<?php echo $_GET['edit']; ?>" title="<?php i18n('calendar/event_delete'); ?>: <?php echo $_GET['edit']; ?>"><?php i18n('calendar/delete'); ?></a>
</form>
