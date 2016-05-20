<?php

class estate_AdminController extends Zend_Controller_Action
{
	public function init()
		{
			$this->_helper->getHelper('layout')->setLayout('bootstrap');
		}

	public function indexAction() {}

	public function listAction() {}

	public function loadListAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			$model = new estate_models_Admin();
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
			$form = new estate_forms_New();
			if ($this->getRequest()->isPost() AND $form->isValid($this->_getAllParams()))
				{
					$id = $this->_save($form->getValues());
					return $this->_helper->redirector
						->gotoSimpleAndExit('edit', 'admin', 'estate', array('id'=>$id));
				}
			$this->view->form = $form;
		}

	public function editAction()
		{
			$form = new estate_forms_Edit();
			if ($this->getRequest()->isPost())
				{
					if ($form->isValid($this->_getAllParams()))
						{
							$id = $this->_save($form->getValues());
							/**
							 * TODO: Sostare nel FlashMessager
							 * @todo Sostare nel FlashMessager
							 */
							$form->setDescription('Registrato!');
							$this->_helper->redirector
								->gotoSimpleAndExit('edit', 'admin', 'estate', array('id'=>$id));
						}
				}
			else
				{
					$model = new estate_models_Admin();
					$data = $model->find($this->_getParam('id'), 'it_IT');
					$form->populate($data);
				}
			$this->view->form = $form;
		}

	private function _save($params)
		{
			$model = new estate_models_Admin();
			$id = $model->save($params);
			return $id;
		}

	/**
	 * Action to use with jqGrid
	 * @throws Exception
	 */
	public function deleteAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			if ((!is_null($this->_getParam('oper')) &&  $this->_getParam('oper') === 'del') || (is_null($this->_getParam('oper'))) )
				{
					$model = new estate_models_Admin();
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

			$model = new estate_models_Admin();
			$r = $model->delete((int)$id);
			if (!$r) throw new Exception("Error deleting id {$id}");
			$this->_helper->redirector
				->gotoSimpleAndExit('list', 'admin', 'estate');
		}

	public function blueimpAction()
		{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			$upload_handler = new estate_models_UploadHandler(
				null,
				$this->_getParam('id', 0)
			);

			header('Pragma: no-cache');
			header('Cache-Control: private, no-cache');
			header('Content-Disposition: inline; filename="files.json"');
			header('X-Content-Type-Options: nosniff');
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
			header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'OPTIONS':
					break;
				case 'HEAD':
				case 'GET':
					$upload_handler->get();
					break;
				case 'POST':
					$upload_handler->post();
					break;
				case 'DELETE':
					$upload_handler->delete();
					break;
				default:
					header('HTTP/1.1 405 Method Not Allowed');
			}

		}
}// class

