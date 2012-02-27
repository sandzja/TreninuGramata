<?php
use Doctrine\ORM\EntityManager;
/**
 * @category BadWolf
 * @package BadWolf_Doctrine
 * @subpackage BadWolf_Doctrine_Auth_Adapter
 */

class ZendX_Doctrine2_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    /**
     * Doctrine Entity manager
     * 
     * @var EntityManager
     */
    protected $_em = null;

    /**
     * $_tableName - the table class to check
     *
     * @var string
     */
    protected $_tableClass = null;

    /**
     * $_tableName - the table name to check
     *
     * @var string
     */
    private $_tableName = null;

    /**
     * $_identityColumn - thecolumn to use as the identity
     *
     * @var string
     */
    protected $_identityColumn = null;

    /**
     * $_credentialColumn - columns to be used as the credentials
     *
     * @var string
     */
    protected $_credentialColumn = null;

    /**
     * $_identity - Identity value
     *
     * @var string
     */
    protected $_identity = null;

    /**
     * $_credential - Credential values
     *
     * @var string
     */
    protected $_credential = null;

    /**
     * $_credentialTreatment - Treatment applied to the credential, such as MD5() or PASSWORD()
     *
     * @var string
     */
    protected $_credentialTreatment = null;

    /**
     * $_authenticateResultInfo
     *
     * @var array
     */
    protected $_authenticateResultInfo = null;

    /**
     * $_resultRow - Results of database authentication query
     *
     * @var array
     */
    protected $_resultRow = null;

    /**
     * $_ambiguityIdentity - Flag to indicate same Identity can be used with
     * different credentials. Default is FALSE and need to be set to true to
     * allow ambiguity usage.
     *
     * 
     * @var boolean
     */
    protected $_ambiguityIdentity = false;

    /**
     *
     * @var ClassMetadata
     */
    protected $_tableMetadata = null;

    /**
     * __construct() - Sets configuration options
     *
     * @param EntityManager     $em
     * @param string            $tableName
     * @param string            $identityColumn
     * @param string            $credentialColumn
     * @param string            $credentialTreatment
     * @return void
     */
    public function  __construct(EntityManager $em = null, $tableClass = null,
        $identityColumn = null, $credentialColumn = null, $credentialTreatment = null)
    {
        if (null !== $em) {
            $this->setEntityManager($em);
        }

        if (null !== $tableClass ) {
            $this->setTableClass($tableClass);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }

        if (null !== $credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
    }

    /**
     * setEntityManager - sets the Doctrine entity manager
     *
     * @param EntityManager $em
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;
        return $this;
    }

    /**
     * getEntityManager - gets the Doctrine entity manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    /**
     * setTableName - set the table name to be used in the select query
     *
     * @param string $tableName
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setTableClass($tableClass)
    {
        $this->_tableClass = $tableClass;
        return $this;
    }

    /**
     * setIdentityColumn - set the column name to be used as the identity column
     *
     * @param string $identityColumn
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setIdentityColumn($identityColumn)
    {
        $this->_identityColumn = $identityColumn;
        return $this;
    }

    /**
     * setCredentialColumn - set the column name to be used as the credential column
     *
     * @param string $credentialColumn
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->_credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a parameterized string that is
     * used to transform or treat the input credential data
     *
     * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or algorithm. By specifying a
     * parameterized treatment string with this method, a developer may apply arbitrary SQL
     * upon input credential data.
     *
     * Examples:
     *
     * 'PASSWORD(?)'
     * 'MD5(?)'
     *
     * @param string $credentialTreatment
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setCredentialTreatment($credentialTreatment)
    {
        $this->_credentialTreatment = $credentialTreatment;
        return $this;
    }

    /**
     * setIdentity() - set the value to be used as the identity
     * 
     * @param string $value
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used, optionally can specify a treatment
     * to be used, should be supplied in parameterized form, such as 'MD5(?)' or 'PASSWORD(?)'
     * 
     * @param <type> $value
     * @return BadWolf_Doctrine_Auth_Adapter Provides a fluent interface
     */
    public function setCredential($value)
    {
        $this->_credential = $value;
        return $this;
    }

    /**
     * setAmbiguityIdentity() - sets a flag for usage of identical identities
     * with unique credentials. It accepts integers (0, 1) or boolean (true,
     * false) parameters. Default is false.
     *
     * @param  int|bool $flag
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setAmbiguityIdentity($flag)
    {
        if (is_integer($flag)) {
            $this->_ambiguityIdentity = (1 === $flag ? true : false);
        } elseif (is_bool($flag)) {
            $this->_ambiguityIdentity = $flag;
        }
        return $this;
    }

    /**
     * getAmbiguityIdentity() - returns TRUE for usage of multiple identical
     * identies with different credentials, FALSE if not used.
     *
     * @return bool
     */
    public function getAmbiguityIdentity()
    {
        return $this->_ambiguityIdentity;
    }

    /**
     * getResultRowObject() - Returns the result row as a detached Entity
     * 
     * @param array $returnColumns
     * @param array $omitColumns
     * @return class
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {
        
        if (!$this->_resultRow) {
            return false;
        }
 
        $entity = new $this->_tableClass;

        if (NULL !== $returnColumns) {

            foreach ($this->_tableMetadata->fieldMappings as $fieldMapping) {
                if (in_array($fieldMapping['columnName'], $returnColumns)) {
                    $value = $this->_tableMetadata->getFieldValue($this->_resultRow, $fieldMapping['fieldName']);
                    $this->_tableMetadata->setFieldValue($entity, $fieldMapping['fieldName'], $value);
                }
            }

        } elseif (NULL !== $omitColumns) {

            foreach ($this->_tableMetadata->fieldMappings as $fieldMapping) {
                if (!in_array($fieldMapping['columnName'], $omitColumns)) {
                    $value = $this->_tableMetadata->getFieldValue($this->_resultRow, $fieldMapping['fieldName']);
                    $this->_tableMetadata->setFieldValue($entity, $fieldMapping['fieldName'], $value);
                }
            }

        } else {

            $this->_em->detach($this->_resultRow);
            $entity = $this->_resultRow;
       
        }
        
        return $entity;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     * 
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        $dqlQuery = $this->_authenticateCreateSelect();

        $resultIdentities = $this->_authenticateQueryDql($dqlQuery);

        if (($authResult = $this->_authenticateValidateResultSet($resultIdentities)) instanceof Zend_Auth_Result) {
            return $authResult;
        }

        if (true === $this->getAmbiguityIdentity()) {
            $validIdentities = array();
            foreach ($resultIdentities as $identity) {
                if (1 === (int) $identity['badwolf_auth_credential_match']) {
                    $validIdentities[] = $identity;
                }
            }
            $resultIdentities = $validIdentities;
        }

        $authResult = $this->_authenticateValidateResult(array_shift($resultIdentities));
        return $authResult;
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with making sure
     * that this adapter was indeed setup properly with all required peices of information.
     *
     * @throws BadWolf_Doctrine_Auth_AdapterException - in the event that setup was not done properly
     * @return boolean
     */
    protected function _authenticateSetup()
    {
        $exceptionMessage = null;

        if ($this->_em === null) {
            $exceptionMessage = "An entity manager was not set.";
        } elseif ($this->_tableClass === null || $this->_tableClass == "") {
            $exceptionMessage = "A table class must be supplied for the BadWolf_Doctrine_Auth_Adapter authentication adapter.";
        } elseif ($this->_credentialColumn === null || $this->_credentialColumn == "") {
            $exceptionMessage = "A credential column must must be supplied for the BadWolf_Doctrine_Auth_Adapter authentication adaapter.";
        } elseif ($this->_identityColumn === null || $this->_identityColumn == "") {
            $exceptionMessage = "An identity column must must be supplied for the BadWolf_Doctrine_Auth_Adapter authentication adaapter.";
        } elseif ($this->_identity === null || $this->_identity == "") {
            $exceptionMessage = "A value for identity was not provided prior to authentication with BadWolf_Doctrine_Auth_Adapter.";
        } elseif ($this->_credential === null || $this->_credential == "") {
            $exceptionMessage = "A value for credential was not provided prior to authentication with BadWolf_Doctrine_Auth_Adapter.";
        }

        if (false === (($this->_tableMetadata = $this->_em->getMetadataFactory()->getMetadataFor($this->_tableClass)) instanceof Doctrine\ORM\Mapping\ClassMetadata)) {
            $exceptionMessage  = "The class name {$this->_tableClass} you submitted is not a valid Doctrine Entity";
        }

        if (null !== $exceptionMessage) {
            throw new ZendX_Doctrine2_Auth_AdapterException($exceptionMessage);
        }

        $this->_authenticateResultInfo = array(
            'code'      =>  Zend_Auth_Result::FAILURE,
            'identity'  =>  $this->_identity,
            'messages'  =>  array()
        );

        return true;
    }

    protected function _authenticateCreateSelect()
    {
        $doctrineConnection = $this->_em->getConnection();
        
        $this->_tableName = $this->_tableMetadata->table['name'];

        if (empty($this->_credentialTreatment) || strpos($this->_credentialTreatment, "?") === false) {
            $this->_credentialTreatment = '?';
        }
  
        
//        $credentialExpression = '(CASE WHEN ' .
//            ($this->_credentialColumn)
//            . ' = ' . $this->_credentialTreatment
//            . ' THEN 1 ELSE 0 END) AS badwolf_auth_credential_match';
        
        $credentialExpression = '(CASE WHEN ' .
            $doctrineConnection->quoteIdentifier($this->_credentialColumn)
            . ' = ' . $this->_credentialTreatment
            . ' THEN 1 ELSE 0 END) AS badwolf_auth_credential_match';

        $rsm = new Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult($this->_tableClass, 'a');
        $rsm->addScalarResult('badwolf_auth_credential_match', 'badwolf_auth_credential_match');

        foreach ($this->_tableMetadata->columnNames as $alias => $columnName) {
            $rsm->addFieldResult('a', $columnName, $alias);
        }

        $sql = "SELECT *, {$credentialExpression} FROM {$this->_tableName} WHERE {$this->_identityColumn} = ?";

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $this->_credential);
        $query->setParameter(2, $this->_identity);

        return $query;
    }

    protected function _authenticateQueryDql(Doctrine\ORM\NativeQuery $dqlQueryObject)
    {
        try {
            $resultIdentities = $dqlQueryObject->getResult();
        } catch (Exception $e) {
            throw new ZendX_Doctrine2_Auth_AdapterException('The supplied parameters to BadWolf_Doctrine_Auth_Adapter '
                                                           . 'failed to produce a valid DQL statement, please check table, table alias, '
                                                           . 'and column names for validity.', 0, $e);
        }

        return $resultIdentities;
    }

    protected function _authenticateValidateResultSet(array $resultIdentities)
    {
        if (count($resultIdentities) < 1) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
            return $this->_authenticateCreateAuthResult();
        } elseif (count($resultIdentities) > 1 && false === $this->getAmbiguityIdentity()) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
            return $this->_authenticateCreateAuthResult();
        }

        return true;
    }

    protected function _authenticateValidateResult($resultIdentity)
    {
        if ($resultIdentity['badwolf_auth_credential_match'] != '1') {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            return $this->_authenticateCreateAuthResult();
        }

        unset($resultIdentity['badwolf_auth_credential_match']);
        $this->_resultRow = $resultIdentity[0];

        $this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
        $this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
        return $this->_authenticateCreateAuthResult();
    }
    
    /**
     * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult()
    {
        return new Zend_Auth_Result(
            $this->_authenticateResultInfo['code'],
            $this->_authenticateResultInfo['identity'],
            $this->_authenticateResultInfo['messages']
        );
    }

}