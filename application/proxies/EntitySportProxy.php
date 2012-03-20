<?php

namespace proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class EntitySportProxy extends \Entity\Sport implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }
    
    
    public function getId()
    {
        $this->__load();
        return parent::getId();
    }

    public function setId($id)
    {
        $this->__load();
        return parent::setId($id);
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function getCaloriesFactor()
    {
        $this->__load();
        return parent::getCaloriesFactor();
    }

    public function setCaloriesFactor($caloriesFactor)
    {
        $this->__load();
        return parent::setCaloriesFactor($caloriesFactor);
    }

    public function getIntensitySpeed()
    {
        $this->__load();
        return parent::getIntensitySpeed();
    }

    public function setIntensitySpeed($intensitySpeed)
    {
        $this->__load();
        return parent::setIntensitySpeed($intensitySpeed);
    }

    public function isSynced()
    {
        $this->__load();
        return parent::isSynced();
    }

    public function setSynced($isSynced)
    {
        $this->__load();
        return parent::setSynced($isSynced);
    }

    public function getUser()
    {
        $this->__load();
        return parent::getUser();
    }

    public function setUser(\Entity\User $user)
    {
        $this->__load();
        return parent::setUser($user);
    }

    public function getTrainingPlans()
    {
        $this->__load();
        return parent::getTrainingPlans();
    }

    public function addTrainingPlan(\Entity\TrainingPlan $trainingPlan)
    {
        $this->__load();
        return parent::addTrainingPlan($trainingPlan);
    }

    public function populate(array $values)
    {
        $this->__load();
        return parent::populate($values);
    }

    public function toArray()
    {
        $this->__load();
        return parent::toArray();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'caloriesFactor', 'intensitySpeed', 'isSynced', 'user', 'trainingPlans');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}