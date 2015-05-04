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