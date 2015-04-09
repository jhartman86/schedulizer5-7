<?php namespace Concrete\Package\Schedulizer\Src {

    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class EventTag
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEventTag"})
     */
    class EventTag extends Persistant {

        use Crud;

        /**
         * @Column(type="string", length=255, nullable=false)
         */
        protected $tagText;

        /**
         * @Column(type="string", length=255, nullable=false)
         */
        protected $tagHandle;

        /**
         * @param null $string
         */
        public function __construct( $string = null ){
            if( $string !== null ){
                $this->tagName = $string;
            }
        }

        public function __toString(){
            return $this->tagText;
        }
    }

}