angular.module('schedulizer.app').

    controller('CtrlCalendar', ['$rootScope', '$scope', '$http', '$calendry', 'API',
        function( $rootScope, $scope, $http, $calendry, API ){

            // $scope.calendarID is ng-init'd from the view!

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