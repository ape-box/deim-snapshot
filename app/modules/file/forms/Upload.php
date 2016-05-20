<?php

class file_forms_Upload extends Zend_Form
{
	const FILE_FORM_UPLOAD_FILE_FIELDNAME = 'file';
	public function init()
	{
		$this->setMethod('POST');
		$this->setEnctype('multipart/form-data');

		$this->addElement('file', self::FILE_FORM_UPLOAD_FILE_FIELDNAME, array(
			'label'			=> 'File',
			'decorators' => array(
				'File',
				array(array('element' => 'HtmlTag'), array('tag' => 'div')),
				'Label',
				array(array('row' => 'HtmlTag'), array('tag' => 'div')),
				array('Description', array('tag' => 'div', 'placement' => 'prepend', 'class' => 'descriptionDiv', 'escape' => false))
			),
			'MaxFileSize' => 1024*1024*24
		));

		$this->addElement('hidden', 'subfolder', array(
			'required' 	=> false,
			'ignore'   	=> true,
			'decorators' => array(
				'ViewHelper'
			)
		));

		$this->addElement('submit', 'upload', array(
			'required' 	=> false,
			'ignore'   	=> true,
			'label'		=> 'upload',
			'decorators' => array(
				'ViewHelper'
			)
		));
	}
}
