<?php

function c_calendarMini() {
        if(!isset($_GET['month'])) $_GET['month'] = date('n');
        if(!isset($_GET['year'])) $_GET['year'] = date('Y');
    ?>
    <link type="text/css" href="<?php get_site_url(); ?>/plugins/calendar/css/calendarMini.css" rel="stylesheet" />
    <table id="calendarMini">
        <tr>        
            <th><?php i18n('calendar/Mo'); ?></th>
            <th><?php i18n('calendar/Tu'); ?></th>
            <th><?php i18n('calendar/We'); ?></th>
            <th><?php i18n('calendar/Th'); ?></th>
            <th><?php i18n('calendar/Fr'); ?></th>
            <th><?php i18n('calendar/Sa'); ?></th>
            <th class="sunday"><?php i18n('calendar/Su'); ?></th>
        </tr>
        <?php
            c_calendar($_GET['month'], $_GET['year'], 'load.php?id=calendar&edit=', False);
        ?>
    </table>
<?php

}

?>
