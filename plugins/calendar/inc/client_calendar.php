<?php
if(!isset($_GET['month'])) $_GET['month'] = date('n');
if(!isset($_GET['year'])) $_GET['year'] = date('Y');
?>
<?php
if(isset($_GET['event']) and !empty($_GET['event'])) {
    $file = $_GET['event'].'.xml';
    $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$file);
    $title = $xml->title;
    $date = $xml->date;
    $dateYear = substr($date, 4, 7);
    $dateMonth = substr($date, 2, 2);
    $dateDay = substr($date, 0, 2);
    $date = $dateDay.'-'.$dateMonth.'-'.$dateYear;
    $contents = $xml->contents;
    echo '<h4>'.$title.'</h4>';
    echo $date.'<br />';
    echo '<br />'.$contents;
    ?>
    
<?php } ?>

   




<div class="container">
                    <div class="panel panel-default">
                        <div class="panel-body table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="
    background-color:rgba(107, 197, 226, 0.91)">
                                        <th><?php i18n('calendar/Monday'); ?></th>
                                        <th><?php i18n('calendar/Tuesday'); ?></th>
                                        <th><?php i18n('calendar/Wednesday'); ?></th>
                                        <th><?php i18n('calendar/Thursday'); ?></th>
                                        <th><?php i18n('calendar/Friday'); ?></th>
                                        <th><?php i18n('calendar/Saturday'); ?></th>
					<th><?php i18n('calendar/Sunday'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php
					$xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar.xml');
					$page = $xml->page;
					c_monthChange('index.php?id='.$page);
					c_calendar($_GET['month'], $_GET['year'], 'index.php?id='.$page.'&event=');
    				?>
                                </tbody>
                            </table>
                        </div>
        
    </div>
</div>

