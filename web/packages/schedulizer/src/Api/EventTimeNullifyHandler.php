<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use User;
    use \DateTime;
    use \DateTimeZone;
    use \Exception;
    use \Concrete\Package\Schedulizer\Src\EventTime;
    use \Concrete\Package\Schedulizer\Src\EventTimeNullify;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\JsonResponse;
    use \Concrete\Core\Controller\Controller AS CoreController;

    class EventTimeNullifyHandler extends CoreController {

        protected $_response, $requestObj;

        /**
         * Dispatch to the correct handler
         * @param Request $request
         * @param $id
         * @return JsonResponse
         */
        public function dispatch( Request $request, $id ){
            $this->_response  = new JsonResponse();
            $this->requestObj = $request;

            try {
                switch($request->getMethod()){
                    case 'GET':
                        $this->get($request, $id);
                        break;
                    case 'POST':
                        $this->post();
                        break;
                    case 'PUT':
                        $this->put($id);
                        break;
                    case 'DELETE':
                        $this->delete($id);
                        break;
                }
            }catch(Exception $e){
                $this->_response->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                $this->_response->setData((object) array(
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                    'file'  => $e->getFile()
                ));
            }

            return $this->_response;
        }

        /**
         * Get an event time nullifier by its ID; OR (more commonly), pass a query parameter
         * eventTimeID in order to get a list of all nullifier for the eventTime.
         * @param null $id
         * @return mixed
         */
        public function get( $request, $id ){
            // If $id is null, we're getting a list of nullifiers by eventID from query param
            if( is_null($id) ){
                $list = EventTimeNullify::fetchAllByEventTimeID($this->requestParams()->eventTimeID);
                $this->_response->setData($list);
                $this->_response->setStatusCode(JsonResponse::HTTP_OK);
                return;
            }

            $nullifierObj = EventTimeNullify::getByID( $id );
            // Set response data
            $this->_response->setData($nullifierObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Create a new event
         * @throws \Exception
         * @todo: permissions, pass user (api key determines?), and timezone options
         */
        public function post(){
            $postData = $this->sanitizeIncoming(json_decode(file_get_contents('php://input')));
            /** @var $eventObj Event */
            $eventTimeObj = EventTime::getByID($postData->eventTimeID);
            if( ! $eventTimeObj ){
                throw new Exception("EventTime entity does not exist.");
            }

            $hideOnDate = new DateTime($postData->hideOnDate, new DateTimeZone('UTC'));
            $hideOnDate->setTime(0,0,0);

            $nullifierObj = EventTimeNullify::create(array(
                'eventTimeID' => $eventTimeObj->getID(),
                'hideOnDate'  => $hideOnDate->format('Y-m-d H:i:s')
            ));
            // Set response data
            $this->_response->setData($nullifierObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_CREATED);
        }

        /**
         * @todo: Do we ever need to update a nullifier?
         * @param $id
         * @throws \Exception
         */
//        public function put( $id ){
//            $postData = $this->sanitizeIncoming(json_decode(file_get_contents('php://input')));
//            $eventObj = Event::getByID($id);
//            if( ! $eventObj ){
//                throw new Exception("Event with ID: {$id} does not exist.");
//            }
//            $eventObj->update($postData);
//            $eventObj->setRepeaters($postData->repeatSettings);
//            // Set response data
//            $this->_response->setData($eventObj);
//            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
//        }

        /**
         * Delete an event.
         * @param $id
         * @throws \Exception
         */
        public function delete( $id ){
            /** @var $nullifierObj EventRepeatNullify */
            $nullifierObj = EventTimeNullify::getByID($id);
            if( ! $nullifierObj ){
                throw new Exception("Nullifier with ID: {$id} does not exist.");
            }
            $nullifierObj->delete();
            // Set response data
            $this->_response->setData((object)array(
                'ok' => true
            ));
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Parse request parameters.
         * @return object
         */
        protected function requestParams(){
            if( $this->_requestParams === null ){
                parse_str($this->requestObj->getQueryString(), $parsed);
                $this->_requestParams = (object) $parsed;
            }
            return $this->_requestParams;
        }

        /**
         * Fields like id, createdUTC, modifiedUTC are handled internally;
         * so unset them if they're passed in
         */
        protected function sanitizeIncoming( $postData ){
            unset($postData->id);
            return $postData;
        }

    }

}