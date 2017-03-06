<?php
class My_Plugin_Route extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->addDefaultRoutes();

        $route = new Zend_Controller_Router_Route('index/startup',
                                          array('format' => 'json',
                                                'controller' => 'index',
                                                'action' => 'startup'));
        $router->addRoute('indexStartup', $route);
        
        $route = new Zend_Controller_Router_Route('index/play',
                                          array('format' => 'json',
                                                'controller' => 'index',
                                                'action' => 'play'));
        $router->addRoute('indexPlay', $route);

        $route = new Zend_Controller_Router_Route('index/twist',
                                          array('format' => 'json',
                                                'controller' => 'index',
                                                'action' => 'twist'));
        $router->addRoute('indexTwist', $route);

        $route = new Zend_Controller_Router_Route('index/stick',
                                          array('format' => 'json',
                                                'controller' => 'index',
                                                'action' => 'stick'));
        $router->addRoute('indexStick', $route);

        $route = new Zend_Controller_Router_Route('index/reset',
                                          array('format' => 'json',
                                                'controller' => 'index',
                                                'action' => 'reset'));
        $router->addRoute('indexReset', $route);

        $route = new Zend_Controller_Router_Route('index/info',
                                          array('format' => 'json',
                                                'controller' => 'index',
                                                'action' => 'info'));
        $router->addRoute('indexInfo', $route);
    }
}