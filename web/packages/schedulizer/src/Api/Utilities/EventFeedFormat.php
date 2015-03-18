<?php namespace Concrete\Package\Schedulizer\Src\Api\Utilities {

    use DateTime;
    use DateTimeZone;

    class EventFeedFormat implements \JsonSerializable {

        protected $formatted = array();

        /**
         * @param array $results
         */
        public function __construct( array $results = array() ){
            foreach($results AS $eventData){
                array_push($this->formatted, (object)array(
                    'id'            => $eventData['id'],
                    'title'         => $eventData['title'],
                    'start'         => $eventData['startLocalized'] . '+00:00',
                    'end'           => $eventData['endLocalized'] . '+00:00',
                    'allDay'        => (bool)$eventData['isAllDay'] ? true : false,
                    'color'         => $eventData['eventColor'],
                    'isAlias'       => $eventData['isAlias'],
                    'isRepeating'   => $eventData['isRepeating'],
                    'repeatMethod'  => $eventData['repeatTypeHandle']
                ));
            }
        }


        /**
         * @return array|mixed
         */
        public function jsonSerialize(){
            return $this->formatted;
        }
    }

}