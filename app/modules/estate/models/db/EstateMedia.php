<?php

class estate_models_db_EstateMedia extends Zend_Db_Table_Abstract
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
	 * @param array $parent_id
	 */
	public function findPrimaryImage($parent_id, $parent_table)
	{
		$query = $this->getAdapter()->select()
			->from($this->getTableName(), array('primary_image-name' => 'name', 'primary_image-id'=>'id', 'primary_image-md5'=>'checksum'))
			->where('parent_id = ?', $parent_id)
			->where('parent_table = ?', $parent_table)
			->where('relation = ?', 'primary');
		return $this->getAdapter()->fetchRow($query);
	}
}