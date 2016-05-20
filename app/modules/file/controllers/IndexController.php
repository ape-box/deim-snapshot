<?php

class file_IndexController extends  Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function imageAction()
	{
		$file = new file_models_File_File($this->_getParam('path'));
		$this->_serveImage($file);
	}

	public function mediaAction()
	{
		$file = new file_models_Media_File($this->_getParam('id'));
		$this->_serveImage($file);
	}

	/**
	 * @param file_models_Interface $file
	 * @throws Zend_Controller_Action_Exception
	 */
	private function _serveImage($file)
	{
		if(!$file->isImage())
			throw new Zend_Controller_Action_Exception("Il file " . $file->getFullName() . " che si cerca di visualizzare non è un'immagine");
		else
			{
				$this->_setResponseHeader($file, 'inline', true);
				if (($mcached = $this->_request->getHeader('If-Modified-Since')) != null  && $file->getMtime() < strtotime($mcached))
					$this->_response->setHttpResponseCode(304);
				else
					{
						$image = $file->editImage(
							$this->_getParam('w', 160),
							$this->_getParam('h', 120)
						);
						$this->_response->setBody($image);
					}
			}
	}// func

	public function downloadAction()
		{
			$file = new file_models_File_File($this->_getParam('path'));
			$this->_setResponseHeader($file, 'attachment');
			$this->_response->setBody($file->getContent());
		}

	public function getAction()
		{
			$file = new file_models_File_File($this->_getParam('path'));
			$this->_setResponseHeader($file, 'inline');
			$this->_response->setBody($file->getContent());
		}

	public function dirListAction()
		{
			$this->_helper->ajaxContext->initJsonContext();
			$dir = new file_models_Directory($this->_getParam('path'));
			$this->_helper->json($dir->listFiles());
		}

	/**
	 * @param file_models_File_File $file
	 * @param string $disposition
	 * @param bool $enableCache
	 */
	private function _setResponseHeader($file, $disposition, $enableCache=false)
		{
			$this->_response->setHeader('Date', gmdate('D, d M Y H:i:s') . ' GMT');
			$this->_response->setHeader('Content-Description', $file->getName());
			$this->_response->setHeader('Content-type', $file->getMime());
			$this->_response->setHeader('Content-Disposition', $disposition . '; filename="'
				. addslashes($file->getName()) . '.' . $file->getExt() .'"');
			$this->_response->setHeader('Content-Transfer-Encoding', 'binary');
			if ($enableCache)
				{
					$this->_response->setHeader('Cache-Control', 'public, max-age=3600', true);//1 hour
					$this->_response->setHeader('Expires', gmdate("D, d M Y H:i:s", time() + 3600) . " GMT", true);
					$this->_response->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT", true);
					$this->_response->setHeader('Pragma', 'public, max-age=3600', true);//1 hour
				}
			$this->_response->setHeader('Connection', 'close');
		}
}