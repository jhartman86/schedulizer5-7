<?php /** @var $calendarObj \Concrete\Package\Schedulizer\Src\Calendar || null */ ?>
<form class="calendar container-fluid" ng-controller="CtrlCalendarForm" ng-submit="submitHandler()">
    <?php Loader::packageElement('templates/loading', 'schedulizer'); ?>

    <div ng-show="_ready">
        <!-- title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">Title</label>
                    <input type="text" class="form-control input-title" placeholder="Title" ng-model="entity.title" />
                </div>
            </div>
        </div>

        <!-- timezone -->
        <div class="row">
            <div class="col-sm-12">
                <label>Calendar Timezone</label>
                <div class="form-group white">
                    <span select-wrap class="block"><select class="form-control" ng-options="opt for opt in timezoneOptions" ng-model="entity.defaultTimezone"></select></span>
                </div>
            </div>
        </div>

        <!-- permissions -->
        <?php if( is_object($calendarObj) ): $permissionsObj = new Permissions(); ?>
        <div class="row">
            <div class="col-sm-12">
                <label>Calendar Permissions</label>
                <div class="form-group">
                    <?php if( $permissionsObj->canManageCalendarPermissions() ): ?>
                        <!--<a href="<?php echo $calendarObj->getPermissionCategoryToolsUrlShim('display_list'); ?>"
                           class="btn btn-default"
                           id="data-permissions-dialog-button"
                           data-permissions-dialog-button=""
                           dialog-title="Permissions"
                           dialog-modal="true"
                           dialog-height="500"
                           dialog-width="420"
                           dialog-append-buttons="true">Manage</a>-->
                        <a ng-click="permissionModal('<?php echo $calendarObj->getPermissionCategoryToolsUrlShim('display_list'); ?>')">Manage</a>
                    <?php else: ?>
                        <strong>You do not have access.</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success btn-lg btn-block">
                    <span ng-hide="_requesting">Save</span>
                    <img ng-show="_requesting" src="<?php echo SCHEDULIZER_IMAGE_PATH; ?>spinner.svg" />
                </button>
            </div>
        </div>
    </div>
</form>