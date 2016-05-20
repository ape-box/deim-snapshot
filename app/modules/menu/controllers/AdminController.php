<?php

class menu_AdminController extends Zend_Controller_Action
{
	public function init()
		{
			$this->_helper->getHelper('layout')->setLayout('bootstrap');
		}

	public function indexAction() {}

	public function listAction()
		{
			$model = new menu_models_Admin();
			$this->view->pages = $model->fetchAllPages();
			$this->view->items = $model->fetchAllMenuItems();

			if ($this->getRequest()->isPost())
			{
				foreach ($this->_getParam('menu') as $item)
					$model->save($item);
				return $this->_helper->redirector
					->gotoSimpleAndExit('list', 'admin', 'menu');
			}
		}

	public function saveAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			$model = new menu_models_Admin();
			$id = $model->save($this->_getAllParams());
			$this->_helper->json(
				$model->find($id)
			);
		}

	public function deleteAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			if ((!is_null($this->_getParam('oper')) &&  $this->_getParam('oper') === 'del') || (is_null($this->_getParam('oper'))) )
				{
					$model = new menu_models_Admin();
					foreach ( explode(',', $this->_getParam('id', '')) as $id )
						{
							$r = $model->delete($id);
							if (!$r) throw new Exception("Error deleting id {$id}");
						}
				}
		}

	public function removeAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			$id = $this->_getParam('id');
			if (!ctype_digit($id)) throw new Exception("Invalid id");
			$model = new menu_models_Admin();
			$r = $model->delete((int)$id);
			$this->_helper->json($r);
		}
}

