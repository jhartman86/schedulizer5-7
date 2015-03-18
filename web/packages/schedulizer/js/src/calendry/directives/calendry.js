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
                        eventObj._moment = momentJS(eventObj.start);
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