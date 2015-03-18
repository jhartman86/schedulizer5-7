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