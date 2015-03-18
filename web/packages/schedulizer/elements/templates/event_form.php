<form class="event container-fluid" ng-controller="CtrlEventForm" ng-submit="submitHandler()">
    <?php Loader::packageElement('templates/loading', 'schedulizer'); ?>

    <div ng-show="_ready">
        <!-- title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="" class="sr-only">Title</label>
                    <input type="text" class="form-control input-title" placeholder="Title" ng-model="entity.title" />
                </div>
            </div>
        </div>

        <!-- start and end -->
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-12">
                        <label>From</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 time-widgets" ng-class="{'is-all-day':entity.isAllDay}">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Start" bs-datepicker ng-model="entity.startUTC" data-autoclose="1" data-min-date="today" data-template="/tpl-datepicker" data-icon-left="icon-angle-left" data-icon-right="icon-angle-right" />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Time" bs-timepicker ng-model="entity.startUTC" data-autoclose="1" data-template="/tpl-timepicker" data-icon-up="icon-angle-up" data-icon-down="icon-angle-down" data-time-format="hh:mm a" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-12">
                        <label>To</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 time-widgets" ng-class="{'is-all-day':entity.isAllDay}">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="End" bs-datepicker ng-model="entity.endUTC" data-autoclose="1" data-min-date="{{entity.startUTC}}" data-template="/tpl-datepicker" data-icon-left="icon-angle-left" data-icon-right="icon-angle-right" />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Time" bs-timepicker ng-model="entity.endUTC" data-autoclose="1" data-template="/tpl-timepicker" data-icon-up="icon-angle-up" data-icon-down="icon-angle-down" data-time-format="hh:mm a" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- all day, repeating, timezone -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" value="" ng-model="entity.isAllDay" /> All Day Event
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" value="" ng-model="entity.isRepeating" /> Repeat
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" value="" ng-model="entity.useCalendarTimezone" /> Use Calendar Timezone
                    </label>
                </div>
            </div>
        </div>

        <!-- timezone -->
        <div class="row" ng-hide="entity.useCalendarTimezone">
            <div class="col-sm-12">
                <div class="form-group">
                    <span select-wrap class="block"><select class="form-control" ng-options="opt for opt in timezoneOptions" ng-model="entity.timezoneName"></select></span>
                </div>
            </div>
        </div>

        <!-- repeat how? -->
        <div ng-show="entity.isRepeating">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group form-inline">
                        Every <span select-wrap><select class="form-control" ng-options="opt as opt for opt in repeatEveryOptions" ng-model="entity.repeatEvery"></select></span>
                        <span select-wrap><select class="form-control" ng-options="opt.value as opt.label for opt in repeatTypeHandleOptions" ng-model="entity.repeatTypeHandle"></select></span>
                        <span select-wrap><select class="form-control" ng-options="opt.value as opt.label for opt in repeatIndefiniteOptions" ng-model="entity.repeatIndefinite"></select></span>
                        <input type="text" class="form-control" placeholder="Repeating End Date" ng-show="entity.repeatIndefinite == repeatIndefiniteOptions[1].value" bs-datepicker ng-model="entity.repeatEndUTC" data-autoclose="1" data-min-date="{{entity.startUTC}}" data-template="/tpl-datepicker" data-icon-left="icon-angle-left" data-icon-right="icon-angle-right" />
                    </div>
                </div>
            </div>

            <!-- repeat weekly options -->
            <div class="row" ng-show="entity.repeatTypeHandle == repeatTypeHandleOptions[1].value">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="form-inline">
                            Weekdays &nbsp;
                            <div class="btn-group" role="group">
                                <label class="btn btn-default" ng-repeat="opt in weekdayRepeatOptions" ng-class="{active:opt.checked}">
                                    {{opt.label}} <input type="checkbox" ng-model="opt.checked" ng-change="selectedWeekdays()" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- repeat monthly options -->
            <div class="row" ng-show="entity.repeatTypeHandle == repeatTypeHandleOptions[2].value">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="form-inline">
                            <label>
                                On the &nbsp;
                                <input type="radio" ng-model="entity.repeatMonthlyMethod" ng-value="repeatMonthlyMethodOptions.specific" />
                                <span select-wrap><select class="form-control" ng-options="opt as opt for opt in repeatMonthlySpecificDayOptions" ng-model="repeatSettings.monthlySpecificDay"></select></span>
                                {{ entity.repeatMonthlySpecificDay|numberContraction }} of the month,
                            </label>
                            <label>
                                or the &nbsp;
                                <input type="radio" ng-model="entity.repeatMonthlyMethod" ng-value="repeatMonthlyMethodOptions.dynamic" />
                                <span select-wrap><select class="form-control" ng-options="opt.value as opt.label for opt in repeatMonthlyDynamicWeekOptions" ng-model="repeatSettings.monthlyDynamicWeek"></select></span>
                                <span select-wrap><select class="form-control" ng-options="opt.value as opt.label for opt in repeatMonthlyDynamicWeekdayOptions" ng-model="repeatSettings.monthlyDynamicWeekday"></select></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- description -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <textarea redactorized ng-model="entity.description"></textarea>
                </div>
            </div>
        </div>

        <!-- event colors -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group text-center">
                    <label ng-repeat="opt in eventColorOptions" class="color-thumb" ng-style="{background:opt.value}" ng-class="{active:(opt.value == entity.eventColor)}">
                        <input type="radio" ng-model="entity.eventColor" ng-value="opt.value" />
                    </label>
                </div>
            </div>
        </div>

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
