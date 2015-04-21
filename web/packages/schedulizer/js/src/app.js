/* global FastClick */
;(function( window, angular, undefined ){ 'use strict';

    angular.module('schedulizer', [
        'ngResource', 'schedulizer.app', 'mgcrea.ngStrap.datepicker', 'mgcrea.ngStrap.timepicker',
        'calendry', 'ui.select', 'ngSanitize'
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
                        calendar:       routeBase.api + '/calendar/:id',
                        event:          routeBase.api + '/event/:id',
                        eventList:      routeBase.api + '/event_list',
                        eventNullify:   routeBase.api + '/event_time_nullify/:eventTimeID/:id',
                        eventTags:      routeBase.api + '/event_tags/:id',
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
           function _methods(){
               return {
                   update: {method:'PUT', params:{_method:'PUT'}}
               };
           }

           return {
               calendar: $resource(Routes.generate('api.calendar'), {id:'@id'}, angular.extend(_methods(), {
                   // more custom methods here
               })),
               event: $resource(Routes.generate('api.event'), {id:'@id'}, angular.extend(_methods(), {
                   // more custom methods here
               })),
               eventNullify: $resource(Routes.generate('api.eventNullify'), {eventTimeID:'@eventTimeID',id:'@id'}, angular.extend(_methods(), {
                   // more custom methods
               })),
               eventTags: $resource(Routes.generate('api.eventTags'), {id:'@id'}, angular.extend(_methods(), {

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