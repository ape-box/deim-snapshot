<?php

class theme_forms_Layout extends Ape_Form
{
	public function init()
		{
			$this->setAttrib('id', 'layout_modify');
			$this->addSubForm(new Ape_Form_SubForm(), 'base', 0);
			$this->getSubForm('base')->setLegend('Modifica Layout');

			$this->getSubForm('base')->addElement('custom', 'text', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'Contenuto',
				'class' => 'span12',
				'tag' => 'div',
				'order' => 20,
				'decorators' => $this->_textarea_decorators
			));

			$this->getSubForm('base')->addElement('button', 'submit', array(
				'required' => false,
				'ignore' => true,
				'type' => 'submit',
				'class' => 'btn btn-primary',
				'label' => 'salva la pagina',
				'decorators' => array(array('decorator' => 'ViewHelper', 'options' => array()))
			));

			$this->getSubForm('base')->addDisplayGroup(array('text'), 'row-1', array(
				'decorators' => array(
					'FormElements',
				),
				'order' => 10
			));

			$this->getSubForm('base')->addDisplayGroup(array('submit'), 'row-2', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'form-actions'))
				),
				'order' => 20
			));
		}
}