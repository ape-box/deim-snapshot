<?php

class tools_models_Breadcrumbs
{
    static private $_list = array();
    
    static private $_home_is_top = true;

    static public function addCrumb($label, $url)
    {
        $key = md5($label);
        self::$_list[$key] = array(
            'label' => $label,
            'url' => $url,
            'separator' => true
        );
    }
    
    static public function disableTopHome()
    {
        self::$_home_is_top = false;
    }

    static public function enableTopHome()
    {
        self::$_home_is_top = true;
    }

    static public function render()
    {
        /* @var $view Zend_View */
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->setScriptPath(realpath(dirname(__FILE__).'/../views/templates/breadcrumbs/'));
        
        $bread = self::$_home_is_top ? $view->assign(array(
            'label' => 'home',
            'url' => '/',
            'seèarator' => false
        ))->render('item.phtml') : '';
        
        foreach (self::$_list as $vars)
        {
            $bread .= $view->assign($vars)->render('item.phtml');
        }
        return $bread;
    }
}