<?php
class ZendX_Validate_Db_NoRecordExists extends Zend_Validate_Abstract
{
      private $_field;
      private $repository;

      const OK = '';

      protected $_messageTemplates = array(
          self::OK => "'%value%' allready in database"
      );

      public function __construct($entity, $field) {
      		$doctrine = Zend_Registry::get('doctrine');
      		$this->repository = $doctrine->getEntityManager()->getRepository($entity);
      		
            if(is_null($this->repository))
                  return null;
            $this->_field = $field;
      }

      public function isValid($value)
      {
            $this->_setValue($value);

            $funcName = 'findBy' . $this->_field;

            if(count($this->repository->$funcName($value))>0) {
                  $this->_error(self::OK);
                  return false;
            }

            return true;
      }
}
