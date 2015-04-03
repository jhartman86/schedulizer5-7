<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use \Exception;
    use DateTime;
    use DateTimeZone;
    use \Concrete\Package\Schedulizer\Src\EventList;
    use \Concrete\Package\Schedulizer\Src\Api\Utilities\EventFeedFormat;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\JsonResponse;
    use \Concrete\Core\Controller\Controller AS CoreController;

    class EventListHandler extends CoreController {

        protected $responseObj;
        protected $requestObj;
        protected $eventListObj;

        public function dispatch( Request $request, $calendarID = null ){
            $this->responseObj = new JsonResponse();
            $this->requestObj = $request;

            try {
                $this->execute($calendarID);
            }catch(Exception $e){
                $this->responseObj->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                $this->responseObj->setData((object) array(
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                    'file'  => $e->getFile()
                ));
            }

            return $this->responseObj;
        }


        protected function execute( $calendarID ){
            // Create event list
            $this->eventListObj = new EventList(array($calendarID));
            $this->eventListObj->setStartDate(new DateTime($this->requestParams()->start, new DateTimeZone('UTC')));
            //$this->eventListObj->setEndDate(new DateTime($this->requestParams()->end, new DateTimeZone('UTC')));
            $results = $this->eventListObj->get();
            $this->responseObj->setData(new EventFeedFormat($results));
            $this->responseObj->setStatusCode(JsonResponse::HTTP_OK);
        }


        /**
         * Get a parsed stdObject of the query string.
         * @return object
         */
        protected function requestParams(){
            if( $this->_requestParams === null ){
                parse_str($this->requestObj->getQueryString(), $parsed);
                $this->_requestParams = (object) $parsed;
            }
            return $this->_requestParams;
        }

    }

}