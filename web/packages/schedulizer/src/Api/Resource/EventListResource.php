<?php namespace Concrete\Package\Schedulizer\Src\Api\Resource {

    use \DateTime;
    use \DateTimeZone;
    use \Concrete\Package\Schedulizer\Src\EventList;

    class EventListResource extends \Concrete\Package\Schedulizer\Src\Api\ApiDispatcher {

        /**
         * List resource get method.
         * @param null $calendarID
         */
        protected function httpGet( $calendarID = null ){
            $eventListObj = new EventList(array($calendarID));
            $this->setStartDate($eventListObj);
            $this->setEndDate($eventListObj);
            $this->setFetchColumns($eventListObj);
            $this->setResponseData($eventListObj->getSerializable());
        }

        /**
         * eg. ?start=2015-04-02
         * @param EventList $eventList
         */
        private function setStartDate( EventList $eventList ){
            if( !empty($this->requestParams()->start) ){
                $eventList->setStartDate(new DateTime($this->requestParams()->start, new DateTimeZone('UTC')));
            }
        }

        /**
         * eg. ?end=2015-04-02
         * @param EventList $eventList
         */
        private function setEndDate( EventList $eventList ){
            if( !empty($this->requestParams()->end) ){
                $eventList->setEndDate(new DateTime($this->requestParams()->end, new DateTimeZone('UTC')));
            }
        }

        /**
         * Comma-delimited list of include fields.
         * eg. ?fields=eventID,calendarID
         * @param EventList $eventList
         */
        private function setFetchColumns( EventList $eventList ){
            if( !empty($this->requestParams()->fields) ){
                $eventList->includeColumns(explode(',', $this->requestParams()->fields));
            }
        }

    }

}