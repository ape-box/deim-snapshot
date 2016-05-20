<?php

class estate_forms_Edit extends estate_forms_New
{
	protected $_file_decorators;

	protected $_gallery_decorators = array(
		array('decorator' => 'ViewHelper', 'options' => array()),
		array('decorator' => 'Description', 'options' => array('tag' => 'div', 'placement' => 'append', 'id' => 'blueimp_box', 'class' => 'multiple_images', 'escape' => false)),
	);

	protected function _setDefaultElementDecorator()
	{
		parent::_setDefaultElementDecorator();

		$this->_file_decorators = array(
			array('decorator' => 'File', 'options' => array()),
			'block' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'span6')),
			array('decorator' => 'Description', 'options' => array('tag' => 'div', 'placement' => 'append', 'class' => 'span3 thumbnails', 'escape' => false)),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			array('decorator' => 'Errors', 'options' => array()),
			'controls' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span12')),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'row-fluid')),
		);
	}

	protected $_inner_button_decorators = array(
		array('decorator' => 'ViewHelper', 'options' => array()),
		array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*')),
		array('decorator' => 'Errors', 'options' => array()),
		array('decorator' => 'HtmlTag', 'options' => array('tag' => 'div', 'class' => 'inner_button')),
	);

	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'estate_edit_form');

		$this->addSubForm(new Ape_Form_SubForm(), 'pics', 90);
		$this->getSubForm('pics')->setLegend('estate_media');

		$this->getSubForm('pics')->addElement('file', 'primary_image', array(
			'filters' => array('StringTrim'),
			'required' => false,
			'class' => 'input-xlarge',
			'label' => 'estate_primary_image',
			'accept' => 'image/*',
			'MaxFileSize' => 1024*1024*2,
			'order' => 10,
			'decorators' => $this->_file_decorators
		));

		$this->getSubForm('pics')->addElement('hidden', 'gallery_images', array(
			'filters' => array('StringTrim'),
			'required' => false,
			'ignore' => true,
			'order' => 20,
			'description' => '<div id="render_gallery" />',
			'decorators' => $this->_gallery_decorators
		));

		$this->getSubForm('base')->addElement('button', 'delete', array(
			'required' => false,
			'ignore' => true,
			'type' => 'submit',
			'class' => 'btn btn-danger',
			'label' => 'estate_ad-delete',
			'onclick' => 'document.getElementById(\'estate_edit_form\').action = \'/estate/admin/remove/id/\'+document.getElementById(\'id\').value;',
			'decorators' => array(array('decorator' => 'ViewHelper', 'options' => array()))
		));
		$this->getSubForm('base')->getDisplayGroup('row-3')->addElement(
			$this->getSubForm('base')->getElement('delete')
		);
	}

	public function populate($values)
	{
		parent::populate($values);

		if (isset($values['primary_image-name'], $values['primary_image-id'], $values['primary_image-md5']))
		{
			/**
			 * Image setup
			 * --------------------------------------------------------------------------------------------------------------
			 */
			$img_width = 270;
			$img_heigh = (int) ceil($img_width * 0.75);
			$img_src = "/file/media/{$values['primary_image-id']}/$img_width/$img_heigh?sum={$values['primary_image-md5']}";
			$img_tag = "<img src=\"$img_src\" alt=\"preview\" width=\"$img_width\" height=\"$img_heigh\" />";

			/**
			 * Caption / image name setup
			 * -------------------------------------------------------------------------
			 */
			if (strlen($values['primary_image-name']) < 28)
				$caption_txt = $values['primary_image-name'];
			else {
				$caption_txt = substr($values['primary_image-name'], 0, 14) . ' ... ';
				$caption_txt.= substr($values['primary_image-name'], -8);
			}

			/* @var $translator Zend_Translate_Adapter_Csv */
			$translator = Zend_Registry::get('Zend_Translate');
			$image_name = 'Image name';
			if ($translator instanceof Zend_Translate_Adapter)
				$image_name = $translator->translate('estate_image_name');

			/**
			 * Assemble everything
			 * -------------------------------------------------------------------------
			 */
			$description =<<<HEREDOC
				<div class="row-fluid">
					<div class="thumbnail span12">
						$img_tag
						<div class="caption">
							<h5>$image_name</h5>
							<p>$caption_txt</p>
						</div>
					</div>
				</div>
HEREDOC;
			$this->getSubForm('pics')->getElement('primary_image')
				->setDescription($description);
		}
	}
}























