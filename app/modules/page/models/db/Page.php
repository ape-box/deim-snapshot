<?php

class page_models_db_Page extends Zend_Db_Table_Abstract
{
	/**
	 * The table name.
	 *
	 * @var string
	 */
	protected $_name = 't_page';

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
	protected $_sequence = true;
}