<div class="calendry-instance" ng-class="{'list-view':forceListView}">
    <div class="calendry-header">
        <div class="header-columns">
            <div class="col left">
                <div class="tb">
                    <div class="cl">
                        <a class="btn-nav" ng-click="goToPrevMonth()">Previous</a>
                        <a class="btn-nav" ng-click="goToNextMonth()">Next</a>
                    </div>
                </div>
            </div>
            <div class="col middle">
                <div class="tb">
                    <div class="cl">
                        <span class="current" ng-click="goToCurrentMonth()">{{settings.currentMonth.format('MMM YYYY')}}</span>
                    </div>
                </div>
            </div>
            <div class="col right">
                <div class="tb">
                    <div class="cl">
                        <a class="btn-nav toggle-list-view" ng-click="toggleListView()">
                            <span ng-hide="forceListView">List View</span>
                            <span ng-show="forceListView">Calendar View</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="weekdays">
            <div class="day-label" ng-repeat="weekday in settings.daysOfWeek">{{weekday}}</div>
        </div>
    </div>
    <div class="calendry-body">
        <div class="calendar-render"></div>
    </div>
</div>