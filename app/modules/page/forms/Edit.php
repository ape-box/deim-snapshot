<?php

class page_forms_Edit extends page_forms_New
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'page_edit_form');

		$this->getSubForm('base')->addElement('text', 'link', array(
			'filters' => array('StringTrim'),
			'required' => false,
			'ignore' => true,
			'readonly' => true,
			'class' => 'span12',
			'order' => 15,
			'label' => 'Link alla pagina',
			'decorators' => $this->_large_element_decorators
		));
		$this->getSubForm('base')->getDisplayGroup('row-1')->addElement(
			$this->getSubForm('base')->getElement('link')
		);

		$this->getSubForm('base')->addElement('button', 'delete', array(
			'required' => false,
			'ignore' => true,
			'type' => 'submit',
			'class' => 'btn btn-danger',
			'label' => 'cancella la pagina',
			'onclick' => 'document.getElementById(\'page_edit_form\').action = \'/page/admin/remove/id/\'+document.getElementById(\'id\').value;',
			'decorators' => array(array('decorator' => 'ViewHelper', 'options' => array()))
		));
		$this->getSubForm('base')->getDisplayGroup('row-3')->addElement(
			$this->getSubForm('base')->getElement('delete')
		);

	}

	public function populate(array $values)
	{
		parent::populate($values);
		$this->getSubForm('base')
			->getElement('link')
			->setValue('/page/view/?id='.$this->getValue('id'));
	}
}