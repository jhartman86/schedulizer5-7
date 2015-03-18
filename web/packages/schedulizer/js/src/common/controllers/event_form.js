angular.module('schedulizer.app').

    controller('CtrlEventForm', ['$rootScope', '$scope', '$q', '$filter', 'Helpers', 'ModalManager', 'API',
        function( $rootScope, $scope, $q, $filter, Helpers, ModalManager, API ){

            $scope._ready                               = false;
            $scope._requesting                          = false;
            $scope.repeatTypeHandleOptions              = Helpers.eventDefaults.repeatTypeHandleOptions;
            $scope.repeatIndefiniteOptions              = Helpers.eventDefaults.repeatIndefiniteOptions;
            $scope.weekdayRepeatOptions                 = Helpers.eventDefaults.weekdayRepeatOptions;
            $scope.repeatMonthlyMethodOptions           = Helpers.eventDefaults.repeatMonthlyMethodOptions;
            $scope.repeatMonthlySpecificDayOptions      = Helpers.range(1,31);
            $scope.repeatMonthlyDynamicWeekOptions      = Helpers.eventDefaults.repeatMonthlyDynamicWeekOptions;
            $scope.repeatMonthlyDynamicWeekdayOptions   = Helpers.eventDefaults.repeatMonthlyDynamicWeekdayOptions;
            $scope.eventColorOptions                    = Helpers.eventDefaults.eventColorOptions;

            var _requests = [API.timezones.query().$promise];

            if( ModalManager.data.eventObj.id ){
                _requests.push(API.event.get({id:ModalManager.data.eventObj.id}).$promise);
            }

            $q.all(_requests).then(function( returned ){
                $scope.timezoneOptions = returned[0];
                $scope.entity = returned[1] || new API.event(angular.extend(ModalManager.data.eventObj, {
                    title                       : null,
                    description                 : null,
                    startUTC                    : ModalManager.data.eventObj.startUTC || new Date(),
                    endUTC                      : ModalManager.data.eventObj.endUTC || new Date(),
                    isAllDay                    : false,
                    useCalendarTimezone         : true,
                    timezoneName                : 'UTC', // @todo: implement form
                    eventColor                  : $scope.eventColorOptions[0].value,
                    isRepeating                 : false,
                    repeatTypeHandle            : $scope.repeatTypeHandleOptions[0].value,
                    repeatEvery                 : 1,
                    repeatIndefinite            : $scope.repeatIndefiniteOptions[0].value,
                    repeatEndUTC                : ModalManager.data.eventObj.endUTC || new Date(),
                    repeatMonthlyMethod         : $scope.repeatMonthlyMethodOptions.specific
                }));
                $scope._ready = true;
            });

            // These don't map directly to an eventObj; but are used in defining it
            $scope.repeatSettings = {
                weekdayIndices          : [],
                monthlySpecificDay      : 1,
                monthlyDynamicWeek      : $scope.repeatMonthlyDynamicWeekOptions[0].value,
                monthlyDynamicWeekday   : $scope.repeatMonthlyDynamicWeekdayOptions[0].value
            };

            $scope.selectedWeekdays = function(){
                var selected = $filter('filter')($scope.weekdayRepeatOptions, {checked: true});
                $scope.repeatSettings.weekdayIndices = selected.map(function( object ){
                    return object.value;
                });
            };

            $scope.$watch('entity.repeatTypeHandle', function( val ){
                switch(val){
                    case $scope.repeatTypeHandleOptions[0].value: // daily
                        $scope.repeatEveryOptions = Helpers.range(1,31);
                        break;
                    case $scope.repeatTypeHandleOptions[1].value: // weekly
                        $scope.repeatEveryOptions = Helpers.range(1,30);
                        break;
                    case $scope.repeatTypeHandleOptions[2].value: // monthly
                        $scope.repeatEveryOptions = Helpers.range(1,11);
                        break;
                    case $scope.repeatTypeHandleOptions[3].value: // yearly
                        $scope.repeatEveryOptions = Helpers.range(1,5);
                        break;
                }
            });

            $scope.submitHandler = function(){
                angular.extend($scope.entity, {repeatSettings:$scope.repeatSettings});

                $scope._requesting = true;
                // If entity already has ID, $update, otherwise $save (create), and bind callback
                ($scope.entity.id ? $scope.entity.$update() : $scope.entity.$save()).then(
                    function( resp ){
                        $scope._requesting = false;
                        $rootScope.$emit('calendar.refresh');
                        ModalManager.classes.open = false;
                    }
                );

                // Transform
//                var data            = angular.copy($scope.form_data, {});
//                data.startUTC       = $scope.form_data.startUTC.toISOString();
//                data.endUTC         = $scope.form_data.endUTC.toISOString();
//                data.repeatEndUTC   = $scope.form_data.repeatEndUTC.toISOString();
//                angular.extend(data, {repeatSettings: $scope.repeatSettings});

                // Send
//                $http.post(Routes.generate('api.event', [$scope.form_data.calendarID]), data).then(
//                    function( resp ){ // Success
//                        console.log('ok', resp);
//                    },
//                    function( resp ){ // Failure
//                        console.log('nope', resp);
//                    }
//                );
            };
        }
    ]);