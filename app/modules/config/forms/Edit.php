<?php

class config_forms_Edit extends Ape_Form
{
	public function init()
		{
			$this->addSubForm(new Ape_Form_SubForm(), 'application', 10);
			$this->getSubForm('application')->setLegend('Opzioni dell\'Applicazione');

			$this->getSubForm('application')->addElement('text', 'website_title', array(
				'filters' => array('StringTrim'),
//				'validator' => array('StringTrim'),
				'required' => true,
				'label' => 'Nome del sito',
				'class' => 'span12',
				'value' => config_Registry::get('application/website/title'),
				'placeholder' => 'demo-immobiliare.com',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('application')->addElement('textarea', 'website_sidebox', array(
				'filters' => array('StringTrim'),
				'required' => false,
				'label' => 'Paragrafo visibile nel sidebox',
				'class' => 'tiny span12',
				'value' => config_Registry::get('application/website/sidebox'),
				'cols' => 20,
				'rows' => 10,
				'decorators' => $this->_textarea_decorators
			));

			$this->getSubForm('application')->addElement('text', 'info_email', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'Email dell\'amministratore',
				'class' => 'span12',
				'value' => config_Registry::get('application/info/email'),
				'placeholder' => 'info@demo-immobiliare.com',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('application')->addElement('text', 'info_reference', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'Nome della ditta',
				'class' => 'span12',
				'value' => config_Registry::get('application/info/reference'),
				'placeholder' => 'Demo Immobiliare srl',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('application')->addElement('text', 'info_piva', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'Partita IVA',
				'class' => 'span12',
				'value' => config_Registry::get('application/info/piva'),
				'placeholder' => '012304560789',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('application')->addElement('text', 'info_legals', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'Informazioni legali obbligatorie',
				'class' => 'span12',
				'value' => config_Registry::get('application/info/legals'),
				'placeholder' => 'REA IT 01234 - Capitale Sociale &euro; 100.000 etc',
				'decorators' => $this->_large_element_decorators
			));

			$this->getSubForm('application')->addElement('text', 'info_addresses', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'Informazioni di contatto',
				'class' => 'span12',
				'value' => config_Registry::get('application/info/addresses'),
				'placeholder' => 'via della Mia Fantasia 1, 00100 (MI) - tel. 02 123456 - mail. contatti@demo-immobiliare.com',
				'decorators' => $this->_largest_element_decorators
			));

			$this->getSubForm('application')->addElement('button', 'submit', array(
				'required' => false,
				'ignore' => true,
				'type' => 'submit',
				'class' => 'btn btn-primary',
				'label' => 'salva le impostazioni',
				'decorators' => array(array('decorator' => 'ViewHelper', 'options' => array()))
			));

			$this->getSubForm('application')->addDisplayGroup(array('website_title', 'info_email', 'info_reference', 'info_piva'), 'row-1', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				)
			));

			$this->getSubForm('application')->addDisplayGroup(array('info_legals', 'info_addresses'), 'row-2', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				)
			));

			$this->getSubForm('application')->addDisplayGroup(array('website_sidebox'), 'row-3', array(
				'decorators' => array(
					'FormElements',
				)
			));

			$this->addSubForm(new Ape_Form_SubForm(), 'estate', 20);
			$this->getSubForm('estate')->setLegend('Opzioni degli Annunci');

			$this->getSubForm('estate')->addElement('text', 'ad_pagination__items', array(
				'filters' => array('StringTrim', 'Digits'),
				'required' => true,
				'label' => 'Annunci per pagina',
				'class' => 'span12',
				'value' => config_Registry::get('estate/ad/pagination_items', 12),
				'decorators' => $this->_small_element_decorators
			));

			$this->getSubForm('estate')->addElement('text', 'widgets_evidenced__limit', array(
				'filters' => array('StringTrim', 'Digits'),
				'required' => true,
				'label' => 'Annunci in Evidenza',
				'class' => 'span12',
				'value' => config_Registry::get('estate/widgets/evidenced_limit', 6),
				'decorators' => $this->_small_element_decorators
			));

			$this->getSubForm('estate')->addDisplayGroup(array('ad_pagination__items', 'widgets_evidenced__limit'), 'row-20', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				)
			));

			$this->getSubForm('application')->addDisplayGroup(array('submit'), 'row-99', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'form-actions'))
				),
				'order' => 1000
			));

		}
}