<?php namespace Concrete\Package\Schedulizer\Src\Api\Utilities {

    use DateTime;
    use DateTimeZone;

    class EventFeedFormat implements \JsonSerializable {

        protected $formatted = array();

        /**
         * @param array $results
         */
        public function __construct( array $results = array() ){
            $dtzUTC = new DateTimeZone('UTC');

            foreach($results AS $eventData){
                array_push($this->formatted, (object)array(
                    'id'                => $eventData['eventID'],
                    'title'             => $eventData['title'],
                    'color'             => $eventData['eventColor'],
                    'synthetic'         => (bool)$eventData['synthetic'],
                    'timezone'          => new DateTimeZone($eventData['timezoneName']),
                    'startUTC'          => (new DateTime($eventData['startUTC'], $dtzUTC))->format('c'),
                    'startLocalized'    => (new DateTime($eventData['startLocal'], new DateTimeZone($eventData['timezoneName'])))->format('c'),
                    'endUTC'            => (new DateTime($eventData['endUTC'], $dtzUTC))->format('c'),
                    'endLocalized'      => (new DateTime($eventData['endLocal'], new DateTimeZone($eventData['timezoneName'])))->format('c')
                ));
            }

//            foreach($results AS $eventData){
//                $dtzEvent   = new DateTimeZone($eventData['timezoneName']);
//                $startLocal = new DateTime($eventData['startLocalized'], $dtzEvent);
//                $endLocal   = new DateTime($eventData['endLocalized'], $dtzEvent);
//
//                array_push($this->formatted, (object)array(
//                    'id'                => $eventData['id'],
//                    'title'             => $eventData['title'],
//                    'allDay'            => (bool)$eventData['isAllDay'],
//                    'color'             => $eventData['eventColor'],
//                    'isAlias'           => (bool)$eventData['isAlias'],
//                    'isRepeating'       => (bool)$eventData['isRepeating'],
//                    'repeatMethod'      => $eventData['repeatTypeHandle'],
//                    'timezone'          => $eventData['timezoneName'],
//                    'startLocalized'    => $startLocal->format('c'),
//                    'startUTC'          => $startLocal->setTimezone($dtzUTC)->format('c'),
//                    'endLocalized'      => $endLocal->format('c'),
//                    'endUTC'            => $endLocal->setTimezone($dtzUTC)->format('c')
////                    'startUTC'          => $eventData['startUTC'],
////                    'endUTC'            => $eventData['endUTC'],
////                    'startLocalized'    => $eventData['startLocalized'],
////                    'endLocalized'      => $eventData['endLocalized']
//                ));
//            }
        }


        /**
         * @return array|mixed
         */
        public function jsonSerialize(){
            return $this->formatted;
        }
    }

}