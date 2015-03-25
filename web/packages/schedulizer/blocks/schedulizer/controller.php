<?php namespace Concrete\Package\Schedulizer\Block\Schedulizer;

    use Loader;
    use Concrete\Package\Schedulizer\Src\Calendar;
    use Concrete\Package\Schedulizer\Src\EventTag;

    class Controller extends \Concrete\Core\Block\BlockController {

        protected $blockData;

        protected $btTable 									= 'btSchedulizer';
        protected $btInterfaceWidth 						= '585';
        protected $btInterfaceHeight						= '440';
        protected $btDefaultSet                             = 'artsy';
        protected $btCacheBlockRecord 						= false; // @todo: renable for production: true;
        protected $btCacheBlockOutput 						= false; // @todo: renable for production: true;
        protected $btCacheBlockOutputOnPost 				= false; // @todo: renable for production: true;
        protected $btCacheBlockOutputForRegisteredUsers 	= false;
        protected $btCacheBlockOutputLifetime 				= 0;


        /**
         * @return string
         */
        public function getBlockTypeName(){
            return t("Schedulizer");
        }


        /**
         * @return string
         */
        public function getBlockTypeDescription(){
            return t("Display Schedulizer");
        }


        public function view(){
            $this->set('schedulizerData', json_decode($this->blockData));
        }


        public function add(){
            $this->edit();
        }


        public function composer(){
            $this->edit();
        }


        public function edit(){
            $this->requireAsset('select2');
            $this->set('calendarList', $this->calendarListResults());
            $this->set('tagList', $this->eventTagList());
        }

        protected function calendarListResults(){
            if( $this->_calendarList === null ){
                $this->_calendarList = Calendar::findAll();
            }
            return $this->_calendarList;
        }

        protected function eventTagList(){
            if( $this->_eventTagList === null ){
                $this->_eventTagList = EventTag::findAll();
            }
            return $this->_eventTagList;
        }


        /**
         * Called automatically by framework
         * @param array $args
         */
        public function save( $args ){
            parent::save(array('blockData' => json_encode((object)array(
                'calendarIDs'   => (array) $args['calendarIDs'],
                'startDate'     => (new \DateTime($args['startDate'], new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                'endDate'       => (new \DateTime($args['endDate'], new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                'tagIDs'        => (array) $args['eventTags']
            ))));
        }

    }