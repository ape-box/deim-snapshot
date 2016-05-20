<?php

class file_models_db_MediaFiles extends Zend_Db_Table_Abstract
{
	/**
	 * The table name.
	 *
	 * @var string
	 */
	protected $_name = 't_media_files';

	/**
	 * The primary key column or columns.
	 * A compound key should be declared as an array.
	 * You may declare a single-column primary key
	 * as a string.
	 *
	 * @var mixed
	 */
	protected $_primary = 'id';

	/**
	 * Define the logic for new values in the primary key.
	 * May be a string, boolean true, or boolean false.
	 *
	 * @var mixed
	 */
	protected $_sequence = false;

	/**
	 * @return string
	 */
	public function getTableName()
	{
		return $this->_name;
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function findNonBlobFields($id)
	{
		$select = $this->getAdapter()->select()
			->from($this->getTableName(), array('id', 'parent_id', 'parent_table', 'relation', 'name', 'mime', 'size', 'checksum', 'serialized_params'))
			->where('id = ?', $id);

		return $this->getAdapter()->fetchRow($select);
	}

	/**
	 * @param int $id
	 * @return string
	 */
	public function findRaw($id)
	{
		$select = $this->getAdapter()->select()
			->from($this->getTableName(), 'data')
			->where('id = ?', $id);

		return $this->getAdapter()->fetchOne($select);
	}
}