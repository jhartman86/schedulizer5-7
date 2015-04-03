<?php namespace Concrete\Package\Schedulizer\Src\Bin {

    /**
     * @package Concrete\Package\Schedulizer\Src\Bin
     */
    interface InterfacePersistable {
        public function setPropertiesFromArray( array $properties );
        public function setPropertiesFromObject( $object );
        public static function create( $data );
        public function update( $data );
        public function delete();
        public function save();
    }

}