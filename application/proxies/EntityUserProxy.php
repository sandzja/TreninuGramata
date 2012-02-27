<?php

namespace proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class EntityUserProxy extends \Entity\User implements \Doctrine\ORM\Proxy\Proxy
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

    public function getEmail()
    {
        $this->__load();
        return parent::getEmail();
    }

    public function setEmail($email)
    {
        $this->__load();
        return parent::setEmail($email);
    }

    public function getMoto()
    {
        $this->__load();
        return parent::getMoto();
    }

    public function setMoto($moto)
    {
        $this->__load();
        return parent::setMoto($moto);
    }

    public function getHeight()
    {
        $this->__load();
        return parent::getHeight();
    }

    public function setHeight($height)
    {
        $this->__load();
        return parent::setHeight($height);
    }

    public function getWeight()
    {
        $this->__load();
        return parent::getWeight();
    }

    public function setWeight($weight)
    {
        $this->__load();
        return parent::setWeight($weight);
    }

    public function getBirtDate()
    {
        $this->__load();
        return parent::getBirtDate();
    }

    public function setBirthDate($birthDate)
    {
        $this->__load();
        return parent::setBirthDate($birthDate);
    }

    public function getFacebookUserId()
    {
        $this->__load();
        return parent::getFacebookUserId();
    }

    public function setFacebookUserId($facebookUserId)
    {
        $this->__load();
        return parent::setFacebookUserId($facebookUserId);
    }

    public function getTwitterUserId()
    {
        $this->__load();
        return parent::getTwitterUserId();
    }

    public function setTwitterUserId($twitterUserId)
    {
        $this->__load();
        return parent::setTwitterUserId($twitterUserId);
    }

    public function getGoogleUserId()
    {
        $this->__load();
        return parent::getGoogleUserId();
    }

    public function setGoogleUserId($googleUserId)
    {
        $this->__load();
        return parent::setGoogleUserId($googleUserId);
    }

    public function getSessionId()
    {
        $this->__load();
        return parent::getSessionId();
    }

    public function getSessionValidTime()
    {
        $this->__load();
        return parent::getSessionValidTime();
    }

    public function setSessionValidTime($sessionValidTime)
    {
        $this->__load();
        return parent::setSessionValidTime($sessionValidTime);
    }

    public function getUpdatedTime()
    {
        $this->__load();
        return parent::getUpdatedTime();
    }

    public function setUpdatedTime($updatedTime)
    {
        $this->__load();
        return parent::setUpdatedTime($updatedTime);
    }

    public function getProfileImageUrl()
    {
        $this->__load();
        return parent::getProfileImageUrl();
    }

    public function setProfileImageUrl($profileImageUrl)
    {
        $this->__load();
        return parent::setProfileImageUrl($profileImageUrl);
    }

    public function getTrainingPlans()
    {
        $this->__load();
        return parent::getTrainingPlans();
    }

    public function setTrainingPlans($trainingPlans)
    {
        $this->__load();
        return parent::setTrainingPlans($trainingPlans);
    }

    public function addTrainingPlan(\Entity\TrainingPlan $trainingPlan)
    {
        $this->__load();
        return parent::addTrainingPlan($trainingPlan);
    }

    public function getWorkouts()
    {
        $this->__load();
        return parent::getWorkouts();
    }

    public function setWorkouts($workouts)
    {
        $this->__load();
        return parent::setWorkouts($workouts);
    }

    public function addWorkout(\Entity\Workout $workout)
    {
        $this->__load();
        return parent::addWorkout($workout);
    }

    public function getRecords()
    {
        $this->__load();
        return parent::getRecords();
    }

    public function addRecord(\Entity\Record $record)
    {
        $this->__load();
        return parent::addRecord($record);
    }

    public function getPosts()
    {
        $this->__load();
        return parent::getPosts();
    }

    public function addPost($post)
    {
        $this->__load();
        return parent::addPost($post);
    }

    public function getLikePosts()
    {
        $this->__load();
        return parent::getLikePosts();
    }

    public function getFollowers()
    {
        $this->__load();
        return parent::getFollowers();
    }

    public function getFollowings()
    {
        $this->__load();
        return parent::getFollowings();
    }

    public function addFollowing($following)
    {
        $this->__load();
        return parent::addFollowing($following);
    }

    public function countDistances()
    {
        $this->__load();
        return parent::countDistances();
    }

    public function countTime()
    {
        $this->__load();
        return parent::countTime();
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
        return array('__isInitialized__', 'id', 'name', 'email', 'moto', 'height', 'weight', 'birthDate', 'facebookUserId', 'twitterUserId', 'googleUserId', 'sessionId', 'sessionValidTime', 'updatedTime', 'profileImageUrl', 'trainingPlans', 'workouts', 'records', 'posts', 'likePosts', 'followers', 'followings');
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