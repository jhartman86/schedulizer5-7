<?php namespace Concrete\Package\Schedulizer\Src\Permission\Key {

    use \Concrete\Package\Schedulizer\Src\Permission\Assignment\SchedulizerCalendarAssignment;
    //use PermissionKey; /** @see \Concrete\Core\Permission\Key\Key */

    /**
     * Note - the $permissions->can{WhateverCamelCasedHere} gets dynamically converted into
     * handle format (eg. canCreateTag -> "create_tag") and then the PermissionKey is loaded
     * from that:
     *
     * Class SchedulizerKey
     * @package Concrete\Package\Schedulizer\Src\Permission\Key
     * @extends \Concrete\Core\Permission\Key\Key
     */
    class SchedulizerCalendarKey extends \Concrete\Core\Permission\Key\Key { //PermissionKey {

        public static function getList(){
            return parent::getList('schedulizer_calendar');
        }

        public function getPermissionAssignmentObject(){
            if (is_object($this->permissionObject)) {
                $className = $this->permissionObject->getPermissionAssignmentClassName();
                $targ = \Core::make($className);
                $targ->setPermissionObject($this->permissionObject);
            } else {
                $targ = new SchedulizerCalendarAssignment();
            }
            $targ->setPermissionKeyObject($this);
            return $targ;
        }

    }

}