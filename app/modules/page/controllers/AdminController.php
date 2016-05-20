<?php

class page_AdminController extends Zend_Controller_Action
{
	public function init()
	{
			//$this->_helper->getHelper('layout')->setLayout('back');
			$this->_helper->getHelper('layout')->setLayout('bootstrap');
	}

	public function indexAction() {}

	public function listAction() {}

	public function loadListAction()
	{
		$this->_helper->ajaxContext->initJsonContext();
		$model = new page_models_Admin();
		$paginator = $model->getPaginator($this->_getAllParams());
		$paginator->setCurrentPageNumber($this->_getParam('page', 1))
				  ->setItemCountPerPage($this->_getParam('rows'));
		$this->_helper->json(array(
			'currpage' => $paginator->getCurrentPageNumber(),
			'totalpages' => ceil($paginator->getTotalItemCount() / $paginator->getItemCountPerPage()),
			'totalrecords' => $paginator->getTotalItemCount(),
			'rows' => (array) $paginator->getCurrentItems()
		));
	}

	public function newAction()
	{
		$form = new page_forms_New();
		if ($this->getRequest()->isPost() AND $form->isValid($this->_getAllParams()))
			{
				$id = $this->_save($form->getValues());
				return $this->_helper->redirector
					->gotoSimpleAndExit('edit', 'admin', 'page', array('id'=>$id));
			}
		$this->view->form = $form;
	}

	public function editAction()
	{
		$form = new page_forms_Edit();
		if ($this->getRequest()->isPost())
			{
				if ($form->isValid($this->_getAllParams()))
					{
						$id = $this->_save($form->getValues());
						$form->setDescription('Registrato!');
					}
			}
		else
			{
				$model = new page_models_Admin();
				$data = $model->find($this->_getParam('id'), 'it_IT');
				$form->populate($data);
			}
		$this->view->form = $form;
	}

	private function _save($params)
	{
		$model = new page_models_Admin();
		$base = $params['base'];
		$spec = $params['spec'];
		unset($params['base'], $params['spec']);
		$params = array_merge($params, $base, $spec);
		$id = $model->save($params);
		return $id;
	}

	public function deleteAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			if ((!is_null($this->_getParam('oper')) &&  $this->_getParam('oper') === 'del') || (is_null($this->_getParam('oper'))) )
				{
					$model = new page_models_Admin();
					foreach ( explode(',', $this->_getParam('id', '')) as $id )
						{
							$r = $model->delete($id);
							if (!$r) throw new Exception("Error deleting id {$id}");
						}
				}
		}

	/**
	 * Action to use within forms
	 * @throws Exception
	 */
	public function removeAction()
		{
			$id = $this->_getParam('id');
			if (!ctype_digit($id)) throw new Exception("Invalid id");

			$model = new page_models_Admin();
			$r = $model->delete((int)$id);
			if (!$r) throw new Exception("Error deleting id {$id}");
			$this->_helper->redirector
				->gotoSimpleAndExit('list', 'admin', 'page');
		}
}

