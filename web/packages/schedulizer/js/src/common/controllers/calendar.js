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

            $rootScope.$on('calendar.refresh', function(){
                _cache.removeAll();
                _fetch($scope.instance.monthMap, true).then(function( resp ){
                    $scope.instance.events = resp.data;
                });
            });

        }
    ]);