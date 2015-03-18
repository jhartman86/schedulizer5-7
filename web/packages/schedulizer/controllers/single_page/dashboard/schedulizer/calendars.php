<?php namespace Concrete\Package\Schedulizer\Controller\SinglePage\Dashboard\Schedulizer {

    use Config;
    use Concrete\Package\Schedulizer\Controller\DashboardController;
    use Concrete\Package\Schedulizer\Src\Calendar;
    use Concrete\Package\Schedulizer\Src\Helpers\TimeConversion;

    class Calendars extends DashboardController {

        public function view(){
            $this->set('calendars', Calendar::findAll());
            $this->set('conversionHelper', new TimeConversion());
        }

        /**
         * @deprecate: User the API
         */
        public function add(){
            $calendarObj = Calendar::create(array(
                'title'             => 'Untitled',
                'defaultTimezone'   => Config::get('app.timezone'),
                'ownerID'           => $this->currentUser()->getUserID()
            ));
            $this->redirect('/dashboard/schedulizer/calendars/manage', $calendarObj->getID());
        }

    }

}