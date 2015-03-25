<?php namespace Concrete\Package\Schedulizer\Controller\SinglePage\Dashboard\Schedulizer\Calendars {

    use Config;
    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Concrete\Package\Schedulizer\Controller\DashboardController;

    class Manage extends DashboardController {

        public function view( $calendarID = null ){
            try {
                $calendarObj = Calendar::getByID( $calendarID );
                if( is_object($calendarObj) ){
                    $this->set('calendarObj', $calendarObj);
                    $this->set('pageTitle', $calendarObj->getTitle());
                    return;
                }
            }catch(\Exception $e){
                $this->redirect('/dashboard/schedulizer/calendars');
            }
        }

    }

}