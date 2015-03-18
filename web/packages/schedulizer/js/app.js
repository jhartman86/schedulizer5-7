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
                        calendar:   routeBase.api + '/calendar',
                        event:      routeBase.api + '/event',
                        eventList:  routeBase.api + '/event/list',
                        timezones:  routeBase.api + '/timezones'
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
                        scope.$apply(Controller.$setViewValue(this.get()));
                    }
                }));
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

    filter('numberContraction', function($filter) {

        var suffixes = ["th", "st", "nd", "rd"];

        return function(input) {
            var relevant = (input < 20) ? input : input % (Math.floor(input / 10) * 10);
            var suffix   = (relevant <= 3) ? suffixes[relevant] : suffixes[0];
            return suffix;
        };
    });
angular.module('schedulizer.app').

    controller('CtrlCalendar', ['$rootScope', '$scope', '$http', '$calendry', 'API',
        function( $rootScope, $scope, $http, $calendry, API ){

            // $scope.calendarID is ng-init'd from the view!

            $scope.calendarSettings = {};

            $scope.$watch('calendarSettings.currentMonth', function(val){
                console.log($scope.calendarSettings);
            });

            /**
             * Receive a month map object from calendry and setup the request as
             * you see fit.
             * @param monthMapObj
             * @returns {HttpPromise}
             * @private
             */
            function _fetch( monthMapObj ){
                return $http.get(API._routes.generate('api.eventList', [$scope.calendarID]), {cache:false, params:{
                    start: monthMapObj.calendarStart.format('YYYY-MM-DD'),
                    end: monthMapObj.calendarEnd.format('YYYY-MM-DD')
                }});
            }


            /**
             * Callback to get the controller once the calendar is loaded.
             */
            $calendry('[calendry]', function( CalendryCtrl ){
                CalendryCtrl.onMonthChange(function( map ){
                    _fetch(map).then(function( resp ){
                        CalendryCtrl.setEvents(resp.data);
                    });
                });

                // Listen for emitted changes
                $rootScope.$on('calendar.refresh', function(){
                    _fetch(CalendryCtrl.getMonthMap()).then(function( resp ){
                        CalendryCtrl.setEvents(resp.data);
                    });
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
            var _requests = [API.timezones.query().$promise];

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
angular.module('schedulizer.app').

    factory('Helpers', function factory(){

        function _range( start, end ){
            var arr = [];
            for(var i = start; i <= end; i++){
                arr.push(i);
            }
            return arr;
        }

        return {
            range: _range,
            eventDefaults: {
                repeatTypeHandleOptions: [
                    {label: 'Days', value: 'daily'},
                    {label: 'Weeks', value: 'weekly'},
                    {label: 'Months', value: 'monthly'},
                    {label: 'Years', value: 'yearly'}
                ],
                repeatIndefiniteOptions: [
                    {label: 'Forever', value: true},
                    {label: 'Until', value: false}
                ],
                weekdayRepeatOptions: [
                    {label: 'Sun', value: 1},
                    {label: 'Mon', value: 2},
                    {label: 'Tue', value: 3},
                    {label: 'Wed', value: 4},
                    {label: 'Thu', value: 5},
                    {label: 'Fri', value: 6},
                    {label: 'Sat', value: 7}
                ],
                repeatMonthlyMethodOptions: {
                    specific    : 1,
                    dynamic     : 0
                },
                repeatMonthlyDynamicWeekOptions: [
                    {label: 'First', value: 1},
                    {label: 'Second', value: 2},
                    {label: 'Third', value: 3},
                    {label: 'Fourth', value: 4},
                    {label: 'Last', value: 5}
                ],
                repeatMonthlyDynamicWeekdayOptions: [
                    {label: 'Sunday', value: 1},
                    {label: 'Monday', value: 2},
                    {label: 'Tuesday', value: 3},
                    {label: 'Wednesday', value: 4},
                    {label: 'Thursday', value: 5},
                    {label: 'Friday', value: 6},
                    {label: 'Saturday', value: 7}
                ],
                eventColorOptions: [
                    {value: '#A3D900'},
                    {value: '#3A87AD'},
                    {value: '#DE4E56'},
                    {value: '#BFBFFF'},
                    {value: '#FFFF73'},
                    {value: '#FFA64D'},
                    {value: '#CCCCCC'},
                    {value: '#00B7FF'},
                    {value: '#222222'}
                ]
            }
        };
    });
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
     * Easy way to gain access to the directive controller from anywhere. The callback
     * will only happen *after* the directive has been linked and initialized.
     * Usage: $calendry('element-selector', function( CalendryController ){ ... })
     */
    factory('$calendry', ['$q', function( $q ){
        return function( selector, callback ){
            var $element = angular.isString(selector) ? angular.element(document.querySelector(selector)) : selector,
                $defer   = $element.data('$calendryDefer');

            if( ! $defer ){
                $defer = $q.defer();
                $element.data('$calendryDefer', $defer);
            }

            if( ! angular.isFunction(callback) ){
                return $defer;
            }

            $defer.promise.then(callback);

            return $defer;
        };
    }]).

    /**
     * Calendry directive
     */
    directive('calendry', ['$cacheFactory', '$document', '$log', '$timeout', '$q', 'MomentJS', '$calendry',
        function factory( $cacheFactory, $document, $log, $timeout, $q, momentJS, $calendry ){

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
                    daysOfWeek      : momentJS.weekdaysShort(),
                    currentMonth    : momentJS(),
                    dayCellClass    : 'day-node',
                    eventCellClass  : 'event-node',
                    // Callbacks
                    onMonthChange   : null
                };


            /**
             * Instantiable method for creating month maps.
             * @param monthStartMoment
             * @constructor
             */
            function MonthMap( monthStartMoment ){
                this.monthStart         = monthStartMoment;
                this.monthEnd           = momentJS(this.monthStart).endOf('month');
                this.calendarStart      = momentJS(this.monthStart).subtract('day', this.monthStart.day());
                this.calendarEnd        = momentJS(this.monthEnd).add('day', (6 - this.monthEnd.day()));
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


            function _link( $scope, $element, attrs, Controller, transcludeFn ){

                $scope.goToCurrentMonth = Controller.goToCurrentMonth;
                $scope.goToPrevMonth    = Controller.goToPrevMonth;
                $scope.goToNextMonth    = Controller.goToNextMonth;
                $scope.toggleListView   = Controller.toggleListView;


                /**
                 * Pass in the directive element and the monthMap we use to generate the
                 * calendar DOM elements. Returns a promise so we can chain it - such that DOM
                 * manipulation takes place, THEN something else (even though DOM is syncronous, makes
                 * the syntax easier :)
                 * @param $element
                 * @param monthMap
                 * @returns {promise|Q.promise}
                 */
                function renderCalendarLayout( monthMap ){
                    // Rebuild the calendar layout (no events attached, just days)
                    var deferred  = $q.defer(),
                        $renderTo = angular.element($element[0].querySelector('.calendar-render')),
                        weekRows  = Math.ceil( monthMap.calendarDayCount / 7 );

                    // Set row classes on calendar-body
                    angular.element($element[0].querySelector('.calendry-body')).removeClass('week-rows-4 week-rows-5 week-rows-6')
                        .addClass('week-rows-' + weekRows);

                    // Render the calendar body
                    $renderTo.empty().append( getCalendarFragment(monthMap) );

                    // Resolve
                    deferred.resolve();

                    return deferred.promise;
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
                    $scope.monthMap.calendarDays.forEach(function( dayMoment ){
                        var eventsForDay = mapped[dayMoment.format(_eventMapKey)];
                        if( eventsForDay ){
                            var $dayNode = angular.element($element[0].querySelector('#' + getDayCellID(dayMoment)));
                            if( $dayNode ){
                                for(var _i = 0, _len = eventsForDay.length; _i < _len; _i++){
                                    var $newScope       = $scope.$new(/*true*/);
                                    $newScope.eventObj  = eventsForDay[_i];
                                    transcludeFn($newScope, _transcluder.bind(null, $dayNode));
//                                    transcludeFn($newScope, function( $cloned ){
//                                        $dayNode.append($cloned);
//                                    });
                                }
                            }
                        }
                    });
                }


                // Any time the monthMap model changes, re-render.
                $scope.$watch('monthMap', function( monthMapObj ){
                    if( monthMapObj ){
                        renderCalendarLayout(monthMapObj).then(function(){
                            if( angular.isFunction($scope.settings.onMonthChange) ){
                                $scope.settings.onMonthChange.apply(Controller);
                            }
                        });
                    }
                });


                // Watch for changes to events property
                $scope.$watch('events', function(eventList){
                    if( angular.isArray(eventList) ){
                        renderEvents(eventList);
                    }
                });


                // Event Handler
                // @todo: bind .moment object to each day cell as data attribute
//                angular.element($element[0].querySelector('.calendry-body')).on('click', function(event){
//                    console.log(this, event);
//                });


                // Calendry element is fully available; resolve the service accessor
                $calendry($element).resolve(Controller);
            }


            return {
                restrict: 'A',
                scope: {
                    settings: '=calendry'
                },
                replace: true,
                templateUrl: '/calendry',
                transclude: true,
                //terminal: true,
                link: _link,
                controller: ['$scope', function( $scope ){
                    var ControllerInstance = this;

                    $scope.forceListView = false;

                    // Initialize settings by merging in defaults
                    $scope.settings = angular.extend(_defaults, ($scope.settings || {}));

                    // Gets called automatically on init with a valid moment
                    $scope.$watch('settings.currentMonth', function(){
                        $scope.monthMap = getMonthMap( $scope.settings.currentMonth );
                    });


                    //----- Publicly accessible methods on the controller instance ----- //
                    /**
                     * Navigate to the current month.
                     * @return void
                     */
                    this.goToCurrentMonth = function(){
                        $scope.settings.currentMonth = momentJS();
                    };

                    /**
                     * Navigate to previous month.
                     * @return void
                     */
                    this.goToPrevMonth = function(){
                        $scope.settings.currentMonth = momentJS($scope.settings.currentMonth).subtract({months:1});
                    };

                    /**
                     * Navigate to next month.
                     * @return void
                     */
                    this.goToNextMonth = function(){
                        $scope.settings.currentMonth = momentJS($scope.settings.currentMonth).add({months:1});
                    };

                    /**
                     * Toggle list or calendar view (only applicable on larger devices; list view
                     * is forced on mobile)
                     * @return void
                     */
                    this.toggleListView = function(){
                        $scope.forceListView = !$scope.forceListView;
                    };

                    /**
                     * If a parameter _for is passed in (a moment object), get the month map for that
                     * month; otherwise return the *current* month map.
                     * @param moment _for optional
                     * @returns MonthMap
                     */
                    this.getMonthMap = function( _for ){
                        if( _for ){
                            return getMonthMap(_for);
                        }
                        return $scope.monthMap;
                    };

                    /**
                     * Pass in an event list (triggers re-render in link fn).
                     * @param eventList
                     */
                    this.setEvents = function( eventList ){
                        $scope.events = eventList;
                    };

                    /**
                     * Get the controller scope
                     * @returns $scope
                     */
                    this.getScope = function(){
                        return $scope;
                    };

                    /**
                     * Callback for when the monthmap changes
                     * @todo: this creates a new $watch, unncessarily since
                     * there is also a watch on the same property above.
                     * Set this up as queue that gets called by the other
                     * watch
                     * @param callback
                     */
                    this.onMonthChange = function( callback ){
                        $scope.$watch('monthMap', function( monthMapObj ){
                            if( monthMapObj ){
                                callback.apply(ControllerInstance, [monthMapObj]);
                            }
                        });
                    };
                }]
            };
        }
    ]);

})( window, window.angular );