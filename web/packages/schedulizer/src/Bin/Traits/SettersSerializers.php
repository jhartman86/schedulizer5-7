<?php namespace Concrete\Package\Schedulizer\Src\Bin\Traits {

    trait SettersSerializers {

        /**
         * @param array $properties
         * @return self
         */
        public function setPropertiesFromArray( array $properties ){
            foreach($properties as $key => $prop) {
                $this->{$key} = $prop;
            }
            return $this;
        }

        /**
         * @param $object
         * @return $this
         */
        public function setPropertiesFromObject( $object ){
            if( is_object($object) ){
                foreach($object AS $prop => $value){
                    $this->{$prop} = $value;
                }
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