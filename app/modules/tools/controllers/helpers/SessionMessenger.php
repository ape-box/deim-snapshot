<?php

/**
 * @method mixed add($value)
 * @method mixed add($namespace, $value)
 * @method mixed get()
 * @method mixed get($namespace)
 * @method mixed push($value)
 * @method mixed unshift($value)
 * @method mixed php()
 * @method mixed shift()
 */
//class tools_controllers_helpers_SessionMessenger extends Zend_Controller_Action_Helper_Abstract
class Zend_Controller_Action_Helper_SessionMessenger extends Zend_Controller_Action_Helper_Abstract
{
	protected $_session;
	protected $_stack;
	protected $_heap;

	const SESSSIONMANAGER_SESSION_NAMESPACE = 'Ape_Tools_SessionMessenger_Session';

	public function __construct()
	{
		$this->_session = new Zend_Session_Namespace(self::SESSSIONMANAGER_SESSION_NAMESPACE);
		$this->_session->setExpirationHops(10);
		$this->_session->unlock();
		$this->_stack = $this->_session->stack;
		$this->_heap = $this->_session->heap;

		is_null($this->_stack) AND $this->_stack = array();
		is_null($this->_heap) AND $this->_heap = array();
	}

	public function __destruct()
	{
		$this->_sync();
	}

	private function _sync()
	{
		$this->_session->heap = $this->_heap;
		$this->_session->stack = $this->_stack;
	}

	public function __call($name, $args)
	{
		switch ($name)
		{
			case 'add':
				if (count($args) > 1)
					$this->_heap[$args[0]] = $args[1];
				else
					$this->_stack[] = $args[0];
				break;
			case 'push':
				array_push($this->_stack, $args[0]);
				break;
			case 'unshift':
				array_unshift($this->_stack, $args[0]);
				break;
			case 'get':
				if (count($args) > 0) {
					$value = $this->_heap[$args[0]];
					unset($this->_heap[$args[0]]);
					return $value;
				} else
					return array_pop($this->_stack);
				break;
			case 'pop':
				return array_pop($this->_stack);
				break;
			case 'shift':
				return array_shift($this->_stack);
				break;
		}

		$this->_sync();
		return $this;
	}
}