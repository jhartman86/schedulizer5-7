<?php namespace Concrete\Package\Schedulizer\Block\Schedulizer;

    use Loader;

    class Controller extends \Concrete\Core\Block\BlockController {

        protected $btTable 									= 'btSchedulizerBlock';
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

        }


        public function add(){
            $this->edit();
        }


        public function composer(){
            $this->edit();
        }


        public function edit(){

        }


        /**
         * Called automatically by framework
         * @param array $args
         */
        public function save( $args ){
            parent::save($args);
        }

    }