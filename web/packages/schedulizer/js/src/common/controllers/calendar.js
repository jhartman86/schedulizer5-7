angular.module('schedulizer.app').

    controller('CtrlCalendar', ['$rootScope', '$scope', '$http', '$cacheFactory', 'API',
        function( $rootScope, $scope, $http, $cacheFactory, API ){

            // $scope.calendarID is ng-init'd from the view!
            var _cache = $cacheFactory('calendarData');

            var _fields = [
                'eventID', 'eventTimeID', 'calendarID', 'title',
                'eventColor', 'isAllDay', 'isSynthetic', 'computedStartUTC',
                'computedStartLocal'
            ];

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
                    end: monthMapObj.calendarEnd.format('YYYY-MM-DD'),
                    fields: _fields.join(',')
                }});
            }

            /**
             * Handlers for calendry stuff.
             * @type {{onMonthChange: Function, onDropEnd: Function}}
             */
            $scope.instance = {
                parseDateField: 'computedStartLocal',
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