<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use User;
    use \Exception;
    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Concrete\Package\Schedulizer\Src\Event;
    use \Concrete\Package\Schedulizer\Src\EventTime;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\JsonResponse;
    use \Concrete\Core\Controller\Controller AS CoreController;

    class EventHandler extends CoreController {

        /**
         * Dispatch to the correct handler
         * @param Request $request
         * @param $id
         * @return JsonResponse
         */
        public function dispatch( Request $request, $id ){
            $this->_response = new JsonResponse();

            try {
                switch($request->getMethod()){
                    case 'GET':
                        $this->get($id);
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
         * Get an event by its ID
         * @param null $id
         * @return mixed
         */
        public function get( $id ){
            $eventObj = Event::getByID( $id );
            // Set response data
            $this->_response->setData($eventObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Create a new event
         * @throws \Exception
         * @todo: permissions, pass user (api key determines?), and timezone options
         */
        public function post(){
            $user                   = new User();
            $postData               = $this->sanitizeIncoming(json_decode(file_get_contents('php://input')));
            $calendarObj            = Calendar::getByID($postData->calendarID);
            if( ! $calendarObj ){
                throw new Exception("Calendar does not exist.");
            }
            $postData->ownerID      = ($user->getUserID() >= 1) ? $user->getUserID() : 0;
            $eventObj               = Event::createWithEventTimes($postData);
            // Set response data
            $this->_response->setData($eventObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_CREATED);
        }

        /**
         * @param $id
         * @throws \Exception
         */
        public function put( $id ){
            $postData = $this->sanitizeIncoming(json_decode(file_get_contents('php://input')));
            $eventObj = Event::getByID($id);
            if( ! $eventObj ){
                throw new Exception("Event with ID: {$id} does not exist.");
            }
            $eventObj->updateWithEventTimes($postData);
            // Set response data
            $this->_response->setData($eventObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Delete an event.
         * @param $id
         * @throws \Exception
         */
        public function delete( $id ){
            /** @var $eventObj Event */
            $eventObj = Event::getByID($id);
            if( ! $eventObj ){
                throw new Exception("Event with ID: {$id} does not exist.");
            }
            $eventObj->delete();
            // Set response data
            $this->_response->setData((object)array(
                'ok' => true
            ));
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Fields like id, createdUTC, modifiedUTC are handled internally;
         * so unset them if they're passed in
         */
        protected function sanitizeIncoming( $postData ){
            unset($postData->id);
            unset($postData->createdUTC);
            unset($postData->modifiedUTC);
            return $postData;
        }

    }

}