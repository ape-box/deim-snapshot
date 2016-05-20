<?php

class menu_models_Admin
{
	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function getPaginator($params)
	{
		/* @var $select Zend_Db_Select */
		$select = Zend_Db_Table::getDefaultAdapter()
			->select()->from('t_menu', array('menu_id'=>'id', 'name'))->joinLeft('t_menu_text', 't_menu.id=t_menu_text.parent_id');

		foreach($params AS $name => $value)
		{
			if($value == '') continue;
			switch($name)
				{
					case 'search':
						$k = explode(' ', $value);
						foreach ($k as $k)
							$select->where("UPPER(label) LIKE UPPER(?) OR UPPER(link) LIKE UPPER(?)", "%$k%");
						break;
					case 'label':
						$name = Zend_Db_Table::getDefaultAdapter()->quote($name);
						$select->where("UPPER(label) LIKE UPPER(?)", "%$value%");
						break;
					case 'sidx':
						$sord = isset($params['sord']) ? $params['sord'] : 'ASC';
						$select->order($value . ' ' . $sord);
						break;
				}
		}
		$select->where("locale = ?", 'it_IT');
		return Zend_Paginator::factory($select);
	}

	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function fetchAllPages()
	{
		/* @var $select Zend_Db_Select */
		$select = Zend_Db_Table::getDefaultAdapter()
			->select()->from('t_page_text', array('id', 'title'))
			->where("locale = ?", 'it_IT');

		return Zend_Db_Table::getDefaultAdapter()->fetchAll($select);
	}

	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function fetchAllMenuItems()
	{
		/* @var $select Zend_Db_Select */
		$select = Zend_Db_Table::getDefaultAdapter()
			->select()->from('t_menu_text')
			->where("locale = ?", 'it_IT')
			->where("parent_id = ?", 1)
			->order('order ASC');

		return Zend_Db_Table::getDefaultAdapter()->fetchAll($select);
	}

	public function getDefaultMenuItems()
	{
		/* @var $select Zend_Db_Select */
		$select = Zend_Db_Table::getDefaultAdapter()->select()
			->from('t_menu', array('menu_id'=>'id', 'name'))->joinLeft('t_menu_text', 't_menu.id=t_menu_text.parent_id')
			->where(new Zend_Db_Expr('t_menu.id = (SELECT MIN(id) FROM t_menu)'))
			->order('order ASC');
		return Zend_Db_Table::getDefaultAdapter()->fetchAll($select);
	}

	/**
	 * @param int $id
	 */
	public function find($id, $locale = 'it_IT')
	{
		if (false == Zend_Locale::isLocale($locale))
			throw new Zend_Locale_Exception('Invalid locale format');

		$validate_digits = new Zend_Validate_Digits();
		if (false == $validate_digits->isValid($id))
			throw new Zend_Db_Table_Exception('Invalid index format');

		/* @var $table Zend_Db_Table */
		$table = new menu_models_db_MenuText();
		/* @var $data Zend_Db_Table_Rowset */
		$data = $table->find($id, $locale);
		if ($data->count() !== 1)
			return array();
		else
			$data = $data->current();

		return $data->toArray();
	}

	/**
	 * @param array $params dati da salvare
	 * @return int $id
	 */
	public function save($params)
	{
		$t_menu_text = array();
		$f_digits = new Zend_Filter_Digits();
		if (!isset($params['locale']))
			$params['locale'] = 'it_IT'; // MERDA !!!
		foreach($params AS $name => $value)
			{
				if (empty($value)) continue;
				switch($name)
					{
						case 'id':
						case 'parent_id':
						case 'page_id':
							$value = $f_digits->filter($value);
							if (empty($value)) continue;
						case 'label':
						case 'link':
						case 'order':
						case 'locale':
							$t_menu_text[$name] = $value;
							break;
					}
			}

		$table = new menu_models_db_Menu();
		if (!isset($t_menu_text['parent_id'])) $t_menu_text['parent_id'] = 1;
		if ($table->find($t_menu_text['parent_id'])->count() === 0)
		{
			$table->insert(array(
				'id' => $t_menu_text['parent_id'],
				'name' => 'default'
			));
		}

		$table = new menu_models_db_MenuText();
		if (isset($t_menu_text['id']) AND $table->find($t_menu_text['id'], $t_menu_text['locale'])->count() === 1)
				$table->update($t_menu_text, $table->getAdapter()->quoteInto('id = ?', $t_menu_text['id'])
					.' AND '
					.$table->getAdapter()->quoteInto('locale = ?', $t_menu_text['locale']));
		else {
			$table->insert($t_menu_text);
			$t_menu_text['id'] = $table->lastInsertId();
		}

		return $t_menu_text['id'];
	}

	/**
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$table = new menu_models_db_MenuText();

		$table->getAdapter()->beginTransaction();
		try
			{
				$r = $table->delete(
					$table->getAdapter()->quoteInto('id = ?', $id)
				);
				$table->getAdapter()->commit();
			}
		catch (Exception $e)
			{
				$table->getAdapter()->rollBack();
				throw $e;
			}
		return (boolean) $r;
	}
}