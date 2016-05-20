<?php

class page_models_View
{
	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function listByDate($order = 'ASC', $locale = 'it_IT')
		{
			if (false == Zend_Locale::isLocale($locale))
				throw new Zend_Locale_Exception('Invalid locale format');

			$order = strtoupper((string)$order);
			if ($order !== 'ASC' AND $order !== 'DESC' )
				throw new Zend_Locale_Exception('Invalid order format');

			$adapter = Zend_Db_Table::getDefaultAdapter();
			$select = $adapter->select()
				->from('t_page')
				->joinLeftUsing('t_page_text', 'id')
				->where('locale = ?', $locale)
				->order("date $order");

			return $adapter->fetchAll($select);
		}

	/**
	 * @param integer $id
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 * @throws Zend_Db_Table_Exception
	 */
	public function find($id, $locale = 'it_IT')
		{
			if (false == Zend_Locale::isLocale($locale))
				throw new Zend_Locale_Exception('Invalid locale format');

			$validate_digits = new Zend_Validate_Digits();
			if (false == $validate_digits->isValid($id))
				throw new Zend_Db_Table_Exception('Invalid index format');

			$adapter = Zend_Db_Table::getDefaultAdapter();
			$select = $adapter->select()
			    ->from('t_page')
				->joinLeftUsing('t_page_text', 'id')
				->where('locale = ?', $locale)
				->where('t_page.id = ?', $id);

			$data = $adapter->fetchRow($select);
			if(empty($data)) throw new Exception('Invalid ID');

			return $data;
		}
}