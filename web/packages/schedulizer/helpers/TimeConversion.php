<?php namespace Concrete\Package\Schedulizer\Helpers {

    date_default_timezone_set('UTC');

    use DateTime;
    use DateTimeZone;
    use Concrete\Package\Schedulizer\Src\Interfaces\BaseInterface;

    class TimeConversion {

        public static function localize( DateTime $dateTime, DateTimeZone $timezone ){
            $converted = clone $dateTime;
            $converted->setTimezone($timezone);
            return $converted;
        }


        public static function localizeWithFormat( DateTime $dateTime, DateTimeZone $timezone, $format = BaseInterface::TIMESTAMP_FORMAT ){
            return self::localize($dateTime, $timezone)->format($format);
        }

    }

}