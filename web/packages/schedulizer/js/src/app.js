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