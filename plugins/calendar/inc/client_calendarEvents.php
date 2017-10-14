<?php
//modificado
function c_calendarEvents($numberOE = False) {
    # Tworzenie zmiennych
    if(!isset($_GET['month'])) $_GET['month'] = date('n');
    if(!isset($_GET['year'])) $_GET['year'] = date('Y');
    
    $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar.xml');
    $page = $xml->page;
    $url = 'index.php?id='.$page;
    
    $events = array();
    $number = 1;
    if($numberOE == False) $numberOE = 100;
    
    # Wczytywanie zdarzenia, jeżeli trzeba
    if(isset($_GET['event']) and !empty($_GET['event'])) {
        $event = $_GET['event'];
        $path = GSDATAOTHERPATH.'/calendar/'.$event.'.xml';
        if(file_exists($path)) {
            $xml = simplexml_load_file($path);
            $title = $xml->title;
            $date = $xml->date;
            $dateYear = substr($date, 4, 7);
            $dateMonth = substr($date, 2, 2);
            $dateDay = substr($date, 0, 2);
            $date = $dateDay.'-'.$dateMonth.'-'.$dateYear;
            $contents = $xml->contents;
            echo '<h4>'.$title.'</h4>';
            echo $date;
            echo '<br />'.$contents;
        }
    }
    
    # Wczytywanie zdarzeń
    $dir = opendir(GSDATAOTHERPATH.'/calendar');
    while($file = readdir($dir)) {
        if($file != '.' and $file != '..'){
            $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$file);
            $date = $xml->date;
            $dateYear = substr($date, 4, 7);
            $dateMonth = substr($date, 2, 2);
            $dateDay = substr($date, 0, 2);
            $actualDay=date('d');
            $actualMonth=date('m');
            $actualYear=date('Y');

	    //modificado para mostrar unicamente proximos eventos
            if($number <= $numberOE and $dateMonth >= $actualMonth and $dateYear == $actualYear and $dateDay >= $actualDay) {
                if(isset($events["$dateDay"])) {
                    $events["$dateDay.".rand(0,100)] = $file;
                } else {
                    $events["$dateDay"] = $file;
                }
                $number++;
            }
        }
    }
    closedir($dir);

    ksort($events);
    echo "<b> Próximos Eventos: </b";
	
    foreach($events as $dateDay => $file) {
        $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$file);
        $file = explode('.', $file);
        $file = $file[0];
        $title = $xml->title;
        $date = $xml->date;
        $dateYear = substr($date, 4, 7);
        $dateMonth = substr($date, 2, 2);
        $dateDay = substr($date, 0, 2);
        if($dateDay == date('j')) $class = 'class="today"'; else $class = '';
	//el plugin tenia un fallo, redirigia al calendario con la fecha actual en vez de la del evento
        $events_list .= '<tr '.$class.'>
            <td>
                <a href="'.$url.'&event='.$file.'&month='.$dateMonth.'&year='.$dateYear.'">'.$title.'</a>
            </td>
            <td class="date">
                <span>'.$dateDay.'.'.$dateMonth.'.'.$dateYear.'</span>
            </td>
        </tr>';
    }
    # Dołączanie zdarzeń do tabeli/listy
    ?>
    <link type="text/css" href="<?php get_site_url(); ?>/plugins/calendar/css/calendarEvents.css" rel="stylesheet" />
    <?php 
   // c_monthChange($url); # Zmiana aktualnego miesiąca
    ?>
    <table id="calendarEvents">
        <tr>
            <th class="title"><?php i18n('calendar/event_title'); ?></th>
            <th class="date"><?php i18n('calendar/date'); ?></th>
        </tr>
        <?php
            echo $events_list;
        ?>
    </table>
    <?php
}
?>
