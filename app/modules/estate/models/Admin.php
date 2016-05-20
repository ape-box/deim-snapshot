<?php

class estate_models_Admin
{
	/**
	 * @var Zend_Filter_Digits
	 */
	private $_filter_digits;

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
				->select()->from('t_estate', array('id', 'date', 'pub', 'evidence', 'ad_code'))
				->joinLeftUsing('t_estate_text', 'id', array('locale', 'title'));

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
							case 'date':
							case 'type':
							case 'pub':
							case 'evidence':
								$name = Zend_Db_Table::getDefaultAdapter()->quote($name);
								$select->where("$name = ?", $value);
								break;
							case 'title':
							case 'ad_code':
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
			$return_data = array();
			if (false == Zend_Locale::isLocale($locale))
				throw new Zend_Locale_Exception('Invalid locale format');

			$validate_digits = new Zend_Validate_Digits();
			if (false == $validate_digits->isValid($id))
				throw new Zend_Db_Table_Exception('Invalid index format');


			/**
			 * Find ad table data of kind: PRIMARY
			 * ----------------------------------------------------------------------------
			 */
			/* @var $table Zend_Db_Table */
			$table = new estate_models_db_Estate();
			/* @var $data Zend_Db_Table_Rowset */
			$data = $table->find($id);
			if ($data->count() !== 1)
				return array();
			else
				$return_data = array_merge($return_data, $data->current()->toArray());


			/**
			 * Find ad table data of kind: LOCALIZED
			 * ----------------------------------------------------------------------------
			 */
			$table_t = new estate_models_db_EstateText();
			$localized_data = $table_t->find($id, $locale);

			if ($localized_data->count() !== 1)
				return array();
			$return_data = array_merge($return_data, $localized_data->current()->toArray());


			/**
			 * Find ad table data of kind: SPECIFICATION
			 * ----------------------------------------------------------------------------
			 */
			$table_s = new estate_models_db_EstateSpec();
			$spec_data = $table_s->find($id);

			if ($spec_data->count() === 1)
				$return_data = array_merge($return_data, $spec_data->current()->toArray());


			/**
			 * Find ad table data of kind: MEDIA
			 * ----------------------------------------------------------------------------
			 */
			$table_m = new estate_models_db_EstateMedia();
			$primary_image = $table_m->findPrimaryImage($id, $table->getTableName());

			if (!empty($primary_image))
				$return_data = array_merge($return_data, $primary_image);


			return $return_data;
		}

	/**
	 * @param array $params dati da salvare
	 * @return int $id
	 */
	public function save($params)
		{
			$t_estate = array();
			$t_estate_text = array();
			$t_estate_spec = array();
			$t_estate_media = array();
			if (!isset($params['locale']))
				$params['locale'] = 'it_IT'; // MERDA !!!

			if (isset($params['base'])) {
				$params = array_merge($params, $params['base']);
				unset($params['base']);
			}
			if (isset($params['spec'])) {
				$params = array_merge($params, $params['spec']);
				unset($params['spec']);
			}
			if (isset($params['pics'])) {
				$params = array_merge($params, $params['pics']);
				unset($params['pics']);
			}

			foreach($params AS $name => $value)
				{
					switch($name)
						{
							case 'date':
								$t_estate[$name] = preg_replace('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4}).*$/', '$3-$2-$1 00:00:00', $value);
								break;
							case 'id':
							case 'pub':
							case 'evidence':
							case 'ad_code':
								$t_estate[$name] = $value;
								break;
							case 'title':
							case 'text':
								if (empty($value))
									throw new Exception('Empty param '.$name);
							case 'locale':
							case 'image':
							case 'gallery':
								$t_estate_text[$name] = $value;
								break;
							case 'type':
							case 'province':
							case 'district':
							case 'address':
							case 'category':
							case 'kind':
							case 'metrics':
							case 'rooms':
							case 'bathrooms':
							case 'balcony':
							case 'heating':
							case 'elevator':
							case 'parking':
							case 'garage':
							case 'floor':
							case 'floors':
							case 'state':
							case 'build_year':
							case 'deed':
							case 'monthly_charges':
							case 'price':
								$t_estate_spec[$name] = $value;
								break;
							case 'primary_image':
								if (!empty($value))
									$t_estate_media[$name] = $value;
								break;
						}
				}
			$table = new estate_models_db_Estate();

			if ( !empty($t_estate['id']))
				$table->update($t_estate, $table->getAdapter()->quoteInto('id = ?', $t_estate['id']));
			else
				$t_estate['id'] = $table->insert($t_estate);

			$table = new estate_models_db_EstateText();
			$t_estate_text['id'] = $t_estate['id'];

			switch ($table->find($t_estate['id'], $t_estate_text['locale'])->count())
			{
				case 1:
					$table->update($t_estate_text, $table->getAdapter()->quoteInto('id = ?', $t_estate['id'])
						.' AND '
						.$table->getAdapter()->quoteInto('locale = ?', $t_estate_text['locale']));
					break;
				case 0:
					$table->insert($t_estate_text);
					break;
				default:
					throw new Exception('Impossibile non ci possono essere indici multipli !!!!');
					break;
			}

			$table = new estate_models_db_EstateSpec();
			$t_estate_spec['id'] = $t_estate['id'];

			switch ($table->find($t_estate['id'])->count())
			{
				case 1:
					$table->update($t_estate_spec, $table->getAdapter()->quoteInto('id = ?', $t_estate['id']));
					break;
				case 0:
					$table->insert($t_estate_spec);
					break;
				default:
					throw new Exception('Impossibile non ci possono essere indici multipli !!!!');
					break;
			}

			if (!empty($t_estate_media))
				{
					/**
					 * SAVE PRIMARY IMAGE
					 */
					$primary_id = $this->_savePrimary($t_estate['id']);
				}

			return $t_estate['id'];
		}

	/**
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
		{
			$table = new estate_models_db_EstateText();

			$table->getAdapter()->beginTransaction();
			try
				{
					$table->delete(
						$table->getAdapter()->quoteInto('id = ?', $id)
					);
					$table = new estate_models_db_Estate();
					$r = $table->delete(
						$table->getAdapter()->quoteInto('id = ?', $id)
					);
					$table_m = new estate_models_db_EstateMedia();
					$table_m->delete(
						$table->getAdapter()->quoteInto('parent_id = ?', $id)
						. ' AND ' .
						$table->getAdapter()->quoteInto('parent_table = ?', $table->getTableName())
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

	/**
	 * @param string $file_name
	 * @param string $type
	 * @param mixed $params
	 * @throws file_models_File_Exception
	 */
	protected function _prepareFileData($file_name = 'primary_image', $type = 'primary', $config = null)
		{
			$file_name = $this->_getString($file_name);
			$type = $this->_getString($type);

			$tmpFile = $_FILES[$file_name]['tmp_name'];
			/**
			 * Check for upload errors
			 */
			if (!is_file($tmpFile)) {
				$alternate_name = dirname($tmpFile).DIRECTORY_SEPARATOR.$_FILES[$file_name]['name'];
				if (is_file($alternate_name)) $tmpFile = $alternate_name;
				else switch ($_FILES[$file_name]['error']) {
					case UPLOAD_ERR_OK: 		throw new file_models_File_Exception('Non ci sono errori, ma il file non esiste');break;
					case UPLOAD_ERR_INI_SIZE:	throw new file_models_File_Exception('Il file è troppo grande rispetto alle impostazioni del server');break;
					case UPLOAD_ERR_FORM_SIZE:	throw new file_models_File_Exception('Il file è troppo grande rispetto alle impostazioni del form');break;
					case UPLOAD_ERR_PARTIAL:	throw new file_models_File_Exception('File incompleto');break;
					case UPLOAD_ERR_NO_FILE:	throw new file_models_File_Exception('Non è stato caricato alcun file');break;
					case UPLOAD_ERR_NO_TMP_DIR:	throw new file_models_File_Exception('Cartella dei temporanei mancante');break;
					case UPLOAD_ERR_CANT_WRITE:	throw new file_models_File_Exception('Impossibile scrivere il file');break;
					case UPLOAD_ERR_EXTENSION:	throw new file_models_File_Exception('Problemi con l\'estensione del file');break;
					default:					throw new file_models_File_Exception('File caricato inesistente!');break;
				}
			}

			/**
			 * Whitelist params
			 *
			 * @var array
			 */
			$params = array();
			$params['relation'] = $type;
			$params['name'] = $_FILES[$file_name]['name'];
			$params['name'] = strtolower($params['name']);
			$finfo = finfo_open(FILEINFO_MIME);
			$params['mime'] =  explode(';', finfo_file($finfo, $tmpFile));
			$params['mime'] = $params['mime'][0];
			finfo_close($finfo);
			$params['size'] = $_FILES[$file_name]['size'];
			$params['data'] = file_get_contents($tmpFile);
			$params['checksum'] = md5_file($tmpFile);

			switch (true)
			{
				case is_object($config):
					try {
						$config->is_image = exif_imagetype($tmpFile);
					} catch (Exception $e) {}
					break;
				case is_null($config):
				default:
					$config = array();
				case is_array($config):
					$config['is_image'] = exif_imagetype($tmpFile);
					break;
					break;
			}

			$params['serialized_params'] = json_encode($config);

			return  $params;
		}// func


		protected function _savePrimary($parent_id, $config = null)
		{
			$table = new estate_models_db_Estate();
			$table_m = new estate_models_db_EstateMedia();

			$parent_id = $this->_getDigit($parent_id);
			$params = $this->_prepareFileData('primary_image', 'primary', $config);
			$params['parent_id'] = $parent_id;
			$params['parent_table'] = $table->getTableName();

			$table_m->getAdapter()->beginTransaction();
			try
				{
					$query = $table_m->getAdapter()->select()->from($table_m->getTableName(), 'id')
						->where('parent_id = ?', $parent_id)
						->where('parent_table = ?', $table->getTableName())
						->where('relation = ?', 'primary');

					$id = $table_m->getAdapter()->fetchOne($query);
					if ($id) {
						$params['id'] = $id;
						$table_m->update($params, $table_m->getAdapter()->quoteInto('id = ?', $id));
					}
					else {
						$id = $table_m->insert($params);
					}
					$table_m->getAdapter()->commit();
					return $id;
				}
			catch (Exception $e)
				{
					$table_m->getAdapter()->rollBack();
					throw $e;
				}
		}// func

		private function _getDigit($value)
		{
			$this->_filter_digits = $this->_filter_digits ? $this->_filter_digits : new Zend_Filter_Digits();
			return $this->_filter_digits->filter($value);
		}

		private function _getString($value)
		{
			if (is_string($value)) return $value;
			else throw new Exception('$value is not a string!');
		}
}// class


