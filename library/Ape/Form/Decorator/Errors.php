<?php

class Ape_Form_Decorator_Errors extends Zend_Form_Decorator_Errors
{
	/**
	 * Render errors
	 *
	 * @param  string $content
	 * @return string
	 */
	public function render($content)
	{
		$element = $this->getElement();
		$view	= $element->getView();
		if (null === $view) {
			return $content;
		}

		$errors = $element->getMessages();
		if (empty($errors)) {
			return $content;
		}

		$separator = $this->getSeparator();
		$placement = $this->getPlacement();
		/* @var $err_hlpr Zend_View_Helper_FormErrors */
		$err_hlpr = $view->getHelper('formErrors');
		$err_hlpr->setElementStart('<div class="help-block"%s>');
		$err_hlpr->setElementSeparator('');
		$err_hlpr->setElementEnd('</div>');
		$errors	= $err_hlpr->formErrors($errors, $this->getOptions());

		switch ($placement) {
			case self::APPEND:
				return $content . $separator . $errors;
			case self::PREPEND:
				return $errors . $separator . $content;
		}
	}
}
