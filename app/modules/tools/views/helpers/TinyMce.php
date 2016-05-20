<?php

class Zend_View_Helper_TinyMce implements Zend_View_Helper_Interface
{
	private $_defaults = array(
		'mode' => "textareas",
		'theme' => "advanced",
		'plugins' => "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist",
		'theme_advanced_buttons1' => "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
		'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink",
		'theme_advanced_buttons3' => "anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,sub,sup,|,charmap,media,advhr,|,print",
		'theme_advanced_buttons4' => "tablecontrols,|,hr,removeformat,visualaid,|,fullscreen",
		'theme_advanced_buttons5' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
		'theme_advanced_toolbar_location' => "top",
		'theme_advanced_toolbar_align' => "left",
		'theme_advanced_statusbar_location' => "bottom",
		'theme_advanced_resizing' => false,
		'theme_advanced_font_sizes' => "8px,9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px",
		'content_css' => "/pub/css/frontend.css",
		'skin' => "o2k7",
		'convert_urls' => false,
		'relative_urls' => false,
		'remove_script_host' => false,
		'file_browser_callback' => 'apeFilemanager_browser_callback'
	);

	public function tinyMce(array $config)
	{
		$_cfg = Zend_Json::encode(array_merge($this->_defaults, $config));
		return "tinyMCE.init({$_cfg});";
	}

	/**
	* @var Zend_View_Interface
	*/
	public $view = null;

	/**
	 * @param  Zend_View_Interface $view
	 * @return Zend_View_Helper_Abstract
	 */
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
		return $this;
	}

	/**
	 * @return void
	 */
	public function direct()
	{
		return $this;
	}
}