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
                    {label: 'Forever', value: 1},
                    {label: 'Until', value: 0}
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