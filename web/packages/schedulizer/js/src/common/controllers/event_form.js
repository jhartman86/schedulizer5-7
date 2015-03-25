angular.module('schedulizer.app').

    controller('CtrlEventForm', ['$rootScope', '$scope', '$q', '$filter', 'Helpers', 'ModalManager', 'API', '_moment',
        function( $rootScope, $scope, $q, $filter, Helpers, ModalManager, API, _moment ){

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

            // Did the user click to edit an event that's an alias?
            $scope.warnAliased = ModalManager.data.eventObj.aliased || false;

            // If aliased, show the message
            if( $scope.warnAliased ){
                $scope._ready = true;
            }

            var _requests = [
                API.timezones.get().$promise
            ];

            // If a calendarID is passed by the ModalManager in the eventObj, its a NEW event
            if( ModalManager.data.eventObj.calendarID ){
                _requests.push(API.calendar.get({id:ModalManager.data.eventObj.calendarID}).$promise);

                $q.all(_requests).then(function( returned ){
                    $scope.timezoneOptions = returned[0];
                    $scope.entity = new API.event(angular.extend(ModalManager.data.eventObj, {
                        title                       : null,
                        description                 : null,
                        startUTC                    : _moment(),
                        endUTC                      : _moment(),
                        isAllDay                    : false,
                        useCalendarTimezone         : true,
                        timezoneName                : returned[1].defaultTimezone,
                        eventColor                  : $scope.eventColorOptions[0].value,
                        isRepeating                 : false,
                        repeatTypeHandle            : $scope.repeatTypeHandleOptions[0].value,
                        repeatEvery                 : 1,
                        repeatIndefinite            : $scope.repeatIndefiniteOptions[0].value,
                        repeatEndUTC                : ModalManager.data.eventObj.endUTC || new Date(),
                        repeatMonthlyMethod         : $scope.repeatMonthlyMethodOptions.specific
                    }));

                    jQuery('[data-file-selector="fileID"]').concreteFileSelector({
                        'inputName': 'fileID',
                        'filters': [{"field":"type","type":1}]
                    });

                    $scope._ready = true;
                });
            }

            // Otherwise, we're editing an existing one
            // @todo: on receiving object data, convert start/endUTC to moment
            // object immediately
            if( ModalManager.data.eventObj.id ){
                _requests.push(API.event.get({id:ModalManager.data.eventObj.id}).$promise);

                $q.all(_requests).then(function( returned ){
                    $scope.timezoneOptions = returned[0];
                    $scope.entity = returned[1];

                    jQuery('[data-file-selector="fileID"]').concreteFileSelector({
                        'inputName': 'fileID',
                        'fID': $scope.entity.fileID,
                        'filters': [{"field":"type","type":1}]
                    });

                    $scope._ready = true;
                });
            }

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

            $scope.$watch('entity.startUTC', function( dateObj ){
                if( dateObj ){
                    $scope.calendarEndMinDate = _moment(dateObj).subtract(1, 'day');

                    if( _moment($scope.entity.endUTC).isBefore(_moment($scope.entity.startUTC)) ){
                        $scope.entity.endUTC = _moment($scope.entity.startUTC);
                    }
                }
            });

            /**
             * Submit handler
             * @todo: before sending, adjust entity start/endUTC props to moment
             * objects and ensure sending correctly (as UTC?)
             */
            $scope.submitHandler = function(){
                angular.extend($scope.entity, {repeatSettings:$scope.repeatSettings});

                // File picker specific
                $scope.entity.fileID = parseInt(jQuery('input[type="hidden"]', '.ccm-file-selector').val()) || null;

                $scope._requesting = true;
                // If entity already has ID, $update, otherwise $save (create), and bind callback
                ($scope.entity.id ? $scope.entity.$update() : $scope.entity.$save()).then(
                    function( resp ){
                        $scope._requesting = false;
                        $rootScope.$emit('calendar.refresh');
                        ModalManager.classes.open = false;
                    }
                );
            };

            /**
             * Delete the entity.
             */
            $scope.confirmDelete = false;
            $scope.deleteEvent = function(){
                $scope.entity.$delete().then(function( resp ){
                    if( resp.ok ){
                        $rootScope.$emit('calendar.refresh');
                        ModalManager.classes.open = false;
                    }
                });
            };
        }
    ]);