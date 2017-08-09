<?php
    function c_events($month, $year, $day = null) {
        $dir = opendir(GSDATAOTHERPATH.'/calendar');
        
        while($file = readdir($dir)) {
            if($file != '.' and $file != '..'){
                $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$file);
                $date = $xml->date;
                $dateYear = substr($date, 4, 7);
                $dateMonth = substr($date, 2, 2);
                $dateDay = substr($date, 0, 2);
                if($dateMonth == $month and $dateYear == $year) {
                    if(isset($events["$dateDay"])) {
                        $events["$dateDay.".rand(0,100)] = $file;
                    } else {
                        $events["$dateDay"] = $file;
                    }
                }
            }
        }
        closedir($dir);
        
		$events = empty($events)? '' : $events;
		if ($events != null) {
		
			ksort($events);
            foreach($events as $dateDay => $file) {
				$xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$file);
				$file = explode('.', $file);
				$file = $file[0];
				$title = $xml->title;
				$date = $xml->date;
				$dateYear = substr($date, 4, 7);
				$dateMonth = substr($date, 2, 2);
				$dateDay = substr($date, 0, 2);
				?>
				<tr>
					<td class="posttitle">
						<a href="load.php?id=calendar&edit=<?php echo $file; ?>"><?php echo $title; ?></a>
					</td>
					<td style="text-align: right;">
						<span><?php echo $dateDay.'.'.$dateMonth.'.'.$dateYear ?></span>
					</td>
					<td class="secondarylink">
						<a href="" target="_blank">#</a>
					</td>
					<td class="delete">
						<a href="load.php?id=calendar&delete=<?php echo $file; ?>" class="delconfirm" title="<?php i18n('calendar/event_delete'); ?>: <?php echo $title; ?>">X</a>
					</td>
				</tr>
				<?php 
			}
		}
    }
    
?>    
<?php c_monthChange('load.php?id=calendar&events'); ?>
<table id="posts" class="highlight">
    <tr>
        <th><?php i18n('calendar/event_title'); ?></th>
        <th style="text-align: right;"><?php i18n('calendar/date'); ?></th>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <?php
            if(!isset($_GET['month'])) $_GET['month'] = date('m');
            if(!isset($_GET['year'])) $_GET['year'] = date('Y');
            c_events($_GET['month'], $_GET['year']);
        ?>
    </tr>
</table>
