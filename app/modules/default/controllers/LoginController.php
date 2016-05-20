<?php

class LoginController extends Zend_Controller_Action
{
	public function indexAction()
		{
			$form = new default_forms_Login();

			if ($this->getRequest()->isPost() AND $form->isValid($this->_getAllParams()))
				{
					sleep(3);
					$this->_warnLogin('Someone tried to logged in!');
					/*
					if (false == $this->processLogin($form->getValues()))
						$form->setDescription('Login Error');
					else $this->_warnLogin('Someone logged in!');
					*/
					$form->setDescription('Login Disabled!');
				}
			elseif ($this->getRequest()->isPost())
				{
					sleep(3);
					$this->_warnLogin('Someone is tring to login with wrong credential!');
				}

			$this->view->loginForm = $form;
		}

	private function processLogin(array $data)
		{
			$authAdapter = new Zend_Auth_Adapter_DbTable(
				Zend_Db_Table_Abstract::getDefaultAdapter(),
				't_user',
				'username',
				'password',
				'?'
			);
			$authAdapter->setIdentity($data['username']);
			$authAdapter->setCredential(md5($data['password']));

			$auth = Zend_Auth::getInstance();
			//$auth->setStorage(new Zend_Auth_Storage_Session('Zend_Auth_Admin'));
			$result = $auth->authenticate($authAdapter);

			if ( false == $result->isValid() )
				return false;

			$storage = $auth->getStorage();
			$userDetail = $authAdapter->getResultRowObject(array(
				'id',
				'username',
				'email'
			));

			$storage->write($userDetail);
			Zend_Session::rememberMe(60*60*2);
			$this->_helper->redirector('list', 'admin', 'estate');

			return true;
		}

	public function logOffAction()
		{
			Zend_Auth::getInstance()
//				->setStorage(new Zend_Auth_Storage_Session('Zend_Auth_Admin'))
				->clearIdentity();
			/**
			 * OR Zend_Auth::getInstance()->clearIdentity();
			 * what the difference
			 */

			return $this->_helper->redirector('index', 'index', 'default');
		}

	private function _warnLogin($msg)
		{
			$mail = new Zend_Mail('utf-8');
			$this->view->addScriptPath(realpath(dirname(__FILE__).'/../views/partials/emails/'));
			$this->view->assign(array('message'=>$msg));
			$html = $this->view->render('loginwarn.mail.phtml');
			$mail->setBodyHtml($html);
			$mail->addTo(config_Registry::get('application/info/email'));
			$mail->setSubject('Admin login warn');
			$mail->setFrom(config_Registry::get('application/info/email'));
			$mail->send();
		}
}
