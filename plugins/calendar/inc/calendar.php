<?php

function c_firstDay($month, $year) {
    $day = date('N', mktime(0,0,0,$month,1,$year));
    return $day;
}

function c_monthDays($month, $year) {
    $days = 31;
    while (!checkdate($month, $days, $year)) $days--;
    return $days;
}

function c_monthChange($where) {
    if(!isset($_GET['month']) or empty($_GET['month'])) $_GET['month'] = date('n');
    if(!isset($_GET['year']) or empty($_GET['year'])) $_GET['year'] = date('Y');
    
    $months = array('', i18n_r('calendar/January'), i18n_r('calendar/February'), i18n_r('calendar/March'), i18n_r('calendar/April'), i18n_r('calendar/May'), i18n_r('calendar/June'), i18n_r('calendar/July'), i18n_r('calendar/August'), i18n_r('calendar/September'), i18n_r('calendar/October'), i18n_r('calendar/November'), i18n_r('calendar/December')); 
    
    $month_m = $_GET['month'] - 1;
    $month_p = $_GET['month'] + 1;
    $year_m = $_GET['year'] - 1;
    $year_p = $_GET['year'] + 1;
    
    echo '<center>';
    if($_GET['month'] == 1) {
        $month_m = 12;
        echo '<a href="'.$where.'&month='.$month_m.'&year='.$year_m.'">'.$months[$month_m].' '.$year_m.'</a>';
    } else {
        echo '<a href="'.$where.'&month='.$month_m.'&year='.$_GET['year'].'">'.$months[$month_m].' '.$_GET['year'].'</a>';
    }
    
    echo ' << '.$months[$_GET['month']].' '.$_GET['year'].' >> ';
    
    if($_GET['month'] == 12) {
        $month_p = 1;
        echo '<a href="'.$where.'&month='.$month_p.'&year='.$year_p.'">'.$months[$month_p].' '.$year_p.'</a>';
    } else {
        echo '<a href="'.$where.'&month='.$month_p.'&year='.$_GET['year'].'">'.$months[$month_p].' '.$_GET['year'].'</a>';
    }
    echo '</center>';
}

function c_repetitionCheck($repetition, $dateYear, $dateMonth, $dateDay, $day, $month, $year) {
    switch ($repetition) {
        case 'everyDay':
            return True;
            break;
        case 'everyWeek':
            $dateDay = $dateDay + 7;
            if($dateDay == $day and $dateMonth == $month and $dateYear == $year) return True;
            $dateDay = $dateDay + 7;
            if($dateDay == $day and $dateMonth == $month and $dateYear == $year) return True;
            $dateDay = $dateDay + 7;
            if($dateDay == $day and $dateMonth == $month and $dateYear == $year) return True;
            $dateDay = $dateDay + 7;
            if($dateDay == $day and $dateMonth == $month and $dateYear == $year) return True;
            break;
        case 'everyMonth':
            for($x = $month; $x <= 12; $x++) {
                if($x == $month and $dateDay == $day and $dateYear == $year) return True;
            }
            break;
        case 'everyYear':
            if($month == $dateMonth and $dateDay == $day) return True;
            break;
    }
}

function c_calendar($month, $year, $link, $eventsIF = True) {
    echo '<tr>';
    $week = 1;
    
    for($index = 1; $index < c_firstDay($month, $year); $index++) {
        echo '<td class="none"></td>';
        $week++;
    }
    
    for ($index1 = 1; $index1 <= c_monthDays($month, $year); $index1++) {
        # dodawanie zdarzeń
        $dir = opendir(GSDATAOTHERPATH.'/calendar');   
        while($file = readdir($dir)) {
            if($file != '.' and $file != '..'){
                $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar/'.$file);
                $file = explode('.', $file);
                $file = $file[0];
                $title = $xml->title;
                $date = $xml->date;
                $repetition = $xml->repetition;
                $dateYear = substr($date, 4, 7);
                $dateMonth = substr($date, 2, 2);
                $dateDay = substr($date, 0, 2);
                $day = $index1;
                if(strlen($day) == '1') $day = '0'.$day;
                if($dateDay == $day and $dateMonth == $month and $dateYear == $year or c_repetitionCheck($repetition, $dateYear, $dateMonth, $dateDay, $day, $month, $year) == True) {
                    # czy mam pokazywać wydarzenia
                    if($eventsIF != False) {
                        if(strlen($title) > 12) {
                            $title = $title;
                        }
						$events = empty($events) ? '' : $events;
                        $events .= '<br />'.'<a href="'.$link.$file.'&month='.$_GET['month'].'&year='.$_GET['year'].'">'.$title.'</a>';
                    } elseif($eventsIF == False) {
                        $events = True;
                    }
                }
            }
        }
        closedir($dir); 
        
        if(isset($events) and ($events == True and $eventsIF == False)) {
            $css = 'class="eventIS"';
            $events = '';
            $xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar.xml');
            $calendarPage = $xml->page;
            $xml = simplexml_load_file(GSDATAPAGESPATH.'/'.$calendarPage.'.xml');
            $calendarPage = $xml->url;
            $li = '<a href="index.php?id='.$calendarPage.'" >';
            $nk = '</a>';
        }
        
        # jeżeli to jest dziś
		$css = empty($css) ? '' : $css;
		$events = empty($events) ? '' : $events;
		$li = empty($li) ? '' : $li;
		$nk = empty($nk) ? '' : $nk;
        if($index1 == date('j') and $month == date('n')) {
            if($css == 'class="eventIS"') $css = 'class="today eventIS"'; else
                $css = 'class="today"';
            echo '<td class="today" '.$css.'>'.$li.$index1.$nk.$events.'</td>';
        } else {
            # Sprawdzanie, czy dziś niedziela
            $css = empty($css) ? '' : $css;
			if(date('w', mktime(0,0,0,$month,$index1,$year)) == 0){
                if($css == 'class="eventIS"') $css = 'class="redDay eventIS"'; else
                    $css = 'class="redDay"';
                echo '<td '.$css.'>'.$li.$index1.$nk.$events.'</td>';
            } else {
						echo '<td '.$css.'>'.$li.$index1.$nk.$events.'</td>';
					}
        }
        
        # usuwanie zdarzeń
        unset($events);
        if(isset($css)) unset($css, $li, $nk, $calendarPage, $xml);
        
        # rozpoczynanie nowych tygodni
        if($week == 7) {
            echo '</tr><tr>';
            $week = 0;
        }
        
        $week++;
    }
    
    echo '</tr>';
}
?>
