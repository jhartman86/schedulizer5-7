<?php

namespace DoctrineProxies\__CG__\Concrete\Package\Schedulizer\Src;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Event extends \Concrete\Package\Schedulizer\Src\Event implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'calendarID', 'title', 'description', 'startUTC', 'endUTC', 'isOpenEnded', 'isAllDay', 'useCalendarTimezone', 'timezoneName', 'eventColor', 'isRepeating', 'repeatTypeHandle', 'repeatEvery', 'repeatIndefinite', 'repeatEndUTC', 'repeatMonthlyMethod', 'ownerID', 'fileID', 'calendarInstance', 'eventRepeatSettings', 'eventTags', 'id', 'createdUTC', 'modifiedUTC');
        }

        return array('__isInitialized__', 'calendarID', 'title', 'description', 'startUTC', 'endUTC', 'isOpenEnded', 'isAllDay', 'useCalendarTimezone', 'timezoneName', 'eventColor', 'isRepeating', 'repeatTypeHandle', 'repeatEvery', 'repeatIndefinite', 'repeatEndUTC', 'repeatMonthlyMethod', 'ownerID', 'fileID', 'calendarInstance', 'eventRepeatSettings', 'eventTags', 'id', 'createdUTC', 'modifiedUTC');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Event $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setCalendarInstance(\Concrete\Package\Schedulizer\Src\Calendar $calendar)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCalendarInstance', array($calendar));

        return parent::setCalendarInstance($calendar);
    }

    /**
     * {@inheritDoc}
     */
    public function addRepeatSetting(\Concrete\Package\Schedulizer\Src\EventRepeat $repeater)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addRepeatSetting', array($repeater));

        return parent::addRepeatSetting($repeater);
    }

    /**
     * {@inheritDoc}
     */
    public function addTag(\Concrete\Package\Schedulizer\Src\EventTag $tag)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addTag', array($tag));

        return parent::addTag($tag);
    }

    /**
     * {@inheritDoc}
     */
    public function getEventTags()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEventTags', array());

        return parent::getEventTags();
    }

    /**
     * {@inheritDoc}
     */
    public function setStartUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStartUTC', array());

        return parent::setStartUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function setEndUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEndUTC', array());

        return parent::setEndUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function setRepeatEndUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRepeatEndUTC', array());

        return parent::setRepeatEndUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function setCalendarTimezone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCalendarTimezone', array());

        return parent::setCalendarTimezone();
    }

    /**
     * {@inheritDoc}
     */
    public function postPersistEvent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'postPersistEvent', array());

        return parent::postPersistEvent();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', array());

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function getCalendarID()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCalendarID', array());

        return parent::getCalendarID();
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitle', array());

        return parent::getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', array());

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function getStartUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStartUTC', array());

        return parent::getStartUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function getEndUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEndUTC', array());

        return parent::getEndUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function getIsAllDay()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsAllDay', array());

        return parent::getIsAllDay();
    }

    /**
     * {@inheritDoc}
     */
    public function getUseCalendarTimezone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUseCalendarTimezone', array());

        return parent::getUseCalendarTimezone();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimezoneName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTimezoneName', array());

        return parent::getTimezoneName();
    }

    /**
     * {@inheritDoc}
     */
    public function getEventColor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEventColor', array());

        return parent::getEventColor();
    }

    /**
     * {@inheritDoc}
     */
    public function getIsRepeating()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsRepeating', array());

        return parent::getIsRepeating();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatTypeHandle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepeatTypeHandle', array());

        return parent::getRepeatTypeHandle();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatEvery()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepeatEvery', array());

        return parent::getRepeatEvery();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatIndefinite()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepeatIndefinite', array());

        return parent::getRepeatIndefinite();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatEndUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepeatEndUTC', array());

        return parent::getRepeatEndUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatMonthlyMethod()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepeatMonthlyMethod', array());

        return parent::getRepeatMonthlyMethod();
    }

    /**
     * {@inheritDoc}
     */
    public function getOwnerID()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOwnerID', array());

        return parent::getOwnerID();
    }

    /**
     * {@inheritDoc}
     */
    public function getFileID()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFileID', array());

        return parent::getFileID();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatSettings()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepeatSettings', array());

        return parent::getRepeatSettings();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'jsonSerialize', array());

        return parent::jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function setPropertiesFromArray(array $properties)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPropertiesFromArray', array($properties));

        return parent::setPropertiesFromArray($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function setPropertiesFromObject($object)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPropertiesFromObject', array($object));

        return parent::setPropertiesFromObject($object);
    }

    /**
     * {@inheritDoc}
     */
    public function update($data)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'update', array($data));

        return parent::update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'delete', array());

        return parent::delete();
    }

    /**
     * {@inheritDoc}
     */
    public function save()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'save', array());

        return parent::save();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedUTC', array());

        return parent::setCreatedUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function setModifiedUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setModifiedUTC', array());

        return parent::setModifiedUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function getID()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getID', array());

        return parent::getID();
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getModifiedUTC', array());

        return parent::getModifiedUTC();
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedUTC()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedUTC', array());

        return parent::getCreatedUTC();
    }

}
