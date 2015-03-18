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

            // Provide API route helpers
            $provide.factory('Routes', function(){
                var _routes = {
                    api: {
                        calendar: '/_schedulizer/calendar',
                        event: '/_schedulizer/event',
                        timezones: '/_schedulizer/timezones'
                    },
                    views: {
                        calendarFormModal: '/calendar_form',
                        eventFormModal: '/event_form',
                        calendar: {
                            manage: '/dashboard/schedulizer/calendars/manage'
                        }
                    }
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

    factory('API', ['$resource',
       function( $resource ){
           var _methods = {
               update: {method:'PUT', params:{_method:'PUT'}}
           };

           return {
               calendar: $resource('/_schedulizer/calendar/:id', {id:'@id'}, angular.extend(_methods, {
                   // more custom methods here
               })),
               event: $resource('/_schedulizer/event/:id', {id:'@id'}, angular.extend(_methods, {
                   // more custom methods here
               })),
               timezones: $resource('/_schedulizer/timezones')
           };
       }
    ]);

    // Manually bootstrap the document
    angular.element(document).ready(function(){
        angular.bootstrap(document, ['schedulizer']);
    });

})(window, window.angular);