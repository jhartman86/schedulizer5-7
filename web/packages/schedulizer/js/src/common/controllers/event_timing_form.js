angular.module('schedulizer.app').

    controller('CtrlEventTimingForm', ['$rootScope', '$scope', '$q', '$filter', 'Helpers', 'ModalManager', 'API', '_moment',
        function( $rootScope, $scope, $q, $filter, Helpers, ModalManager, API, _moment ){

        }]
    );



//angular.module('schedulizer.app').
//
//    controller('CtrlEventForm', ['$rootScope', '$scope', '$q', '$filter', 'Helpers', 'ModalManager', 'API', '_moment',
//        function( $rootScope, $scope, $q, $filter, Helpers, ModalManager, API, _moment ){
//
//            $scope.timingConfigs = [
//                {label:'Time 1', active:true}
//            ];
//
//            $scope.setActiveTiming = function( obj ){
//                angular.forEach($scope.timingConfigs, function( obj ){
//                    obj.active = false;
//                });
//                obj.active = true;
//            };
//
//            $scope.addTiming = function(){
//                angular.forEach($scope.timingConfigs, function( obj ){
//                    obj.active = false;
//                });
//                $scope.timingConfigs.push({label:'Time ' + ($scope.timingConfigs.length + 1), active:true});
//            };
//
//            $scope._ready                               = false;
//            $scope._requesting                          = false;
//            $scope.repeatTypeHandleOptions              = Helpers.repeatTypeHandleOptions();
//            $scope.repeatIndefiniteOptions              = Helpers.repeatIndefiniteOptions();
//            $scope.weekdayRepeatOptions                 = Helpers.weekdayRepeatOptions();
//            $scope.repeatMonthlyMethodOptions           = Helpers.repeatMonthlyMethodOptions();
//            $scope.repeatMonthlySpecificDayOptions      = Helpers.range(1,31);
//            $scope.repeatMonthlyDynamicWeekOptions      = Helpers.repeatMonthlyDynamicWeekOptions();
//            $scope.repeatMonthlyDynamicWeekdayOptions   = Helpers.repeatMonthlyDynamicWeekdayOptions();
//            $scope.eventColorOptions                    = Helpers.eventColorOptions();
//
//            // Default repeat settings. These don't map directly to an eventObj;
//            // but are used in defining it
//            $scope.repeatSettings = {
//                weekdayIndices          : [],
//                monthlySpecificDay      : 1,
//                monthlyDynamicWeek      : $scope.repeatMonthlyDynamicWeekOptions[0].value,
//                monthlyDynamicWeekday   : $scope.repeatMonthlyDynamicWeekdayOptions[0].value
//            };
//
//            // Did the user click to edit an event that's an alias?
//            $scope.warnAliased = ModalManager.data.eventObj.aliased || false;
//
//            // If aliased, show the message
//            if( $scope.warnAliased ){
//                $scope._ready = true;
//            }
//
//            var _requests = [
//                API.timezones.get().$promise
//            ];
//
//            // If a calendarID is passed by the ModalManager in the eventObj, its a NEW event
//            if( ModalManager.data.eventObj.calendarID ){
//                _requests.push(API.calendar.get({id:ModalManager.data.eventObj.calendarID}).$promise);
//
//                $q.all(_requests).then(function( returned ){
//                    $scope.timezoneOptions = returned[0];
//                    $scope.entity = new API.event(angular.extend(ModalManager.data.eventObj, {
//                        title                       : null,
//                        description                 : null,
//                        startUTC                    : _moment(),
//                        endUTC                      : _moment(),
//                        isOpenEnded                 : false,
//                        isAllDay                    : false,
//                        useCalendarTimezone         : true,
//                        timezoneName                : returned[1].defaultTimezone,
//                        eventColor                  : $scope.eventColorOptions[0].value,
//                        isRepeating                 : false,
//                        repeatTypeHandle            : $scope.repeatTypeHandleOptions[0].value,
//                        repeatEvery                 : 1,
//                        repeatIndefinite            : $scope.repeatIndefiniteOptions[0].value,
//                        repeatEndUTC                : ModalManager.data.eventObj.endUTC || new Date(),
//                        repeatMonthlyMethod         : $scope.repeatMonthlyMethodOptions.specific
//                    }));
//
//                    jQuery('[data-file-selector="fileID"]').concreteFileSelector({
//                        'inputName': 'fileID',
//                        'filters': [{"field":"type","type":1}]
//                    });
//
//                    $scope._ready = true;
//                });
//            }
//
//            // Otherwise, we're editing an existing one
//            // @todo: on receiving object data, convert start/endUTC to moment
//            // object immediately
//            if( ModalManager.data.eventObj.id ){
//                _requests.push(API.event.get({id:ModalManager.data.eventObj.id}).$promise);
//
//                $q.all(_requests).then(function( returned ){
//                    $scope.timezoneOptions = returned[0];
//                    $scope.entity = returned[1];
//
//                    // Handle passed repeat settings. this is not superb...
//                    if( angular.isArray($scope.entity._repeaters) ){
//                        switch($scope.entity.repeatTypeHandle){
//                            case 'weekly':
//                                var values = $scope.entity._repeaters.map(function( record ){
//                                    return record.repeatWeekday;
//                                });
//                                angular.forEach($scope.weekdayRepeatOptions, function( obj ){
//                                    obj.checked = values.indexOf(obj.value) > -1;
//                                });
//                                break;
//                            case 'monthly':
//                                // "Repeat monthly specific"
//                                if( $scope.entity.repeatMonthlyMethod === true ){
//                                    $scope.repeatSettings.monthlySpecificDay = $scope.entity._repeaters[0].repeatDay;
//                                    // "Repeat monthly ordinal"
//                                }else{
//                                    $scope.repeatSettings.monthlyDynamicWeek = $scope.entity._repeaters[0].repeatWeek;
//                                    $scope.repeatSettings.monthlyDynamicWeekday = $scope.entity._repeaters[0].repeatWeekday;
//                                }
//                                break;
//                            case 'yearly':
//                                break;
//                            default:
//                                // daily, do nothing
//                                break;
//                        }
//                        // Execute scope.selected weekdays function to apply the settings above
//                        $scope.selectedWeekdays();
//                    }
//                });
//            }
//
//            $scope.selectedWeekdays = function(){
//                var selected = $filter('filter')($scope.weekdayRepeatOptions, {checked: true});
//                $scope.repeatSettings.weekdayIndices = selected.map(function( object ){
//                    return object.value;
//                });
//            };
//
//            $scope.$watch('entity.repeatTypeHandle', function( val ){
//                switch(val){
//                    case $scope.repeatTypeHandleOptions[0].value: // daily
//                        $scope.repeatEveryOptions = Helpers.range(1,31);
//                        break;
//                    case $scope.repeatTypeHandleOptions[1].value: // weekly
//                        $scope.repeatEveryOptions = Helpers.range(1,30);
//                        break;
//                    case $scope.repeatTypeHandleOptions[2].value: // monthly
//                        $scope.repeatEveryOptions = Helpers.range(1,11);
//                        break;
//                    case $scope.repeatTypeHandleOptions[3].value: // yearly
//                        $scope.repeatEveryOptions = Helpers.range(1,5);
//                        break;
//                }
//            });
//
//            $scope.$watch('entity.startUTC', function( dateObj ){
//                if( dateObj ){
//                    $scope.calendarEndMinDate = _moment(dateObj).subtract(1, 'day');
//
//                    if( _moment($scope.entity.endUTC).isBefore(_moment($scope.entity.startUTC)) ){
//                        $scope.entity.endUTC = _moment($scope.entity.startUTC);
//                    }
//                }
//            });
//        }
//    ]);