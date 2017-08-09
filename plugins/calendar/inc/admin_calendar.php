<?php
if(!isset($_GET['month'])) $_GET['month'] = date('n');
if(!isset($_GET['year'])) $_GET['year'] = date('Y');
?>
<style>
    #calendar {
        margin: 0 0 0 -15px;
        color: black;
        background-color: #dddddd;
        border: 0px;
    }
    #calendar th {
        color: #404040;
        min-width: 88px;
        border: 0px;
    }
    #calendar td {
        padding: 0 0 25px 5px;
        background: #bebebe;
        height: 45px;
        border: 1px white;
    }
    #calendar td:hover {
        background: #979797;
    }
    #calendar td.none {
        background: none;
    }
    #calendar td.today {
        background: #9d9d9d;
    }
    #calendar th.sunday {
        color: #ff1e1e;
    }
</style>
<table id="calendar">
    <tr>        
        <th><?php i18n('calendar/Monday'); ?></th>
        <th><?php i18n('calendar/Tuesday'); ?></th>
        <th><?php i18n('calendar/Wednesday'); ?></th>
        <th><?php i18n('calendar/Thursday'); ?></th>
        <th><?php i18n('calendar/Friday'); ?></th>
        <th><?php i18n('calendar/Saturday'); ?></th>
        <th class="sunday"><?php i18n('calendar/Sunday'); ?></th>
    </tr>
    <?php
        c_monthChange('load.php?id=calendar&calendar');
        c_calendar($_GET['month'], $_GET['year'], 'load.php?id=calendar&edit=');
    ?>
</table>

