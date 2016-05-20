<?php

class page_models_Admin
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
			->select()->from('t_page')->joinLeftUsing('t_page_text', 'id');

		foreach($params AS $name => $value)
		{
			if($value == '') continue;
			switch($name)
				{
					case 'search':
						$k = explode(' ', $value);
						foreach ($k as $k)
							$select->where("UPPER(title) LIKE UPPER(?) OR UPPER(text) LIKE UPPER(?)", "%$k%");
						break;
					case 'title':
						$name = Zend_Db_Table::getDefaultAdapter()->quote($name);
						$select->where("UPPER(title) LIKE UPPER(?)", "%$value%");
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
		$table = new page_models_db_Page();
		/* @var $data Zend_Db_Table_Rowset */
		$data = $table->find($id);
		if ($data->count() !== 1)
			return array();
		else
			$data = $data->current();

		$table = new page_models_db_PageText();
		$localized_data = $table->find($data->id, $locale);

		if ($localized_data->count() !== 1)
			return array();
		$localized_data = $localized_data->current();

		return array_merge($data->toArray(), $localized_data->toArray());
	}

	/**
	 * @param array $params dati da salvare
	 * @return int $id
	 */
	public function save($params)
	{
		$t_page = array();
		$t_page_text = array();
		if (!isset($params['locale']))
			$params['locale'] = 'it_IT'; // MERDA !!!
		foreach($params AS $name => $value)
			{
				if (empty($value)) continue;
				switch($name)
					{
						case 'id':
							$t_page[$name] = $value;
							break;
						case 'title':
						case 'text':
						case 'keywords':
						case 'description':
							if (empty($value))
								throw new Exception('Empty param '.$name);
						case 'locale':
							$t_page_text[$name] = $value;
							break;
					}
			}
		$table = new page_models_db_Page();
		if ( isset($t_page['id']))
			$table->update($t_page, $table->getAdapter()->quoteInto('id = ?', $t_page['id']));
		else
			$t_page['id'] = $table->insert($t_page);

		$table = new page_models_db_PageText();
		$t_page_text['id'] = $t_page['id'];

		switch ($table->find($t_page['id'], $t_page_text['locale'])->count())
		{
			case 1:
				$table->update($t_page_text, $table->getAdapter()->quoteInto('id = ?', $t_page['id'])
					.' AND '
					.$table->getAdapter()->quoteInto('locale = ?', $t_page_text['locale']));
				break;
			case 0:
				$table->insert($t_page_text);
				break;
			default:
				throw new Exception('Impossibile non ci possono essere indici multipli !!!!');
				break;
		}

		return $t_page['id'];
	}

	/**
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
		{
			$table = new page_models_db_PageText();

			$table->getAdapter()->beginTransaction();
			try
				{
					$table->delete(
						$table->getAdapter()->quoteInto('id = ?', $id)
					);
					$table = new page_models_db_Page();
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