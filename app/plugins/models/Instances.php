<?php

class plugins_models_Instances
{

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_dba = null;

	public function getInstanceDataFromHost($host)
	{
		$hv = new Zend_Validate_Hostname();
		if ($hv->isValid($host) === false)
			throw new Exception('Invalid host');

		$select = $this->getDBA()->select()->from('t_instance', array(
				'dbname' => 'AES_DECRYPT('.$this->getDBA()->quoteIdentifier('dbname').', \'47d1a7b66de2853ea0c6a201b560425c\')',
				'dbpass' => 'AES_DECRYPT('.$this->getDBA()->quoteIdentifier('dbpass').', \'47d1a7b66de2853ea0c6a201b560425c\')'
		))
		->where('enabled = ?', 1)
		->where('host = ?', $host);

		return $this->getDBA()->fetchRow($select);
	}

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDBA()
	{
		if ($this->_dba === null) $this->setDBA(Zend_Db_Table::getDefaultAdapter());
		return $this->_dba;
	}

	/**
	 * @param Zend_Db_Adapter_Abstract $adapter
	 * @throws Exception
	 */
	public function setDBA(Zend_Db_Adapter_Abstract $adapter)
	{
		$this->_dba = $adapter;
		return $this;
	}
}