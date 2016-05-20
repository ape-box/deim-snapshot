<?php

class Ape_Form extends Zend_Form
{
	protected $_cmdform_decorators = array(
		array('decorator' => 'FormElements', 'options' => array()),
		array('decorator' => 'HtmlTag', 'options' => array('class'=>'form-actions')),
		array('decorator' => 'Fieldset', 'options' => array('class' => 'span10', 'style' => ''))
	);

	protected $_element_decorators;

	protected $_small_element_decorators;

	protected $_big_element_decorators;

	protected $_large_element_decorators;

	protected $_larger_element_decorators;

	protected $_largest_element_decorators;

	protected $_full_element_decorators;

	protected $_textarea_decorators;

	protected $_checkbox_decorators;

	protected $_small_checkbox_decorators;

	protected $_buttons_decorators = array(
		array('decorator' => 'ViewHelper', 'options' => array()),
		array('decorator' => 'HtmlTag', 'options' => array('tag' => 'div', 'class' => 'control-group span2')),
	);

	protected function _setDefaultElementDecorator()
	{
		$this->_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			'errors' => new Ape_Form_Decorator_Errors(array('tag' => 'span')),
			'row' => new Ape_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span3')),
		);

		$this->_small_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			'errors' => new Ape_Form_Decorator_Errors(array('tag' => 'span')),
			'row' => new Ape_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span2')),
		);

		$this->_big_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			array('decorator' => 'Errors', 'options' => array()),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span4')),
		);

		$this->_large_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			array('decorator' => 'Errors', 'options' => array()),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span5')),
		);

		$this->_larger_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			array('decorator' => 'Errors', 'options' => array()),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span6')),
		);

		$this->_largest_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			array('decorator' => 'Errors', 'options' => array()),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span7')),
		);

		$this->_full_element_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			array('decorator' => 'Errors', 'options' => array()),
			'controls' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span12')),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'row-fluid')),
		);

		$this->_textarea_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			'errors' => new Ape_Form_Decorator_Errors(array('tag' => 'span')),
			'controls' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span12')),
			'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'row-fluid')),
		);

		$this->_checkbox_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			array('decorator' => 'Description', 'options' => array('tag' => 'span')),
			'element-label' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'label', 'class' => 'checkbox')),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			'errors' => new Ape_Form_Decorator_Errors(array('tag' => 'span')),
			'row' => new Ape_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span3')),
		);

		$this->_small_checkbox_decorators = array(
			array('decorator' => 'ViewHelper', 'options' => array()),
			array('decorator' => 'Description', 'options' => array('tag' => 'span')),
			'element-label' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'label', 'class' => 'checkbox')),
			'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
			array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
			'errors' => new Ape_Form_Decorator_Errors(array('tag' => 'span')),
			'row' => new Ape_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span2')),
		);
	}

	public function __construct($options = null)
	{
		$this->_setDefaultElementDecorator();
		parent::__construct($options);
	}

	/**
	 * Load the default decorators
	 *
	 * @return Zend_Form
	 */
	public function loadDefaultDecorators()
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) {
			return $this;
		}

		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('FormElements')
			->addDecorator('Form', array('class' => 'form-vertical'))
			->addDecorator('HtmlTag', array('class' => 'row-fluid'));
		}
		return $this;
	}

	/**
	 * @param $view Zend_View_Interface|null
	 * @return string
	 */
	public function render($view = null)
	{
		if ($view === null) $view = new Zend_View();
		return parent::render($view);
	}
}






























