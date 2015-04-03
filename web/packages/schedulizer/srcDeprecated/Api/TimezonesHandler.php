<?php namespace Concrete\Package\Schedulizer\Src\Api {

    use DateTimeZone;
    use Gettext\Languages\Exporter\Json;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\JsonResponse;
    use \Concrete\Core\Controller\Controller AS CoreController;

    class TimezonesHandler extends CoreController {

        protected $requestObj;

        /**
         * http://{domain}/_schedulizer/timezones
         *  ?region=africa|america|antarctica|arctic|asia|atlantic|australia|europe|indian|pacific
         *  ?country=ISO_COUNTRY_CODE (2 letters)
         * @param Request $request
         * @return JsonResponse
         * @throws \Exception
         */
        public function getList( Request $request ){
            $response         = new JsonResponse();
            $this->requestObj = $request;

            // If country code is set, honor ONLY that (ignore region) and return
            if( isset($this->requestParams()->country) ){
                return $this->filterByCountry( $response );
            }

            // If region is set, do filter
            if( isset($this->requestParams()->region) ){
                return $this->filterByRegion($response);
            }

            // No filters, send the whole kit and kaboodle
            $response->setData(DateTimeZone::listIdentifiers());
            $response->setStatusCode(JsonResponse::HTTP_OK);
            return $response;
        }


        /**
         * Determine what to send back and set on the injected $response.
         * @param $response
         * @return mixed
         */
        protected function filterByCountry( $response ){
            $countryCode  = strtoupper($this->requestParams()->country);
            $timezoneList = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);

            // If requestParams()->country is a VALID country, a list should be available
            if( !empty($timezoneList) ){
                $response->setData(DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode));
                $response->setStatusCode(JsonResponse::HTTP_OK);
                return $response;
            }

            // If the $timezoneList was empty, an invalid country was passed
            $response->setStatusCode(JsonResponse::HTTP_NOT_ACCEPTABLE);
            return $response;
        }


        /**
         * Determine what to send back and set on the injected $response.
         * @param $response
         * @return mixed
         */
        protected function filterByRegion( $response ){
            $region = null;

            switch( strtolower($this->requestParams()->region) ){
                case 'africa': $region = DateTimeZone::AFRICA; break;
                case 'america': $region = DateTimeZone::AMERICA; break;
                case 'antarctica': $region = DateTimeZone::ANTARCTICA; break;
                case 'arctic': $region = DateTimeZone::ARCTIC; break;
                case 'asia': $region = DateTimeZone::ASIA; break;
                case 'atlantic': $region = DateTimeZone::ATLANTIC; break;
                case 'australia': $region = DateTimeZone::AUSTRALIA; break;
                case 'europe': $region = DateTimeZone::EUROPE; break;
                case 'indian': $region = DateTimeZone::INDIAN; break;
                case 'pacific': $region = DateTimeZone::PACIFIC; break;
            }

            // Is $region NOT null anymore? We can get a list...
            if( ! is_null($region) ){
                $response->setData(DateTimeZone::listIdentifiers($region));
                $response->setStatusCode(JsonResponse::HTTP_OK);
                return $response;
            }

            // Region was still null (thus invalid)
            $response->setStatusCode(JsonResponse::HTTP_NOT_ACCEPTABLE);
            return $response;
        }


        /**
         * Get a parsed stdObject of the query string.
         * @return stdObject
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