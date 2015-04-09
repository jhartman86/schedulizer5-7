angular.module('schedulizer.app').

    controller('CtrlEventForm', ['$rootScope', '$scope', '$q', '$filter', 'Helpers', 'ModalManager', 'API', '_moment',
        function( $rootScope, $scope, $q, $filter, Helpers, ModalManager, API, _moment ){

            /**
             * Template for a new time entity.
             * @param _populator
             * @returns {*}
             */
            function newEventTimeEntity( _populator ){
                return angular.extend({
                    startUTC:                       _moment(),
                    endUTC:                         _moment(),
                    isOpenEnded:                    false,
                    isAllDay:                       false,
                    isRepeating:                    false,
                    repeatTypeHandle:               null,
                    repeatEvery:                    null,
                    repeatIndefinite:               null,
                    repeatEndUTC:                   null,
                    repeatMonthlyMethod:            null,
                    repeatMonthlySpecificDay:       null,
                    repeatMonthlyOrdinalWeek:       null,
                    repeatMonthlyOrdinalWeekday:    null,
                    weeklyDays:                     []
                }, _populator || {});
            }

            $scope._ready               = false;
            $scope._requesting          = false;
            $scope.eventColorOptions    = Helpers.eventColorOptions();
            $scope.timingTabs           = [];

            // Did the user click to edit an event that's an alias?
            $scope.warnAliased = ModalManager.data.eventObj.synthetic || false;

            // If aliased, show the message
            if( $scope.warnAliased ){
                $scope._ready = true;
            }

            var _requests = [
                API.timezones.get().$promise,
                API.calendar.get({id:ModalManager.data.eventObj.calendarID}).$promise
            ];

            $q.all(_requests).then(function( results ){
                // Set timezone options on scope
                $scope.timezoneOptions = results[0];
                // Set calendar on scope
                $scope.calendarObj = results[1];

                // If eventObj passed by the modal manager DOES NOT have an ID, we're
                // creating a new entity
                if( ! ModalManager.data.eventObj.id ){
                    // Set entity on scope
                    $scope.entity = new API.event({
                        calendarID:             $scope.calendarObj.id,
                        title:                  '',
                        description:            '',
                        useCalendarTimezone:    true,
                        timezoneName:           $scope.calendarObj.defaultTimezone,
                        eventColor:             $scope.eventColorOptions[0].value,
                        _timeEntities:          [newEventTimeEntity()]
                    });
                    jQuery('[data-file-selector="fileID"]').concreteFileSelector({
                        'inputName': 'fileID',
                        'filters': [{"field":"type","type":1}]
                    });
                    $scope._ready = true;
                }
            });

            // If modal manager event object DOES have an ID, we're editing an existing one
            if( ModalManager.data.eventObj.id ){
                // Push a new request onto the promise chain...
                _requests.push(API.event.get({id:ModalManager.data.eventObj.id}).$promise);
                // When resolved (first two should be done immediately, this just chains onto the queue),
                // and the last request is index 2
                $q.all(_requests).then(function( results ){
                    // Map existing time entity results before setting entity on scope
                    results[2]._timeEntities.map(function( record ){
                        return newEventTimeEntity(record);
                    });
                    // Set the entity
                    $scope.entity = results[2];

                    jQuery('[data-file-selector="fileID"]').concreteFileSelector({
                        'inputName': 'fileID',
                        'fID': $scope.entity.fileID,
                        'filters': [{"field":"type","type":1}]
                    });

                    $scope._ready = true;
                });
            }

            /**
             * Set a specific time entity tab to active
             * @param index
             */
            $scope.setTimingTabActive = function( index ){
                angular.forEach($scope.timingTabs, function( obj ){
                    obj.active = false;
                });
                $scope.timingTabs[index].active = true;
            };

            /**
             * Add a new time entity by pushing onto the _timeEntities stack.
             */
            $scope.addTimeEntity = function(){
                $scope.entity._timeEntities.push(newEventTimeEntity());
            };

            /**
             * Remove a time entity.
             * @param index
             */
            $scope.removeTimeEntity = function( index ){
                $scope.entity._timeEntities.splice(index,1);
            };

            /**
             * Watch time entities and create/remove tabs appropriately.
             */
            $scope.$watchCollection('entity._timeEntities', function( timeEntities ){
                if( angular.isArray(timeEntities) ){
                    $scope.timingTabs = Helpers.range(1, timeEntities.length).map(function(val, index){
                        return {label:'Time ' + val, active:(index === (timeEntities.length - 1))};
                    });
                }
            });

            /**
             * Timezone configuration
             */
            $scope.$watch('calendarObj', function( obj ){
                if( angular.isObject(obj) ){
                    $scope.useCalendarTimezoneOptions = [
                        {label:'Use Calendar Timezone ('+$scope.calendarObj.defaultTimezone+')', value:true},
                        {label:'Event Uses Custom Timezone', value:false}
                    ];
                }
            });

            /**
             * If use calendar timezone is set to true, or changes to be set to true,
             * set the timezoneName on the event accordingly.
             */
            $scope.$watch('entity.useCalendarTimezone', function( val ){
                if( val === true ){
                    $scope.entity.timezoneName = $scope.calendarObj.defaultTimezone;
                }
            });

            /**
             * Persist the entity.
             */
            $scope.submitHandler = function(){
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

            /**
             * This is a synthetic event being passed by the calendar results;
             * therefore the user sees a warning window and can nullify this
             * event day in the series.
             */
            $scope.nullifyInSeries = function(){
                var nullifier = new API.eventNullify({
                    eventTimeID: ModalManager.data.eventObj.eventTimeID,
                    hideOnDate: ModalManager.data.eventObj.startUTC
                });
                nullifier.$save().then(function( resp ){
                    $rootScope.$emit('calendar.refresh');
                    ModalManager.classes.open = false;
                });
            };

            //$scope.timingConfigs = [
            //    {label:'Time 1', active:true}
            //];
            //
            //$scope.setActiveTiming = function( obj ){
            //    angular.forEach($scope.timingConfigs, function( obj ){
            //        obj.active = false;
            //    });
            //    obj.active = true;
            //};
            //
            //$scope.addTiming = function(){
            //    angular.forEach($scope.timingConfigs, function( obj ){
            //        obj.active = false;
            //    });
            //    $scope.timingConfigs.push({label:'Time ' + ($scope.timingConfigs.length + 1), active:true});
            //};
            //
            //$scope._ready                               = false;
            //$scope._requesting                          = false;
            //$scope.repeatTypeHandleOptions              = Helpers.repeatTypeHandleOptions();
            //$scope.repeatIndefiniteOptions              = Helpers.repeatIndefiniteOptions();
            //$scope.weekdayRepeatOptions                 = Helpers.weekdayRepeatOptions();
            //$scope.repeatMonthlyMethodOptions           = Helpers.repeatMonthlyMethodOptions();
            //$scope.repeatMonthlySpecificDayOptions      = Helpers.range(1,31);
            //$scope.repeatMonthlyDynamicWeekOptions      = Helpers.repeatMonthlyDynamicWeekOptions();
            //$scope.repeatMonthlyDynamicWeekdayOptions   = Helpers.repeatMonthlyDynamicWeekdayOptions();
            //$scope.eventColorOptions                    = Helpers.eventColorOptions();
            //
            //// Default repeat settings. These don't map directly to an eventObj;
            //// but are used in defining it
            //$scope.repeatSettings = {
            //    weekdayIndices          : [],
            //    monthlySpecificDay      : 1,
            //    monthlyDynamicWeek      : $scope.repeatMonthlyDynamicWeekOptions[0].value,
            //    monthlyDynamicWeekday   : $scope.repeatMonthlyDynamicWeekdayOptions[0].value
            //};
            //

            //
            //var _requests = [
            //    API.timezones.get().$promise
            //];
            //
            //// If a calendarID is passed by the ModalManager in the eventObj, its a NEW event
            //if( ModalManager.data.eventObj.calendarID ){
            //    _requests.push(API.calendar.get({id:ModalManager.data.eventObj.calendarID}).$promise);
            //
            //    $q.all(_requests).then(function( returned ){
            //        $scope.timezoneOptions = returned[0];
            //        $scope.entity = new API.event(angular.extend(ModalManager.data.eventObj, {
            //            title                       : null,
            //            description                 : null,
            //            startUTC                    : _moment(),
            //            endUTC                      : _moment(),
            //            isOpenEnded                 : false,
            //            isAllDay                    : false,
            //            useCalendarTimezone         : true,
            //            timezoneName                : returned[1].defaultTimezone,
            //            eventColor                  : $scope.eventColorOptions[0].value,
            //            isRepeating                 : false,
            //            repeatTypeHandle            : $scope.repeatTypeHandleOptions[0].value,
            //            repeatEvery                 : 1,
            //            repeatIndefinite            : $scope.repeatIndefiniteOptions[0].value,
            //            repeatEndUTC                : ModalManager.data.eventObj.endUTC || new Date(),
            //            repeatMonthlyMethod         : $scope.repeatMonthlyMethodOptions.specific
            //        }));
            //
            //        jQuery('[data-file-selector="fileID"]').concreteFileSelector({
            //            'inputName': 'fileID',
            //            'filters': [{"field":"type","type":1}]
            //        });
            //
            //        $scope._ready = true;
            //    });
            //}
            //
            //// Otherwise, we're editing an existing one
            //// @todo: on receiving object data, convert start/endUTC to moment
            //// object immediately
            //if( ModalManager.data.eventObj.id ){
            //    _requests.push(API.event.get({id:ModalManager.data.eventObj.id}).$promise);
            //
            //    $q.all(_requests).then(function( returned ){
            //        $scope.timezoneOptions = returned[0];
            //        $scope.entity = returned[1];
            //
            //        // Handle passed repeat settings. this is not superb...
            //        if( angular.isArray($scope.entity._repeaters) ){
            //            switch($scope.entity.repeatTypeHandle){
            //                case 'weekly':
            //                    var values = $scope.entity._repeaters.map(function( record ){
            //                        return record.repeatWeekday;
            //                    });
            //                    angular.forEach($scope.weekdayRepeatOptions, function( obj ){
            //                        obj.checked = values.indexOf(obj.value) > -1;
            //                    });
            //                    break;
            //                case 'monthly':
            //                    // "Repeat monthly specific"
            //                    if( $scope.entity.repeatMonthlyMethod === true ){
            //                        $scope.repeatSettings.monthlySpecificDay = $scope.entity._repeaters[0].repeatDay;
            //                        // "Repeat monthly ordinal"
            //                    }else{
            //                        $scope.repeatSettings.monthlyDynamicWeek = $scope.entity._repeaters[0].repeatWeek;
            //                        $scope.repeatSettings.monthlyDynamicWeekday = $scope.entity._repeaters[0].repeatWeekday;
            //                    }
            //                    break;
            //                case 'yearly':
            //                    break;
            //                default:
            //                    // daily, do nothing
            //                    break;
            //            }
            //            // Execute scope.selected weekdays function to apply the settings above
            //            $scope.selectedWeekdays();
            //        }
            //
            //        jQuery('[data-file-selector="fileID"]').concreteFileSelector({
            //            'inputName': 'fileID',
            //            'fID': $scope.entity.fileID,
            //            'filters': [{"field":"type","type":1}]
            //        });
            //
            //        $scope._ready = true;
            //    });
            //}
            //
            //$scope.selectedWeekdays = function(){
            //    var selected = $filter('filter')($scope.weekdayRepeatOptions, {checked: true});
            //    $scope.repeatSettings.weekdayIndices = selected.map(function( object ){
            //        return object.value;
            //    });
            //};
            //
            //$scope.$watch('entity.repeatTypeHandle', function( val ){
            //    switch(val){
            //        case $scope.repeatTypeHandleOptions[0].value: // daily
            //            $scope.repeatEveryOptions = Helpers.range(1,31);
            //            break;
            //        case $scope.repeatTypeHandleOptions[1].value: // weekly
            //            $scope.repeatEveryOptions = Helpers.range(1,30);
            //            break;
            //        case $scope.repeatTypeHandleOptions[2].value: // monthly
            //            $scope.repeatEveryOptions = Helpers.range(1,11);
            //            break;
            //        case $scope.repeatTypeHandleOptions[3].value: // yearly
            //            $scope.repeatEveryOptions = Helpers.range(1,5);
            //            break;
            //    }
            //});
            //
            //$scope.$watch('entity.startUTC', function( dateObj ){
            //    if( dateObj ){
            //        $scope.calendarEndMinDate = _moment(dateObj).subtract(1, 'day');
            //
            //        if( _moment($scope.entity.endUTC).isBefore(_moment($scope.entity.startUTC)) ){
            //            $scope.entity.endUTC = _moment($scope.entity.startUTC);
            //        }
            //    }
            //});
            //
            ///**
            // * Submit handler
            // * @todo: before sending, adjust entity start/endUTC props to moment
            // * objects and ensure sending correctly (as UTC?)
            // */
            //$scope.submitHandler = function(){
            //    console.log($scope.entity);
            //    //angular.extend($scope.entity, {repeatSettings:$scope.repeatSettings});
            //
            //    // File picker specific
            //    //$scope.entity.fileID = parseInt(jQuery('input[type="hidden"]', '.ccm-file-selector').val()) || null;
            //    //
            //    //$scope._requesting = true;
            //    //// If entity already has ID, $update, otherwise $save (create), and bind callback
            //    //($scope.entity.id ? $scope.entity.$update() : $scope.entity.$save()).then(
            //    //    function( resp ){
            //    //        $scope._requesting = false;
            //    //        $rootScope.$emit('calendar.refresh');
            //    //        ModalManager.classes.open = false;
            //    //    }
            //    //);
            //};

            //

            //
            //// Nullifiers
            //API.eventNullify.query({eventID:ModalManager.data.eventObj.id}, function( resp ){
            //    $scope.hasNullifiers  = resp.length >= 1;
            //    $scope.showNullifiers = false;
            //    resp.forEach(function( resource ){
            //        resource._moment = _moment.utc(resource.hideOnDate);
            //    });
            //    $scope.configuredNullifiers = resp;
            //});
            //
            ///**
            // * Delete an existing nullifier record.
            // * @param resource
            // */
            //$scope.cancelNullifier = function( resource ){
            //    resource.$delete(function( resp ){
            //        $rootScope.$emit('calendar.refresh');
            //    });
            //};
            //
            ///**
            // * Hide a single event from a day series (this is called when the event repeat warning
            // * message pops up, nowhere else)
            // */
            //$scope.nullifyInSeries = function(){
            //    // Setup resource
            //    var nullifier = new API.eventNullify({
            //        eventID: ModalManager.data.eventObj.id,
            //        hideOnDate: ModalManager.data.eventObj.record.startLocalized
            //    });
            //    // Persist it
            //    nullifier.$save().then(function( resp ){
            //        if( resp.id ){
            //            $scope._requesting = false;
            //            $rootScope.$emit('calendar.refresh');
            //            ModalManager.classes.open = false;
            //        }
            //    });
            //};
        }
    ]);