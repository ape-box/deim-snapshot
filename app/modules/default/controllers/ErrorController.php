<?php

class ErrorController extends Zend_Controller_Action
{
	public function errorAction()
		{
			$errors = $this->_getParam('error_handler');
			switch ($errors->type)
				{
					case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
					case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
					case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
						$this->getResponse()
							->setRawHeader('HTTP/1.1 404 Not Found');
						break;
					default:
						$this->_response->setHttpResponseCode(500);
						break;
				}

			/* @var $e Exception */
			$e = $errors->exception;
			$this->view->error = $e;

			ob_start();
			echo <<<HEREDOC
<style>
table {
    border-collapse: collapse;
}

table tr {
}

table tr:nth-child(even) {
    background-color: #DDDDDD;
}

table tr:nth-child(odd) {
    background-color: #EEEEEE;
}

table tr td {
    border: 1px solid;
    padding: 5px;
    vertical-align: top;
}
</style>
<table>
	<tr>
		<td>Host: </ts><td>{$_SERVER['HTTP_HOST']}</td>
	</tr>
	<tr>
		<td>Errore: </ts><td>{$e->getMessage()}</td>
	</tr>
	<tr>
		<td>Trace: </ts><td><pre>{$e->getTraceAsString()}</pre></td>
	</tr>
	<tr>
		<td>SERVER: </ts><td><pre>
HEREDOC;
			var_export($_SERVER);
			echo <<<HEREDOC
		</pre></td>
	</tr>
	<tr>
		<td>REQUEST: </ts><td><pre>
HEREDOC;
			var_export($_REQUEST);
			echo <<<HEREDOC
		</pre></td>
	</tr>
	<tr>
		<td>SESSION: </ts><td><pre>
HEREDOC;
			var_export($_SESSION);
			echo <<<HEREDOC
		</pre></td>
	</tr>
</table>
HEREDOC;
			$html = ob_get_clean();
			ob_end_clean();

			if ($_SERVER['HTTP_HOST'] === 'dev.demo-immobiliare.com')
			{
				echo $html;die;
			}
			else {
				/*
				$mail = new Zend_Mail('utf-8');
				$mail->setBodyHtml($html);
				$mail->addTo('alessio.peternelli@gmail.com');
				$mail->setSubject('deim: debug dump');
				$mail->setFrom('alessio.peternelli@gmail.com');
				$mail->send();
				*/
			}
		}
}
