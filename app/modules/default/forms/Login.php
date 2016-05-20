<?php

class default_forms_Login extends Zend_Form
{
	public function init()
		{
			/**
			 * I validator mi mandano in loop il php
			 */
			$this->setMethod('post');
			$this->addElement('text', 'username', array(
//				'validators' => array(
//					array('StringLength', true, array(4, 64))
//				),
				'required'   => true,
				'label'	  => 'Username:'
			));

			$this->addElement('password', 'password', array(
//				'validators' => array(
//					array('StringLength', true, array(4, 64))
//				),
				'required'   => true,
				'label'	  => 'Password:'
			));

			$this->addElement('submit', 'submit', array(
				'required' => false,
				'ignore'   => true,
				'label'	=> 'Entra'
			));
		}
}