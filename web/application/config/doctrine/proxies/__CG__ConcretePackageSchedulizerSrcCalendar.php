<?php

namespace DoctrineProxies\__CG__\Concrete\Package\Schedulizer\Src;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Calendar extends \Concrete\Package\Schedulizer\Src\Calendar implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', 'title', 'ownerID', 'defaultTimezone', 'id', 'createdUTC', 'modifiedUTC');
        }

        return array('__isInitialized__', 'title', 'ownerID', 'defaultTimezone', 'id', 'createdUTC', 'modifiedUTC');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Calendar $proxy) {
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
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', array());

        return parent::__toString();
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
    public function getOwnerID()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOwnerID', array());

        return parent::getOwnerID();
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultTimezone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDefaultTimezone', array());

        return parent::getDefaultTimezone();
    }

    /**
     * {@inheritDoc}
     */
    public function getCalendarTimezoneObj()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCalendarTimezoneObj', array());

        return parent::getCalendarTimezoneObj();
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
