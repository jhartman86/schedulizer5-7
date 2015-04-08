/* global FastClick */
;(function( window, angular, undefined ){ 'use strict';

    angular.module('schedulizer', [
        'ngResource', 'schedulizer.app', 'mgcrea.ngStrap.datepicker', 'mgcrea.ngStrap.timepicker', 'calendry'
    ]).

    /**
     * @description App configuration
     * @param $provide
     * @param $locationProvider
     */
    config(['$provide', '$locationProvider',
        function( $provide, $locationProvider ){
            // Disable Angular's HTML5 mode stuff
            $locationProvider.html5Mode(false);

            var routeBase = window['_Schedulizer'];

            // Provide API route helpers
            $provide.factory('Routes', function(){
                var _routes = {
                    api: {
                        calendar:       routeBase.api + '/calendar',
                        event:          routeBase.api + '/event',
                        eventNullify:   routeBase.api + '/event_nullify',
                        eventList:      routeBase.api + '/event/list',
                        timezones:      routeBase.api + '/timezones'
                    },
                    dashboard: routeBase.dashboard
                };

                return {
                    routeList: _routes,
                    generate: function( _route, _routeParams ){
                        var route = _route.split('.').reduce(function(obj, mapTo){
                            return obj[mapTo];
                        }, _routes);
                        return (_routeParams || []).length ? (route + '/' + _routeParams.join('/')) : route;
                    }
                };
            });
        }
    ]).

    factory('API', ['$resource', 'Routes',
       function( $resource, Routes ){
           var _methods = {
               update: {method:'PUT', params:{_method:'PUT'}}
           };

           return {
               calendar: $resource(Routes.generate('api.calendar',[':id']), {id:'@id'}, angular.extend(_methods, {
                   // more custom methods here
               })),
               event: $resource(Routes.generate('api.event',[':id']), {id:'@id'}, angular.extend(_methods, {
                   // more custom methods here
               })),
               eventNullify: $resource(Routes.generate('api.eventNullify',[':id']), {id:'@id'}, angular.extend(_methods, {
                   // more custom methods
               })),
               timezones: $resource(Routes.generate('api.timezones'), {}, {
                   get: {isArray:true, cache:true}
               }),
               // Append the Routes factory result into the API for easier access
               _routes: Routes
           };
       }
    ]);


    /**
     * Manually bootstrap the document
     */
    angular.element(document).ready(function(){
        if( !(window['_Schedulizer']) ){
            alert('Schedulizer is missing a configuration to run and has aborted.');
            return;
        }

        angular.bootstrap(document, ['schedulizer']);
    });

})(window, window.angular);
angular.module('schedulizer.app', []);
angular.module('calendry', []);

angular.module('schedulizer.app').

    directive('calendar', ['$rootScope', '_moment', 'ModalManager', 'Routes',
        function( $rootScope, _moment, ModalManager, Routes ){

            function _link( scope, $element, attrs ){
                $element.fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    editable: true,
                    defaultView: 'month',
                    events: attrs.feed,
                    dayClick: function( moment ){
                        scope.$apply(function(){
                            ModalManager.data = {
                                source: Routes.generate('views.eventFormModal'), // DEPRECATED routes.generate call
                                eventObj: {
                                    calendarID:     +(attrs.id),
                                    startUTC:       moment.local().clone().add(9, 'hours'),
                                    endUTC:         moment.local().clone().add(10, 'hours'),
                                    repeatEndUTC:   moment.local().clone().add(10, 'hours'),
                                }
                            };
                        });
                    },
                    eventClick: function( calEvent ){
                        scope.$apply(function(){
                            ModalManager.data = {
                                source: Routes.generate('views.eventFormModal'),
                                eventObj: calEvent
                            };
                        });
                    }
                });

                // Event Listeners
//                $rootScope.$on('calendar.refresh', function(){
//                    $element.fullCalendar('refetchEvents');
//                });

//                $element.fullCalendar({
//                    header: {
//                        left: 'prev,next today',
//                        center: 'title',
//                        right: 'month,agendaWeek,agendaDay'
//                    },
//                    editable: true,
//                    defaultView: 'month',
//                    // load event data
//                    events: _toolsURI + 'dashboard/events/feed?' + $.param({
//                        calendarID: _calendarID
//                    }),
//
//                    // open a dialog and create a new event on the specific day
//                    dayClick: function(date, allDay, jsEvent, view){
//                        var _data = $.param({
//                            calendarID: _calendarID,
//                            year: date.getUTCFullYear(),
//                            month: date.getUTCMonth() + 1,
//                            day: date.getUTCDate(),
//                            hour: date.getUTCHours(),
//                            min: date.getUTCMinutes(),
//                            allDay: allDay
//                        });
//
//                        // launch the dialog and pass appropriate data
//                        $.fn.dialog.open({
//                            width:650,
//                            height:550,
//                            title: 'New Event: ' + date.toLocaleDateString(),
//                            href: _toolsURI + 'dashboard/events/new?' + _data
//                        });
//                    },
//
//                    // open a dialog to edit an existing event
//                    eventClick: function(calEvent, jsEvent, view){
//                        editEventDialog(calEvent);
//                    },
//
//                    eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc){
//                        // if its a repeating event, show warning
//                        if( event.isRepeating === 1 ){
//                            if( event.repeatMethod !== 'daily' ){
//                                ccmAlert.hud('Events that repeat ' + event.repeatMethod + ' cannot be dragged/dropped.', 2000, 'error');
//                                revertFunc.call();
//                                return;
//                            }
//                            if( ! confirm('This is a repeating event and will affect all other events in the series. Proceed?') ){
//                                revertFunc.call();
//                                return;
//                            }
//                        }
//
//                        // append day and minute deltas to the event object
//                        event.dayDelta    = dayDelta;
//                        event.minuteDelta = minuteDelta;
//
//                        // then send the whole shebang
//                        $.post( _toolsURI + 'dashboard/events/calendar_handler_drop', event, function( _respData ){
//                            if( _respData.code === 1 ){
//                                ccmAlert.hud(_respData.msg, 2000, 'success');
//                            }else{
//                                ccmAlert.hud('Error occurred adjusting the event length', 2000, 'error');
//                            }
//                        }, 'json');
//                    },
//
//                    eventResize: function(event, dayDelta, minuteDelta, revertFunc){
//                        // if its a repeating event, show warning
//                        if( event.isRepeating === 1 ){
//                            if( ! confirm('This is a repeating event and will affect all other events in the series. Proceed?') ){
//                                revertFunc.call();
//                                return;
//                            }
//                        }
//
//                        // append day and minute deltas to the event object
//                        event.dayDelta    = dayDelta;
//                        event.minuteDelta = minuteDelta;
//
//                        // then send the whole shebang
//                        $.post( _toolsURI + 'dashboard/events/calendar_handler_resize', event, function( _respData ){
//                            if( _respData.code === 1 ){
//                                ccmAlert.hud(_respData.msg, 2000, 'success');
//                            }else{
//                                ccmAlert.hud('Error occurred adjusting the event length', 2000, 'error');
//                            }
//                        }, 'json');
//                    }
//                });
            }

            return {
                restrict: 'A',
                link:     _link
            };
        }
    ]);
angular.module('schedulizer.app').

    directive('eventTimeForm', [function(){

        function _link( scope, $elem, attrs, Controller ){
            // Nothing done here, everything via the controller
        }

        return {
            restrict:       'A',
            templateUrl:    '/event_timing_form',
            scope:          {_timeEntity:'=eventTimeForm'},
            link:           _link,
            controller: ['$scope', '$filter', 'Helpers', '_moment',
                function( $scope, $filter, Helpers, _moment ){
                    $scope.repeatTypeHandleOptions              = Helpers.repeatTypeHandleOptions();
                    $scope.repeatIndefiniteOptions              = Helpers.repeatIndefiniteOptions();
                    $scope.weekdayRepeatOptions                 = Helpers.weekdayRepeatOptions();
                    $scope.repeatMonthlyMethodOptions           = Helpers.repeatMonthlyMethodOptions();
                    $scope.repeatMonthlySpecificDayOptions      = Helpers.range(1,31);
                    $scope.repeatMonthlyDynamicWeekdayOptions   = Helpers.repeatMonthlyDynamicWeekdayOptions();
                    $scope.repeatMonthlyDynamicWeekOptions      = Helpers.repeatMonthlyDynamicWeekOptions();

                    /**
                     * Weekday selection is tracked in a different object on the $scope, so we
                     * use that to determine what to put into entity.weeklyDays.
                     */
                    $scope.selectedWeekdays = function(){
                        var selected = $filter('filter')($scope.weekdayRepeatOptions, {checked: true});
                        $scope._timeEntity.weeklyDays = selected.map(function( obj ){
                            return obj.value;
                        });
                    };

                    /**
                     * If weeklyDays has values, set selected values in the scope tracker.
                     */
                    if( angular.isArray($scope._timeEntity.weeklyDays) && $scope._timeEntity.weeklyDays.length >= 1 ){
                        angular.forEach($scope.weekdayRepeatOptions, function( obj ){
                            obj.checked = $scope._timeEntity.weeklyDays.indexOf(obj.value) > -1;
                        });
                    }

                    /**
                     * These setters will only run if the user clicks "repeat" and all the
                     * current repeat settings are null.
                     */
                    function onChangeRepeatMethodAdjustValuesIfNull(){
                        // Set repeatEvery frequency
                        if( $scope._timeEntity.repeatEvery === null ){
                            $scope._timeEntity.repeatEvery = $scope.repeatEveryOptions[0];
                        }
                        // Set repeatIndefinite values
                        if( $scope._timeEntity.repeatIndefinite === null ){
                            $scope._timeEntity.repeatIndefinite = $scope.repeatIndefiniteOptions[0].value;
                        }
                        // If repeat type is set to monthly and the monthly settings are null...
                        if( $scope._timeEntity.repeatTypeHandle === $scope.repeatTypeHandleOptions[2].value ){
                            if( $scope._timeEntity.repeatMonthlyMethod === null ){
                                $scope._timeEntity.repeatMonthlyMethod = $scope.repeatMonthlyMethodOptions.specific;
                            }
                            if( $scope._timeEntity.repeatMonthlySpecificDay === null ){
                                $scope._timeEntity.repeatMonthlySpecificDay = $scope.repeatMonthlySpecificDayOptions[0];
                            }
                            if( $scope._timeEntity.repeatMonthlyOrdinalWeek === null ){
                                $scope._timeEntity.repeatMonthlyOrdinalWeek = $scope.repeatMonthlyDynamicWeekOptions[0].value;
                            }
                            if( $scope._timeEntity.repeatMonthlyOrdinalWeekday === null ){
                                $scope._timeEntity.repeatMonthlyOrdinalWeekday = $scope.repeatMonthlyDynamicWeekdayOptions[0].value;
                            }
                        }
                    }

                    /**
                     * Nullify monthly repeat settings.
                     */
                    function nullifyMonthlySettings(){
                        $scope._timeEntity.repeatMonthlyMethod = null;
                        $scope._timeEntity.repeatMonthlyOrdinalWeek = null;
                        $scope._timeEntity.repeatMonthlyOrdinalWeekday = null;
                        $scope._timeEntity.repeatMonthlySpecificDay = null;
                    }

                    /**
                     * Nullify weekly repeat settings.
                     */
                    function nullifyWeeklySettings(){
                        $scope._timeEntity.weeklyDays = [];
                        angular.forEach($scope.weekdayRepeatOptions, function( obj ){
                            obj.checked = false;
                        });
                    }

                    /**
                     * Nullify all repeat settings.
                     */
                    function nullifyAllRepeatSettings(){
                        nullifyMonthlySettings();
                        nullifyWeeklySettings();
                        $scope._timeEntity.repeatEndUTC = null;
                        $scope._timeEntity.repeatEvery = null;
                        $scope._timeEntity.repeatIndefinite = null;
                        $scope._timeEntity.repeatTypeHandle = null;
                    }

                    /**
                     * When the repeat type handle is switched, set default values
                     * if some are existing, and nullify others.
                     */
                    $scope.$watch('_timeEntity.repeatTypeHandle', function( val ){
                        switch(val){
                            case $scope.repeatTypeHandleOptions[0].value: // daily
                                $scope.repeatEveryOptions = Helpers.range(1,31);
                                nullifyMonthlySettings();
                                nullifyWeeklySettings();
                                break;
                            case $scope.repeatTypeHandleOptions[1].value: // weekly
                                $scope.repeatEveryOptions = Helpers.range(1,30);
                                nullifyMonthlySettings();
                                break;
                            case $scope.repeatTypeHandleOptions[2].value: // monthly
                                $scope.repeatEveryOptions = Helpers.range(1,11);
                                nullifyWeeklySettings();
                                break;
                            case $scope.repeatTypeHandleOptions[3].value: // yearly
                                $scope.repeatEveryOptions = Helpers.range(1,5);
                                nullifyMonthlySettings();
                                nullifyWeeklySettings();
                                break;
                        }
                        if( $scope._timeEntity.repeatTypeHandle !== null ){
                            onChangeRepeatMethodAdjustValuesIfNull();
                        }
                    });

                    /**
                     * If set to repeat indefinitely, nullify repeatEndUTC.
                     */
                    $scope.$watch('_timeEntity.repeatIndefinite', function( value ){
                        if( value === true ){
                            $scope._timeEntity.repeatEndUTC = null;
                        }
                    });

                    /**
                     * Update the endUTC when startUTC is adjusted.
                     */
                    $scope.$watch('_timeEntity.startUTC', function( dateObj ){
                        if( dateObj ){
                            $scope.calendarEndMinDate = _moment(dateObj).subtract(1, 'day');
                            if( _moment($scope._timeEntity.endUTC).isBefore(_moment($scope._timeEntity.startUTC)) ){
                                $scope._timeEntity.endUTC = _moment($scope._timeEntity.startUTC);
                            }
                        }
                    });

                    /**
                     * This takes care of syncronizing repeat settings, including when
                     * the time form is initialized.
                     */
                    $scope.$watch('_timeEntity.isRepeating', function( value ){
                        if( value === true && $scope._timeEntity.repeatTypeHandle === null ){
                            $scope._timeEntity.repeatTypeHandle = $scope.repeatTypeHandleOptions[0].value;
                        }
                        if( value === false ){
                            nullifyAllRepeatSettings();
                        }
                    });
                }
            ]
        };
    }]);
angular.module('schedulizer.app').

    /**
     * Will automatically initialize modalWindow directive; and we don't have to worry about
     * leaving this in HTML somewhere.
     */
    run([function(){
        angular.element(document.querySelector('body')).append('<div modal-window class="schedulizer-app" ng-class="manager.classes"><a class="icon-close" modal-close></a><div class="modal-inner" ng-include="manager.data.source"></div></div>');
    }]).

    /**
     * ModalManager
     */
    factory('ModalManager', [function(){
        return {
            classes : {open: false},
            data    : {source: null}
        };
    }]).

    /**
     * Elements that should trigger opening a modal window
     * @returns {{restrict: string, scope: boolean, link: Function, controller: Array}}
     */
    directive('modalize', [function(){

            /**
             * @param scope
             * @param $element
             * @param attrs
             * @private
             */
            function _link( scope, $element, attrs ){
                $element.on('click', function(){
                    scope.$apply(function(){
                        scope.manager.data = angular.extend({
                            source: attrs.modalize
                        }, scope.using);
                    });
                });
            }

            return {
                restrict:   'A',
                scope:      {using: '=using'},
                link:       _link,
                controller: ['$scope', 'ModalManager', function( $scope, ModalManager ){
                    $scope.manager = ModalManager;
                }]
            };
        }
    ]).

    /**
     * Close the modal window
     */
    directive('modalClose', ['ModalManager', function( ModalManager ){

        function _link( scope, $elem, attrs ){
            $elem.on('click', function(){
                scope.$apply(function(){
                    ModalManager.classes.open = false;
                    ModalManager.data = null;
                });
            });
        }

        return {
            restrict: 'A',
            link: _link
        };
    }]).

    /**
     * Actual ModalWindow directive handler
     * @param Tween
     * @returns {{restrict: string, scope: boolean, link: Function, controller: Array}}
     */
    directive('modalWindow', [function(){

        /**
         * Link function with ModalManager service bound to the scope
         * @param scope
         * @param $elem
         * @param attrs
         * @private
         */
        function _link( scope, $elem, attrs ){
            scope.$watch('manager.classes.open', function(_val){
                angular.element(document.documentElement).toggleClass('schedulizer-modal', _val);
                if( ! _val ){
                    scope.manager.data = null;
                }
            });
        }

        return {
            restrict:   'A',
            scope:      true,
            link:       _link,
            controller: ['$scope', 'ModalManager', function( $scope, ModalManager ){
                $scope.manager = ModalManager;

                $scope.$on('$includeContentLoaded', function(){
                    $scope.manager.classes.open = true;
                });
            }]
        };
        }
    ]);

angular.module('schedulizer.app').

    directive('redactorized', [function(){

        /**
         * Redactor settings, pulled from Concrete5 defaults
         * @type {{minHeight: number, concrete5: {filemanager: boolean, sitemap: boolean, lightbox: boolean}, plugins: Array}}
         */
        var settings = {
            minHeight: 200,
            concrete5: {
                filemanager: true,
                sitemap: true,
                lightbox: true
            },
            plugins: ['fontcolor', 'concrete5','underline']
        };

        /**
         * @param scope
         * @param $element
         * @param attrs
         * @param Controller ngModel controller
         * @private
         */
        function _link( scope, $elem, attrs, Controller ){
            // ngModel's $render function
            Controller.$render = function(){
                // Set the initial value, if any
                $elem.val(Controller.$viewValue);

                // Initialize redactor, binding change callback
                $elem.redactor(angular.extend(settings, {
                    changeCallback: function(){
                        Controller.$setViewValue(this.get());
                        //scope.$apply(Controller.$setViewValue(this.get()));
                    }
                }));

                if( Controller.$viewValue ){
                    $elem.redactor('set', Controller.$viewValue);
                }
            };
        }

        return {
            priority:   0,
            require:    '?ngModel',
            restrict:   'A',
            link:       _link
        };
    }]);
angular.module('schedulizer.app').

    /**
     * @description MomentJS provider
     * @param $window
     * @param $log
     * @returns Moment | false
     */
    provider('_moment', function(){
        this.$get = ['$window', '$log',
            function( $window, $log ){
                return $window['moment'] || ($log.warn('MomentJS unavailable!'), false);
            }
        ];
    });
/* global Sortable */
;(function( window, angular, undefined ){
    'use strict';

    angular.module('calendry').


    /**
     * Wrap 'moment' from the global scope for angular DI, or set to false if unavailable.
     */
    factory('MomentJS', ['$window', '$log', function( $window, $log ){
        return $window['moment'] ||
            ($log.warn('Moment.JS not available in global scope, Calendry will be unavailable.'), false);
    }]).

    /**
     * Calendry directive
     */
    directive('calendry', ['$cacheFactory', '$document', '$log', '$q', 'MomentJS',
        function factory( $cacheFactory, $document, $log, $q, momentJS ){

            // If momentJS is not available, don't initialize the directive!
            if( ! momentJS ){
                $log.warn('Calendry not instantiated due to missing momentJS library');
                return;
            }


            var _document       = $document[0],
                _monthMapCache  = $cacheFactory('monthMap'),
                _docFragsCache  = $cacheFactory('docFrags'),
                // Cache keys
                _monthMapKey    = 'YYYY_MM',
                _eventMapKey    = 'YYYY_MM_DD',
                // Default settings
                _defaults       = {
                    forceListView   : false,
                    daysOfWeek      : momentJS.weekdaysShort(),
                    currentMonth    : momentJS(),
                    dayCellClass    : 'day-node',
                    onMonthChange   : function(){},
                    onDropEnd       : function(){}
                };


            /**
             * Instantiable method for creating month maps.
             * @param monthStartMoment
             * @constructor
             */
            function MonthMap( monthStartMoment ){
                this.monthStart         = monthStartMoment;
                this.monthEnd           = momentJS(this.monthStart).endOf('month');
                this.calendarStart      = momentJS(this.monthStart).subtract(this.monthStart.day(), 'day');
                this.calendarEnd        = momentJS(this.monthEnd).add((6 - this.monthEnd.day()), 'day');
                this.calendarDayCount   = Math.abs(this.calendarEnd.diff(this.calendarStart, 'days'));
                this.calendarDays       = (function( daysInCalendar, calendarStart, _array ){
                    for( var _i = 0; _i <= daysInCalendar; _i++ ){
                        _array.push(momentJS(calendarStart).add('days', _i));
                    }
                    return _array;
                })( this.calendarDayCount, this.calendarStart, []);
            }


            /**
             * Generate a list of moment objects, grouped by weeks visible on the calendar.
             * @param MomentJS _month : Pass in a moment object to derive the month, or the current month will be
             * used automatically.
             * @returns {Array}
             */
            function getMonthMap( _month ){
                var monthStart = momentJS.isMoment(_month) ? momentJS(_month).startOf('month') : momentJS({day:1}),
                    _cacheKey  = monthStart.format(_monthMapKey);

                // In cache?
                if( _monthMapCache.get(_cacheKey) ){
                    return _monthMapCache.get(_cacheKey);
                }

                // Hasn't been created yet, do so now.
                _monthMapCache.put(_cacheKey, new MonthMap(monthStart));

                // Return the cache item
                return _monthMapCache.get(_cacheKey);
            }


            /**
             * Get the id attribute for a day cell.
             * @param MomentJS | MomentObj
             * @returns {string}
             */
            function getDayCellID( MomentObj ){
                return _defaults.dayCellClass + '-' + MomentObj.format('YYYY_MM_DD');
            }


            /**
             * Passing in a monthMapObj, this will return a document fragment of the
             * composed calendar DOM elements.
             * @note: This caches documentFragments the first time they're generated, and
             * returns CLONED elements each time thereafter.
             * @param MonthMap | monthMapObj
             * @returns {DocumentFragment|Object|*}
             */
            function getCalendarFragment( monthMapObj ){
                var momentNow   = momentJS(),
                    cacheKey    = monthMapObj.monthStart.format('YYYY_MM');

                // If already exists in the cache, just return a cloned instance immediately
                if( _docFragsCache.get(cacheKey) ){
                    return _docFragsCache.get(cacheKey).cloneNode(true);
                }

                // Hasn't been created yet, do so now.
                var docFragment = _document.createDocumentFragment();

                for( var _i = 0, _len = monthMapObj.calendarDays.length; _i < _len; _i++ ){
                    var cell    = _document.createElement('div'),
                        inMonth = monthMapObj.calendarDays[_i].isSame(monthMapObj.monthStart, 'month') ? 'month-incl' : 'month-excl',
                        isToday = monthMapObj.calendarDays[_i].isSame(momentNow, 'day') ? 'is-today' : '';

                    cell.setAttribute('id', getDayCellID(monthMapObj.calendarDays[_i]));
                    cell.className = _defaults.dayCellClass + ' ' + inMonth + ' ' + isToday;
                    cell.innerHTML = '<span class="date-num">'+monthMapObj.calendarDays[_i].format('DD')+'<small>'+monthMapObj.calendarDays[_i].format('MMM')+'</small></span>';

                    docFragment.appendChild(cell);
                }

                _docFragsCache.put(cacheKey, docFragment);

                // Return a CLONED instance of the document fragment
                return _docFragsCache.get(cacheKey).cloneNode(true);
            }


            /**
             * Hex to RGB conversion utility
             * @param hex
             * @returns {{r: number, g: number, b: number}}
             */
            function hexToRgb(hex) {
                // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
                var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
                hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                    return r + r + g + g + b + b;
                });

                var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : null;
            }


            function _link( $scope, $element, attrs, Controller, transcludeFn ){
                /**
                 * Pass in the directive element and the monthMap we use to generate the
                 * calendar DOM elements.
                 * @param $element
                 * @param monthMap
                 * @returns null
                 */
                function renderCalendarLayout( monthMap ){
                    // Rebuild the calendar layout (no events attached, just days)
                    var $renderTo = angular.element($element[0].querySelector('.calendar-render')),
                        weekRows  = Math.ceil( monthMap.calendarDayCount / 7 );

                    // Set row classes on calendar-body
                    angular.element($element[0].querySelector('.calendry-body'))
                        .removeClass('week-rows-4 week-rows-5 week-rows-6')
                        .addClass('week-rows-' + weekRows);

                    // Render the calendar body
                    //$renderTo.empty().append( getCalendarFragment(monthMap) );
                    var fragment = getCalendarFragment(monthMap);

                    // DECORATE EVERY DAY ELEMENT WITH A _moment PROPERTY VIA .data()
                    Array.prototype.slice.call(fragment.childNodes).forEach(function(node, index){
                        fragment.childNodes[index] = angular.element(node).data('_moment', monthMap.calendarDays[index]);
                    });

                    $renderTo.empty().append(fragment);
                }


                /**
                 * Receive an event list as an array, and update the UI.
                 * @param eventList array
                 */
                function renderEvents( eventList ){
                    // Clear all previously rendered events
                    angular.element($element[0].querySelectorAll('.event-cell')).remove();

                    // Variables
                    var mapped = {};

                    // Loop through every event object and create _moment property, and
                    // append to mapped
                    eventList.forEach(function(eventObj){
                        eventObj._moment = momentJS(eventObj.startLocalized, momentJS.ISO_8601);
                        var mappedKey    = eventObj._moment.format(_eventMapKey);
                        if( ! mapped[mappedKey] ){
                            mapped[mappedKey] = [];
                        }
                        mapped[eventObj._moment.format(_eventMapKey)].push(eventObj);
                    });

                    /**
                     * Transclude function callback; note the $cloned element is implicitly
                     * set by the transcludeFn, and below we use .bind() to pass in the $dayNode
                     * @param $dayNode
                     * @param $cloned
                     * @private
                     */
                    function _transcluder( $dayNode, $cloned ){
                        $dayNode.append($cloned);
                    }

                    /**
                     * Loop through every day in the calendar and look for events to
                     * render.
                     * @note: the transcluder function in the loop, by default, passes in
                     * $cloned as the first argument. but since we're using .bind(), it
                     * re-orders the arguments so that $dayNode is the first arg, THEN
                     * $cloned
                     */
                    $scope.instance.monthMap.calendarDays.forEach(function( dayMoment ){
                        var eventsForDay = mapped[dayMoment.format(_eventMapKey)];
                        if( eventsForDay ){
                            var $dayNode = angular.element($element[0].querySelector('#' + getDayCellID(dayMoment)));
                            if( $dayNode ){
                                for(var _i = 0, _len = eventsForDay.length; _i < _len; _i++){
                                    var $newScope       = $scope.$new(/*true*/);
                                    $newScope.eventObj  = eventsForDay[_i];
                                    transcludeFn($newScope, _transcluder.bind(null, $dayNode));
                                }
                            }
                        }
                    });
                }


                // Any time the monthMap model changes, re-render.
                $scope.$watch('instance.monthMap', function( monthMapObj ){
                    if( monthMapObj ){
                        renderCalendarLayout(monthMapObj);
                    }
                });


                // Watch for changes to events property
                $scope.$watch('events', function(eventList){
                    if( angular.isArray(eventList) ){
                        renderEvents(eventList);

                        // Sortable
                        //Array.prototype.slice.call($element[0].querySelectorAll('.day-node')).forEach(function(node){
                        //    Sortable.create(node, {
                        //        group: 'day',
                        //        draggable: '.event-cell',
                        //        sort: false,
                        //        onAdd: function(ev){
                        //            var landingDayMoment = angular.element(this.el).data('_moment').clone(),
                        //                eventObj         = angular.element(ev.item).data('$scope').eventObj;
                        //            $scope.instance.onDropEnd.apply(Controller, [landingDayMoment, eventObj]);
                        //        }
                        //    });
                        //});

                    }
                });

                // Event click handler
//                angular.element($element[0].querySelector('.calendry-body')).on('click', function(event){
//                    // Ghetto delegation from the parent
//                    var delegator = this,
//                        target    = (function( _target ){
//                            while( ! _target.classList.contains('event-cell') ){
//                                if(_target === delegator){_target = null; break;}
//                                _target = _target.parentNode;
//                            }
//                            return _target;
//                        })(event.target);
//
//                    //console.log(target);
//                });

            }


            return {
                restrict: 'A',
                scope: {
                    instance: '=calendry'
                },
                replace: true,
                templateUrl: '/calendry',
                transclude: true,
                link: _link,
                controller: ['$scope', function( $scope ){

                    var Controller = this;

                    $scope.instance = angular.extend(Controller, _defaults, ($scope.instance || {}));

                    this.goToCurrentMonth = $scope.goToCurrentMonth = function(){
                        $scope.instance.currentMonth = momentJS();
                    };

                    this.goToPrevMonth = $scope.goToPrevMonth = function(){
                        $scope.instance.currentMonth = momentJS($scope.instance.currentMonth).subtract({months:1});
                    };

                    this.goToNextMonth = $scope.goToNextMonth = function(){
                        $scope.instance.currentMonth = momentJS($scope.instance.currentMonth).add({months:1});
                    };

                    this.toggleListView = $scope.toggleListView = function(){
                        $scope.instance.forceListView = !$scope.instance.forceListView;
                    };

                    $scope.$watch('instance.currentMonth', function( monthMoment ){
                        if( monthMoment ){
                            $scope.instance.monthMap = getMonthMap(monthMoment);
                            // Dispatch callback
                            $scope.instance.onMonthChange.apply(Controller, [$scope.instance.monthMap]);
                        }
                    });

                    $scope.$watch('instance.events', function( events ){
                        if( events ){
                            $scope.events = events;
                        }
                    });

                    $scope.eventFontColor = function( color ){
                        var rgb = hexToRgb(color),
                            val = Math.round(((rgb.r * 299) + (rgb.g * 587) + (rgb.b * 114)) / 1000);
                        return (val > 125) ? '#000000' : '#FFFFFF';
                    };
                }]
            };
        }
    ]);

})( window, window.angular );
angular.module('schedulizer.app').

    factory('Helpers', ['_moment', function factory(_moment){

        this.range = function( start, end ){
            var arr = [];
            for(var i = start; i <= end; i++){
                arr.push(i);
            }
            return arr;
        };

        this.repeatTypeHandleOptions = function(){
            return [
                {label: 'Days', value: 'daily'},
                {label: 'Weeks', value: 'weekly'},
                {label: 'Months', value: 'monthly'},
                {label: 'Years', value: 'yearly'}
            ];
        };

        this.repeatIndefiniteOptions = function(){
            return [
                {label: 'Forever', value: true},
                {label: 'Until', value: false}
            ];
        };

        this.weekdayRepeatOptions = function(){
            return [
                {label: 'Sun', value: 1},
                {label: 'Mon', value: 2},
                {label: 'Tue', value: 3},
                {label: 'Wed', value: 4},
                {label: 'Thu', value: 5},
                {label: 'Fri', value: 6},
                {label: 'Sat', value: 7}
            ];
        };

        this.repeatMonthlyMethodOptions = function(){
            return {
                specific    : 'specific',
                dynamic     : 'ordinal'
            };
        };

        this.repeatMonthlyDynamicWeekOptions = function(){
            return [
                {label: 'First', value: 1},
                {label: 'Second', value: 2},
                {label: 'Third', value: 3},
                {label: 'Fourth', value: 4},
                {label: 'Last', value: 5}
            ];
        };

        this.repeatMonthlyDynamicWeekdayOptions = function(){
            return [
                {label: 'Sunday', value: 1},
                {label: 'Monday', value: 2},
                {label: 'Tuesday', value: 3},
                {label: 'Wednesday', value: 4},
                {label: 'Thursday', value: 5},
                {label: 'Friday', value: 6},
                {label: 'Saturday', value: 7}
            ];
        };

        this.eventColorOptions = function(){
            return [
                {value: '#A3D900'},
                {value: '#3A87AD'},
                {value: '#DE4E56'},
                {value: '#BFBFFF'},
                {value: '#FFFF73'},
                {value: '#FFA64D'},
                {value: '#CCCCCC'},
                {value: '#00B7FF'},
                {value: '#222222'}
            ];
        };

        return this;
    }]);
angular.module('schedulizer.app').

    filter('numberContraction', function($filter) {

        var suffixes = ["th", "st", "nd", "rd"];

        return function(input) {
            var relevant = (input < 20) ? input : input % (Math.floor(input / 10) * 10);
            var suffix   = (relevant <= 3) ? suffixes[relevant] : suffixes[0];
            return suffix;
        };
    });
angular.module('schedulizer.app').

    controller('CtrlCalendar', ['$rootScope', '$scope', '$http', '$cacheFactory', 'API',
        function( $rootScope, $scope, $http, $cacheFactory, API ){

            // $scope.calendarID is ng-init'd from the view!
            var _cache = $cacheFactory('calendarData');

            /**
             * Receive a month map object from calendry and setup the request as
             * you see fit.
             * @param monthMapObj
             * @returns {HttpPromise}
             * @private
             */
            function _fetch( monthMapObj ){
                return $http.get(API._routes.generate('api.eventList', [$scope.calendarID]), {cache:_cache, params:{
                    start: monthMapObj.calendarStart.format('YYYY-MM-DD'),
                    end: monthMapObj.calendarEnd.format('YYYY-MM-DD')
                }});
            }

            /**
             * Handlers for calendry stuff.
             * @type {{onMonthChange: Function, onDropEnd: Function}}
             */
            $scope.instance = {
                onMonthChange: function( monthMap ){
                    _fetch(monthMap).then(function( resp ){
                        $scope.instance.events = resp.data;
                    });
                },
                onDropEnd: function( landingMoment, eventObj ){
                    console.log(landingMoment, eventObj);
                }
            };

            /**
             * calendar.refresh IS NOT issued by the calendry directive; it comes
             * from other things in the app.
             */
            $rootScope.$on('calendar.refresh', function(){
                _cache.removeAll();
                _fetch($scope.instance.monthMap, true).then(function( resp ){
                    $scope.instance.events = resp.data;
                });
            });

        }
    ]);
angular.module('schedulizer.app').

    controller('CtrlCalendarForm', ['$scope', '$q', '$window', 'ModalManager', 'API',
        function( $scope, $q, $window, ModalManager, API ){

            // Show loading message
            $scope._ready       = false;
            $scope._requesting  = false;

            // Create requests promise queue, always loading available timezones list
            var _requests = [API.timezones.get().$promise];

            // If calendarID is available; try to load it, and push to the requests queue
            if( ModalManager.data.calendarID ){
                _requests.push(API.calendar.get({id:ModalManager.data.calendarID}).$promise);
            }

            // When all requests are finished; proceed...
            $q.all(_requests).then(function( returned ){
                $scope.timezoneOptions = returned[0];
                $scope.entity = returned[1] || new API.calendar({
                    defaultTimezone: $scope.timezoneOptions[$scope.timezoneOptions.indexOf('America/Denver')]
                });
                $scope._ready = true;
            }, function( resp ){
                console.log(resp);
            });

            // Save the resource
            $scope.submitHandler = function(){
                $scope._requesting = true;
                // If entity already has ID, $update, otherwise $save (create), and bind callback
                ($scope.entity.id ? $scope.entity.$update() : $scope.entity.$save()).then(
                    function( resp ){
                        $scope._requesting = false;
                        $window.location.href = API._routes.generate('dashboard',['calendars','manage',resp.id]);
                    }
                );
            };
        }
    ]);
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
                console.log('called');
                $scope.entity.$delete().then(function( resp ){
                    if( resp.ok ){
                        $rootScope.$emit('calendar.refresh');
                        ModalManager.classes.open = false;
                    }
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
            //// Did the user click to edit an event that's an alias?
            //$scope.warnAliased = ModalManager.data.eventObj.aliased || false;
            //
            //// If aliased, show the message
            //if( $scope.warnAliased ){
            //    $scope._ready = true;
            //}
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