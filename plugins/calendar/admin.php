<h3 class="floated"> <?php if(isset($_GET['settings'])) i18n('calendar/calendar_settings'); else i18n('calendar/calendar'); ?></h3>
<div class="edit-nav clearfix">
    <a href="load.php?id=calendar&settings" <?php if(isset($_GET['settings'])) { ?>class="current"<?php } ?>><?php i18n('calendar/settings'); ?></a>
    <a href="load.php?id=calendar&events" <?php if(isset($_GET['events'])) { ?>class="current"<?php } ?>><?php i18n('calendar/events'); ?></a>
    <a href="load.php?id=calendar&calendar" <?php if(isset($_GET['calendar'])) { ?>class="current"<?php } ?>><?php i18n('calendar/calendar'); ?></a>
    <a href="load.php?id=calendar&edit" <?php if(isset($_GET['edit'])) { ?>class="current"<?php } ?>><?php i18n('calendar/add'); ?></a>
</div>
<?php 
if(isset($_GET['delete'])) {
    # Delete
    $file = GSDATAOTHERPATH.'/calendar/'.$_GET['delete'].'.xml';
    if(file_exists($file)) {
        # usunięcie
        unlink($file);
        
        # wyświetlenie komunikatu
        $msg .= i18n_r('calendar/deleteSuccess').'!'
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
}

if(isset($_GET['events'])) { 
    # Events
    include 'inc/admin_events.php';
    
} elseif(isset($_GET['calendar'])) {    
    # Calendar
    include 'inc/admin_calendar.php';
    
} elseif(isset($_GET['settings'])) { 
    # Settings
    include 'inc/admin_settings.php';
    
} elseif(isset($_GET['edit'])) { 
    # Edit Event
    include 'inc/admin_edit.php';
    
}
?>
