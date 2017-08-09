<?php

/*
Plugin Name: Calendar
Description: Plugin umożliwiający tworzenie prostego kalendarza
Version: 1.8
Author: Maciej Szpakowski
*/

$thisfile = basename(__FILE__, '.php');

register_plugin(
  $thisfile,
  'Calendar',
  '1.8',
  'Maciej Szpakowski',
  '',
  'Plugin to create simple calendar',
  'pages',
  'c_admin'
);

# language
$file = GSDATAOTHERPATH.'/calendar.xml';
if(!file_exists($file)) {
    # tworzenie pliku ustawień
    $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><calendar_plugin></calendar_plugin>');
    $xml->addChild('lang', 'en_US');
    XMLsave($xml, $file);
}
$xml = simplexml_load_file($file);
$lang = $xml->lang;
i18n_merge('calendar', $lang) || i18n_merge('calendar', 'en_US'); # wybieranie języka
$LANG = $lang;
unset($xml, $file, $lang);

# hooks, filters
add_action('pages-sidebar', 'createSideMenu', array($thisfile, i18n_r('calendar/calendar')));

# file check 
if(!is_dir(GSDATAOTHERPATH.'/calendar')) {
    mkdir(GSDATAOTHERPATH.'/calendar');
}

# $_GET check
if(!isset($_GET['calendar']) and !isset($_GET['settings']) and !isset($_GET['edit'])) $_GET['events'] = True;

# admin
include 'calendar/inc/calendar.php';
function c_admin() {
    include 'calendar/admin.php';
}

# client
$xml = simplexml_load_file(GSDATAOTHERPATH.'/calendar.xml');
# define('index', 'index');
if(isset($_GET['id']) && ($xml->page == $_GET['id'] or $xml->page == "index")) {
    function c_client_calendar() { 
        include 'calendar/inc/client_calendar.php'; 
    }
    add_filter('content', 'c_client_calendar');
}
# calendarMini
include 'calendar/inc/client_calendarMini.php';  
# calendarEvents
include 'calendar/inc/client_calendarEvents.php';
/*
$reflFunc = new ReflectionFunction('function_name');
print $reflFunc->getFileName() . ':' . $reflFunc->getStartLine();*/
?>
