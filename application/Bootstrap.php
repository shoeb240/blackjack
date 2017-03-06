<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_appNamespace = 'Application';
    
    protected function _initPlaceholders()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        $view->docType('XHTML1_TRANSITIONAL');
        $view->headTitle('Blackjack');
        $view->headLink()->prependStylesheet('/css/layout.css')
                         ->appendStylesheet('/css/game.css');
        
        $view->headScript()->prependFile('/scripts/jquery-1.7.1.min.js');
    }
    
}