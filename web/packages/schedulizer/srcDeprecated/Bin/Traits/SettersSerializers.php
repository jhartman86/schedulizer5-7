<?php namespace Concrete\Package\Schedulizer\Src\Bin\Traits {

    use \Doctrine\Common\PropertyChangedListener;

    /**
     * Class SettersSerializers; Note: classes using this trait must both:
     * have changetrackingpolicy annotation of NOTIFY, and implement
     * NotifyPropertyChanged interface.
     * @see http://doctrine-orm.readthedocs.org/en/latest/reference/change-tracking-policies.html
     * @package Concrete\Package\Schedulizer\Src\Bin\Traits
     */
    trait SettersSerializers {

        /** ----------- DOCTRINE CHANGE NOTIFICATION PLUMBING ----------- */

        private $_listeners = array();

        public function addPropertyChangedListener(PropertyChangedListener $listener){
            $this->_listeners[] = $listener;
        }

        protected function onPropertyChanged($propName, $oldValue, $newValue){
            if( $this->_listeners ){
                foreach($this->_listeners AS $listener){
                    $listener->propertyChanged($this, $propName, $oldValue, $newValue);
                }
            }
        }

        /** ----------- ^ DOCTRINE CHANGE NOTIFICATION PLUMBING ^ ----------- */

        /**
         * Set properties on the object via passing in an array. Note - BEFORE the
         * property gets set on the object, we check if its different than the CURRENT
         * property and if so, notify a listener.
         * @param array $properties
         * @return self
         */
        public function setPropertiesFromArray( array $properties ){
            foreach($properties as $prop => $value) {
                // If CURRENT value != passed in value; notify listener
                if( $this->{$prop} != $value ){
                    $this->onPropertyChanged($prop, $this->{$prop}, $value);
                }
                $this->{$prop} = $value;
            }
            return $this;
        }

        /**
         * @param $object
         * @return $this
         */
        public function setPropertiesFromObject( $object ){
            foreach($object as $prop => $value) {
                // If CURRENT value != passed in value; notify listener
                if( $this->{$prop} != $value ){
                    $this->onPropertyChanged($prop, $this->{$prop}, $value);
                }
                $this->{$prop} = $value;
            }
            return $this;
        }

        /**
         * @param $mixed
         * @return $this
         */
        public function mergePropertiesFrom( $mixed ){
            if( is_array($mixed) ){
                $this->setPropertiesFromArray( $mixed );
            }
            if( is_object($mixed) ){
                $this->setPropertiesFromObject( $mixed );
            }
            return $this;
        }


        /**
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            return (object) get_object_vars($this);
        }

    }

}