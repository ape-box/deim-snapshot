<?php

class file_ManagerController extends Zend_Controller_Action
{
	/**
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	private $_flash_messenger;
	const FLASH_MESSENGER_SESSION_NAMESPACE = 'file_manager_flash_messenger_namespace';

	/**
	 * Get the parent of folder,
	 * use with function that alter current folder
	 *
	 * @param string $folder
	 */
	private function _parentFolder($folder)
	{
		$folder = $this->_unslashFolder($folder);
		$folder = explode(DIRECTORY_SEPARATOR, $folder);
		array_pop($folder);
		$folder = implode(DIRECTORY_SEPARATOR, $folder);
		return $folder;
	}

	/**
	 * Stip last slash from folder
	 *
	 * @param string $folder
	 */
	private function _unslashFolder($folder)
	{
		$folder = str_replace('//', '/', $folder);
		$folder = substr($folder, -1) === DIRECTORY_SEPARATOR ? substr($folder, 0, -1) : $folder;
		return $folder;
	}

	/**
	 * Retrive standard backurl with the option to alter it on the fly
	 *
	 * @param string $folder
	 * @param string $url
	 */
	private function _getBackUrl($folder, $url = false)
	{
		$url = $url ?: '/file/manager/list/mode/'.$this->_getParam('mode', 'plugin').'?folder=';
		$folder = $this->_unslashFolder($folder);
		$folder = explode('/', $folder);
		foreach ($folder as $k => $f)
			$folder[$k] = urlencode($f);
		$folder = '/'.implode('/', $folder);
		return $url . $folder;
	}

	/**
	 * Dispatch the requested action
	 * and add error control and rescue
	 *
	 * @param string $action Method name of action
	 * @return void
	 */
	public function dispatch($action)
	{
		try {
			parent::dispatch($action);
		} catch (file_models_File_Exception $e){
			$this->_flash_messenger->addMessage($e->getMessage());
			$model = new file_models_File_Manager();
			$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
			return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($this->_parentFolder($folder)));
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_flash_messenger = $this->_helper->getHelper('FlashMessenger');
		$this->_flash_messenger->setNamespace(self::FLASH_MESSENGER_SESSION_NAMESPACE);
		$this->view->mode = $this->_getParam('mode', 'plugin');
	}

	/**
	 * List FILES and DIRECTORIES in the current folder or root
	 */
	public function listAction()
	{
		$folder = $this->_getParam('folder', DIRECTORY_SEPARATOR);
		$folder = pathinfo($folder, PATHINFO_EXTENSION)=='' ? $folder : pathinfo($folder, PATHINFO_DIRNAME);
		$model = new file_models_File_Manager();
		$this->view->directories = $model->listDir($folder);
		$this->view->files = $model->listFiles($folder);
		$this->view->folder = $model->getCurrentFolder($folder);
		if ($this->view->folder !== DIRECTORY_SEPARATOR) $this->view->folder .= DIRECTORY_SEPARATOR;
		$this->view->errors = $this->_flash_messenger->getMessages();
	}

	/**
	 * Rename FILE $from $to
	 */
	public function renameFileAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->renameFile($folder, $this->_getParam('from'), $this->_getParam('to'));
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Put in the clipboard the path to $file for copy and paste purposess
	 */
	public function copyFileAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->copyFile($folder, $this->_getParam('file'));
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Put in the clipboard the path to $file for cut and paste purposess
	 */
	public function cutFileAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->cutFile($folder, $this->_getParam('file'));
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Paste a FILE path from clipboard into current folder
	 */
	public function pasteFileAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->pasteTo($folder);
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Delete FILE $file in the current folder
	 */
	public function deleteFileAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->deleteFile($folder, $this->_getParam('file'));
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Create new DIRECTORY $name
	 */
	public function createDirectoryAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->createDir($folder, $this->_getParam('name'));
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Rename DIRECTORY current folder to $to
	 */
	public function renameDirectoryAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->renameDir($folder, $this->_getParam('to'));
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($this->_parentFolder($folder)));
	}

	/**
	 * Put a DIRECTORY current folder into the clipboard for cut and paste purposess
	 */
	public function cutDirectoryAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->cutDir($folder);
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Paste DIRECTORY from clipboard into current folder
	 */
	public function pasteDirectoryAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->pasteTo($folder);
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
	}

	/**
	 * Delete DIRECTORY current folder and FILES into it
	 */
	public function deleteDirectoryAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));
		$model->deleteDir($folder);
		return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($this->_parentFolder($folder)));
	}

	/**
	 * Show upload form and recive uploaded file
	 */
	public function uploadFileAction()
	{
		$model = new file_models_File_Manager();
		$folder = $model->getCurrentFolder($this->_getParam('folder', DIRECTORY_SEPARATOR));

		if ($this->_request->isPost()) {
			$save_success = $model->registerUpload($this->_getAllParams(), $folder, new file_forms_Upload());
			if($save_success === true)
				return $this->_helper->redirector->gotoUrlAndExit($this->_getBackUrl($folder));
		}

		$this->view->form = $model->showUploadForm(array(), $folder);
	}
}



