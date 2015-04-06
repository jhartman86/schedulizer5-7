<?php namespace Concrete\Package\Schedulizer\Controller\SinglePage\Dashboard\Schedulizer {

    use Config;
    use \Concrete\Package\Schedulizer\Controller\DashboardController;
    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Concrete\Package\Schedulizer\Src\Bin\TimeConversion;

    class Calendars extends DashboardController {

        public function view(){
            $this->set('calendars', Calendar::fetchAll());
            $this->set('conversionHelper', new TimeConversion());
        }

    }

}