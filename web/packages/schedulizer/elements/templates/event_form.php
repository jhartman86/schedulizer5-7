<form class="event container-fluid" ng-controller="CtrlEventForm" ng-submit="submitHandler()">
    <?php Loader::packageElement('templates/loading', 'schedulizer'); ?>

    <div class="row" ng-show="warnAliased">
        <div class="col-sm-12 text-center">
            <blockquote class="text-left"><strong>Heads Up!</strong> The event you clicked is one in a repeating series. To update it, you have to update the original event, which will cascade to <i>all</i> events in the series.<br/><br/><strong>Or,</strong> you can hide just this event from within the series.</blockquote>
            <button type="button" class="btn btn-info" ng-click="warnAliased = false">Edit Original Event</button>
            <button type="button" class="btn btn-warning" ng-click="nullifyInSeries()">Hide Just This Event From The Series</button>
        </div>
    </div>

    <div ng-show="(_ready && !warnAliased)">
        <!-- delete event? -->
        <div class="delete-event" ng-show="entity.id">
            <button type="button" class="btn btn-warning btn-xs" ng-click="confirmDelete = !confirmDelete" ng-hide="confirmDelete">
                Delete Event
            </button>
            <div ng-show="confirmDelete">
                <div>
                    <button type="button" class="btn btn-info btn-xs" ng-click="confirmDelete = !confirmDelete">
                        Nevermind!
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-danger btn-xs" ng-click="deleteEvent()">
                        <strong>Delete It!</strong>
                    </button>
                </div>
            </div>
        </div>

        <!-- title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="" class="sr-only">Title</label>
                    <input type="text" class="form-control input-title" placeholder="Title" ng-model="entity.title" />
                </div>
            </div>
        </div>

        <!-- timing tabs -->
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs">
                    <li ng-repeat="timing in timingTabs" ng-click="setTimingTabActive($index)" ng-class="{active:timingTabs[$index].active}">
                        <a>{{timing.label}}</a>
                    </li>
                    <li ng-click="addTimeEntity()"><a><i class="icon-plus"></i></a></li>
                </ul>
            </div>
        </div>

        <!-- time entities (tab contents) -->
        <div class="tab-content">
            <div class="tab-pane" ng-repeat="timeEntity in entity._timeEntities" ng-class="{active:timingTabs[$index].active}">
                <button type="button" class="btn btn-danger btn-xs remove-time-entity" ng-click="removeTimeEntity($index)" ng-show="($index !== 0)">
                    Remove
                </button>
                <div event-time-form="timeEntity"></div>
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

        <!-- timezone -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <span select-wrap class="block"><select class="form-control" ng-options="opt.value as opt.label for opt in useCalendarTimezoneOptions" ng-model="entity.useCalendarTimezone"></select></span>
                </div>
            </div>
        </div>
        <div class="row" ng-hide="entity.useCalendarTimezone">
            <div class="col-sm-12">
                <div class="form-group">
                    <span select-wrap class="block"><select class="form-control" ng-options="opt for opt in timezoneOptions" ng-model="entity.timezoneName"></select></span>
                </div>
            </div>
        </div>

        <!-- file picker -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="ccm-file-selector" data-file-selector="fileID"></div>
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
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-lg btn-block">
                        <span ng-hide="_requesting">Save</span>
                        <img ng-show="_requesting" src="<?php echo SCHEDULIZER_IMAGE_PATH; ?>spinner.svg" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>