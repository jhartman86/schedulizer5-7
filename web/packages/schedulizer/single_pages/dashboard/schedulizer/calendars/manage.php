<!-- Templates -->
<script type="text/ng-template" id="/tpl-datepicker">
<?php Loader::packageElement('templates/datepicker', 'schedulizer'); ?>
</script>
<script type="text/ng-template" id="/tpl-timepicker">
<?php Loader::packageElement('templates/timepicker', 'schedulizer'); ?>
</script>
<script type="text/ng-template" id="/event_form">
<?php Loader::packageElement('templates/event_form', 'schedulizer'); ?>
</script>
<script type="text/ng-template" id="/calendar_form">
<?php Loader::packageElement('templates/calendar_form', 'schedulizer'); ?>
</script>
<script type="text/ng-template" id="/calendry">
<?php Loader::packageElement('templates/calendry', 'schedulizer'); ?>
</script>

<div class="ccm-dashboard-header-buttons">
    <button class="btn btn-primary" modalize="/event_form" data-using="{eventObj:{calendarID:<?php echo $calendarObj->getID(); ?>}}"><?php echo t("Create Event"); ?></button>
    <button class="btn btn-default" modalize="/calendar_form" data-using="{calendarID:<?php echo $calendarObj->getID(); ?>}"><?php echo t("Calendar Settings"); ?></button>
</div>

<!-- Page view -->
<div class="schedulizer-app">
    <div class="ccm-dashboard-content-full">
        <!--<div calendar data-id="<?php echo $calendarObj->getID(); ?>" data-feed="<?php echo View::url('/_schedulizer/event/list', $calendarObj->getID()); ?>"></div>-->
        <div class="calendar-wrap" ng-controller="CtrlCalendar">
            <!-- Note: transclusion of items *inside* calendry represents the EVENT objects on the day cells. -->
            <div calendry="calendarSettings" ng-cloak>
                <a class="event-cell" modalize="/event_form" data-using="{eventObj:{id:eventObj.id}}" ng-style="{background:eventObj.color}">
                    <span class="dt">{{eventObj._moment.format('h:mm a')}}</span> {{eventObj.title}}
                </a>
            </div>
        </div>
    </div>
</div>
