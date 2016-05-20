<?php

class estate_models_Specs
{
	protected $_prepend_select_dashes = true;

	protected $_fields = array(
		'type'=>array(
				'vendita'=>'vendita',
				'nuda proprietà'=>'nuda proprietà',
				'affitto residenti'=>'affitto residenti',
				'affitto non residenti'=>'affitto non residenti',
				'locali commerciali/uso ufficio'=>'locali commerciali/uso ufficio',
			),
		'province'=>array(
				'AG' => 'Agrigento',
				'AL' => 'Alessandria',
				'AN' => 'Ancona',
				'AO' => 'Aosta',
				'AR' => 'Arezzo',
				'AP' => 'Ascoli Piceno',
				'AT' => 'Asti',
				'AV' => 'Avellino',
				'BA' => 'Bari',
				'BT' => 'Barletta Andria Trani',
				'BL' => 'Belluno',
				'BN' => 'Benevento',
				'BG' => 'Bergamo',
				'BI' => 'Biella',
				'BO' => 'Bologna',
				'BZ' => 'Bolzano',
				'BS' => 'Brescia',
				'BR' => 'Brindisi',
				'CA' => 'Cagliari',
				'CL' => 'Caltanissetta',
				'CB' => 'Campobasso',
				'CI' => 'Carbonia Iglesias',
				'CE' => 'Caserta',
				'CT' => 'Catania',
				'CZ' => 'Catanzaro',
				'CH' => 'Chieti',
				'CO' => 'Como',
				'CS' => 'Cosenza',
				'CR' => 'Cremona',
				'KR' => 'Crotone',
				'CN' => 'Cuneo',
				'EN' => 'Enna',
				'FM' => 'Fermo',
				'FE' => 'Ferrara',
				'FI' => 'Firenze',
				'FG' => 'Foggia',
				'FC' => 'Forlì Cesena',
				'FR' => 'Frosinone',
				'GE' => 'Genova',
				'GO' => 'Gorizia',
				'GR' => 'Grosseto',
				'IM' => 'Imperia',
				'IS' => 'Isernia',
				'SP' => 'La Spezia',
				'AQ' => 'L\'Aquila',
				'LT' => 'Latina',
				'LE' => 'Lecce',
				'LC' => 'Lecco',
				'LI' => 'Livorno',
				'LO' => 'Lodi',
				'LU' => 'Lucca',
				'MC' => 'Macerata',
				'MN' => 'Mantova',
				'MS' => 'Massa Carrara',
				'MT' => 'Matera',
				'VS' => 'Medio Campidano',
				'ME' => 'Messina',
				'MI' => 'Milano',
				'MO' => 'Modena',
				'MB' => 'Monza e della Brianza',
				'NA' => 'Napoli',
				'NO' => 'Novara',
				'NU' => 'Nuoro',
				'OG' => 'Ogliastra',
				'OT' => 'Olbia Tempio',
				'OR' => 'Oristano',
				'PD' => 'Padova',
				'PA' => 'Palermo',
				'PR' => 'Parma',
				'PV' => 'Pavia',
				'PG' => 'Perugia',
				'PU' => 'Pesaro e Urbino',
				'PE' => 'Pescara',
				'PC' => 'Piacenza',
				'PI' => 'Pisa',
				'PT' => 'Pistoia',
				'PN' => 'Pordenone',
				'PZ' => 'Potenza',
				'PO' => 'Prato',
				'RG' => 'Ragusa',
				'RA' => 'Ravenna',
				'RC' => 'Reggio Calabria',
				'RE' => 'Reggio Emilia',
				'RI' => 'Rieti',
				'RN' => 'Rimini',
				'RM' => 'Roma',
				'RO' => 'Rovigo',
				'SA' => 'Salerno',
				'SS' => 'Sassari',
				'SV' => 'Savona',
				'SI' => 'Siena',
				'SR' => 'Siracusa',
				'SO' => 'Sondrio',
				'TA' => 'Taranto',
				'TE' => 'Teramo',
				'TR' => 'Terni',
				'TO' => 'Torino',
				'TP' => 'Trapani',
				'TN' => 'Trento',
				'TV' => 'Treviso',
				'TS' => 'Trieste',
				'UD' => 'Udine',
				'VA' => 'Varese',
				'VE' => 'Venezia',
				'VB' => 'Verbano Cusio Ossola',
				'VC' => 'Vercelli',
				'VR' => 'Verona',
				'VV' => 'Vibo Valentia',
				'VI' => 'Vicenza',
				'VT' => 'Viterbo',
			),
		'category'=>array(
				'residenziale'=>'residenziale',
				'commerciale'=>'commerciale',
				'vacanze'=>'vacanze',
			),
		'kind'=>array(
				'appartamento'=>'appartamento',
				'loft'=>'loft',
				'villa'=>'villa',
				'villa bifamiliare'=>'villa bifamiliare',
				'casale'=>'casale',
				'ufficio'=>'ufficio',
				'negozio'=>'negozio',
				'capannone'=>'capannone',
				'box auto'=>'box auto',
				'posto auto'=>'posto auto',
			),
		'state'=>array(
				'primo ingresso'=>'primo ingresso',
				'come nuovo'=>'come nuovo',
				'in buone condizioni'=>'in buone condizioni',
				'da ristrutturare'=>'da ristrutturare',
			)
	);

	public function getPrependDashes()
	{
		return $this->_prepend_select_dashes;
	}

	public function setPrependDashes($foo)
	{
		$this->_prepend_select_dashes = (bool) $foo;
		return $this;
	}

	public function getField($name)
	{
		if (isset($this->_fields[$name]))
			$return = $this->getPrependDashes() ?
				array_merge(array('--'=>'--'), $this->_fields[$name]) :
				$this->_fields[$name];
		else $return = array('--'=>'Filed not compiled!');
		return $return;
	}
}