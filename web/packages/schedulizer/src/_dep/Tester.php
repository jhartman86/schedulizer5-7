<?php namespace Concrete\Package\Schedulizer\Src {

    /**
     * Class Tester
     * @definition({"table":"SchedulizerTester"})
     */
    class Tester {

        /** @definition({"type":"integer","nullable":false}) */
        protected $id;

        /** @definition({"type":"string","nullable":true}) */
        protected $title;

        /** @definition({"type":"boolean","nullable":false}) */
        protected $isValid = true;

        /** @definition({"type":"string","nullable":true}) */
        protected $meow;

        /** @definition({"type":"string","nullable":true}) */
        protected $thisis;

        /** @definition({"type":"boolean","nullable":false}) */
        protected $gettingBetter = true;

        /** @definition({"type":"datetime","nullable":false}) */
        protected $dt;

        /** some note in here eh? @id @var OK */
        protected $propWithoutDef;

        protected $propnodocblock;

        public function __construct( $properties ){
            foreach($properties AS $prop => $val){
                $this->{$prop} = $val;
            }
        }

    }

}