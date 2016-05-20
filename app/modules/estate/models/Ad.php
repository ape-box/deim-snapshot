<?php

class estate_models_Ad
{
	private $_search_min_length = 3;

	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function query($params, $locale = 'it_IT')
		{
			if (false == Zend_Locale::isLocale($locale))
				throw new Zend_Locale_Exception('Invalid locale format');

			$adapter = Zend_Db_Table::getDefaultAdapter();
			$select = $adapter->select()
				->from('t_estate')
				->joinLeftUsing('t_estate_text', 'id')
				->joinLeftUsing('t_estate_spec', 'id')
				->joinLeft('t_media_files', 't_media_files.parent_id=t_estate.id', array('primary_id'=>'id', 'primary_name'=>'name', 'primary_md5'=>'checksum'))
				->where('locale = ?', $locale)
				->where('pub = ?', 'yes')
				->where('t_media_files.parent_table = ? OR t_media_files.parent_table IS NULL', 't_estate')
				->where('t_media_files.relation = ? OR t_media_files.relation IS NULL', 'primary')
				->order('date ASC');

			foreach ($params as $name => $value)
			{
				if (empty($value) OR $value === '--') continue;
				switch ($name)
				{
					case 'type':
					case 'category':
					case 'kind':
					case 'garage':
						$select->where("$name = ?", $value);
						break;
					case 'district':
						$select->where("UPPER($name) LIKE UPPER(?)", "%$value%");
						break;
					// da mettere in un select range
					case 'metrics':
					case 'price':
						if (ctype_digit($value))
							$select->where("$name = ?", $value);
						else {
							$minmax = explode('-', $value);
							if (count($minmax) == 0 OR count($minmax) > 2) continue;
							$min = (int) $minmax[0];
							$max = isset($minmax[1]) ? (int) $minmax[1] : 0;
							switch (true) {
								case $min == 0 AND $max > $min:
									$select->where("$name <= ?", $max);
									break;
								case $min > 0 AND $max > $min:
									$select->where("$name >= ?", $min);
									$select->where("$name <= ?", $max);
									break;
								case $min > 0 AND $max == 0:
									$select->where("$name >= ?", $min);
									break;
							}
						}
						break;
				}
			}

			return Zend_Paginator::factory($select);
		}

	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function search($_search, $locale = 'it_IT')
		{
			if (false == Zend_Locale::isLocale($locale))
				throw new Zend_Locale_Exception('Invalid locale format');

			$_search = preg_replace('[^a-zA-Z0-9\-_ ]', '', $_search);
			$_search = str_replace('  ', ' ', $_search);
			$_search = trim($_search);
			if (strlen($_search) < $this->getSearchMinimumLength()) return Zend_Paginator::factory(array());
			$_search = explode(' ', $_search);

			$_map = 'UPPER(t_estate.ad_code) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_text.title) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_text.text) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_spec.type) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_spec.district) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_spec.address) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_spec.category) LIKE UPPER(?) OR ' .
					'UPPER(t_estate_spec.kind) LIKE UPPER(?)';

			$adapter = Zend_Db_Table::getDefaultAdapter();
			foreach ($_search as $_id => $word)
				if (strlen($word) < $this->getSearchMinimumLength())
					unset($_search[$_id]);
				else
					$_search[$_id] = $adapter->quoteInto($_map, "%$word%");
			$_search = implode(' OR ', $_search);

			$select = $adapter->select()
				->from('t_estate')
				->joinLeftUsing('t_estate_text', 'id')
				->joinLeftUsing('t_estate_spec', 'id')
				->joinLeft('t_media_files', 't_media_files.parent_id=t_estate.id', array('primary_id'=>'id', 'primary_name'=>'name', 'primary_md5'=>'checksum'))
				->where('locale = ?', $locale)
				->where('pub = ?', 'yes')
				->where('t_media_files.parent_table = ? OR t_media_files.parent_table IS NULL', 't_estate')
				->where('t_media_files.relation = ? OR t_media_files.relation IS NULL', 'primary')
				->where($_search)
				->order('date ASC');

			return Zend_Paginator::factory($select);
		}

	/**
	 * TODO: Implementare come parametro modificabile nella configurazione
	 * @todo Implementare come parametro modificabile nella configurazione
	 */
	public function getSearchMinimumLength()
		{
			return $this->_search_min_length;
		}

	public function setSearchMinimumLength(int $length)
		{
			$length = intval($length, 10);
			$this->_search_min_length = $length;
		}

	/**
	 * @param string $order [ASC|DESC]
	 * @param string|Zend_Locale $locale
	 * @return array
	 * @throws Zend_Locale_Exception
	 */
	public function listEvidence($limit = 6, $locale = 'it_IT')
		{
			if (false == Zend_Locale::isLocale($locale))
				throw new Zend_Locale_Exception('Invalid locale format');

			$limit = (int) $limit;
			$adapter = Zend_Db_Table::getDefaultAdapter();
			$select = $adapter->select()
				->from('t_estate')
				->joinLeftUsing('t_estate_text', 'id')
				->joinLeftUsing('t_estate_spec', 'id')
				->joinLeft('t_media_files', 't_media_files.parent_id=t_estate.id', array('primary_id'=>'id', 'primary_name'=>'name', 'primary_md5'=>'checksum'))
				->where('locale = ?', $locale)
				->where('pub = ?', 'yes')
				->where('evidence = ?', 'yes')
				->where('t_media_files.parent_table = ? OR t_media_files.parent_table IS NULL', 't_estate')
				->where('t_media_files.relation = ? OR t_media_files.relation IS NULL', 'primary')
				->order("date ASC");

			return Zend_Paginator::factory($select);
		}

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
				->from('t_estate')
				->joinLeftUsing('t_estate_text', 'id')
				->joinLeftUsing('t_estate_spec', 'id')
				->joinLeft('t_media_files', 't_media_files.parent_id=t_estate.id', array('primary_id'=>'id', 'primary_name'=>'name', 'primary_md5'=>'checksum'))
				->where('locale = ?', $locale)
				->where('pub = ?', 'yes')
				->where('t_media_files.parent_table = ? OR t_media_files.parent_table IS NULL', 't_estate')
				->where('t_media_files.relation = ? OR t_media_files.relation IS NULL', 'primary')
				->order("date $order");

			return Zend_Paginator::factory($select);
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
			    ->from('t_estate')
				->joinLeftUsing('t_estate_text', 'id')
				->joinLeftUsing('t_estate_spec', 'id')
				->where('locale = ?', $locale)
				->where('pub = ?', 'yes')
				->where('t_estate.id = ?', $id);

			$data = $adapter->fetchRow($select);
			if(empty($data)) throw new Exception('Invalid ID');


			/**
			* Find ad table data of kind: MEDIA
			* ----------------------------------------------------------------------------
			*/
			$table_m = new estate_models_db_EstateMedia();
			$primary_image = $table_m->findPrimaryImage($id, 't_estate');

			if (!empty($primary_image))
			$data = array_merge($data, $primary_image);


			return $data;
		}
}