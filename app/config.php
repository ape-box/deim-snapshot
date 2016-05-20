<?php
/**
 * dbname e dbpass sono cryptati con "AES_ENCRYPT" con chiave "47d1a7b66de2853ea0c6a201b560425c" (md5 di "vivalafigha")
 */
return array(
	'database' => array(
		'adapter' => 'pdo_mysql',
		'params' => array(
			'host' => 'localhost',
			'username' => 'username',
			'password' => 'password',
			'dbname' => 'dbname'
		)
	),
	'file' => array(
		'path' => array(
			'application' => APPLICATION_PATH,
			'views' => APPLICATION_PATH.'/../data/views',
			'storage' => APPLICATION_PATH.'/../data/storage',
			'cache' => APPLICATION_PATH.'/../data/cache',
			'translation' => APPLICATION_PATH.'/../data/translations'
		)
	),
	'languages' => array(
		'default' => 'it_IT',
		'avaible' => array(
			'it_IT' => '1',
/*
			'it' => '0.7',
			'en' => '0.5',
			'en_EN' => '0.5',
			'en_GB' => '0.3',
			'en_US' => '0.3'
*/
		)
	),
	'plugins' => array(
		'instances' => array(
			'className' => 'plugins_Instances',
			'stackIndex' => '101'
		),
		'acl' => array(
			'className' => 'plugins_Acl',
			'stackIndex' => '102'
		),
		'menu' => array(
			'className' => 'plugins_Menu',
			'stackIndex' => '1001'
		)
	),
	'modules' => array(
		'default' => array(
			'classes' => array(
				'menu' => 'true',
				'acl' => 'false'
			),
			'viewHelpers' => 'false',
			'actionHelpers' => 'false',
			'translations' => 'false',
		),
		'estate' => array(
			'classes' => array(
				'menu' => 'true',
				'acl' => 'true',
			),
			'viewHelpers' => 'true',
			'actionHelpers' => 'false',
			'translations' => 'true',
		),
		'page' => array(
			'classes' => array(
				'menu' => 'true',
				'acl' => 'true',
			),
			'viewHelpers' => 'false',
			'actionHelpers' => 'false',
			'translations' => 'false',
		),
		'menu' => array(
			'classes' => array(
				'menu' => 'true',
				'acl' => 'true',
			),
			'viewHelpers' => 'false',
			'actionHelpers' => 'false',
			'translations' => 'false',
		),
		'file' => array(
			'classes' => array(
				'menu' => 'false',
				'acl' => 'true',
			),
			'viewHelpers' => 'false',
			'actionHelpers' => 'false',
			'translations' => 'false',
		),
		'tools' => array(
			'classes' => array(
				'menu' => 't',
				'acl' => 'false',
			),
			'viewHelpers' => 'true',
			'actionHelpers' => 'true',
			'translations' => 'false',
		),
		'config' => array(
			'classes' => array(
				'menu' => 'true',
				'acl' => 'true',
			),
			'viewHelpers' => 'false',
			'actionHelpers' => 'false',
			'translations' => 'false',
		),
		'theme' => array(
			'classes' => array(
				'menu' => 'true',
				'acl' => 'true',
			),
			'viewHelpers' => 'false',
			'actionHelpers' => 'false',
			'translations' => 'true',
		),
	)
);