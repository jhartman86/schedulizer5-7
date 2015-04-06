<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use Config;
    use User;
    use \Exception;
    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\JsonResponse;
    use \Concrete\Core\Controller\Controller AS CoreController;

    class CalendarHandler extends CoreController {

        protected $_response;

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
         * Get a calendar by its ID
         * @param null $id
         * @return mixed
         */
        public function get( $id ){
            $calendarObj = Calendar::getByID( $id );
            // Set response data
            $this->_response->setData($calendarObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Create a new calendar
         * @todo: permissions, pass user (api key determines?), and timezone options
         */
        public function post(){
            $user               = new User();
            $postData           = $this->sanitizeIncoming(json_decode(file_get_contents('php://input')));
            $postData->ownerID  = ($user->getUserID() >= 1) ? $user->getUserID() : 0;
            $calendarObj        = Calendar::create($postData);
            // Set response data
            $this->_response->setData($calendarObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_CREATED);
        }

        /**
         * Update an existing calendar
         * @param $id
         */
        public function put( $id ){
            $postData       = $this->sanitizeIncoming(json_decode(file_get_contents('php://input')));
            $calendarObj    = Calendar::getByID($id);
            $calendarObj->update($postData);
            // Set response data
            $this->_response->setData($calendarObj);
            $this->_response->setStatusCode(JsonResponse::HTTP_OK);
        }

        /**
         * Delete a calendar
         * @param $id
         * @return stdObject
         */
        public function delete( $id ){
            Calendar::getByID( $id )->delete();
            return (object) array('ok' => true);
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