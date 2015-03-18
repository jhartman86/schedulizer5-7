<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use \Symfony\Component\HttpFoundation\JsonResponse;
    use \Concrete\Core\Controller\Controller AS CoreController;

    class TimezonesHandler extends CoreController {

        public function getList(){
            $response = new JsonResponse();
            $response->setData(\DateTimeZone::listIdentifiers());
            return $response;
        }

    }
}