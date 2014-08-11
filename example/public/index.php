<?php

$start = microtime(true);

require __DIR__  . '/../../vendor/autoload.php';

$request = new \Jentin\Mvc\Request\Request();
$router = new \Jentin\Mvc\Router\Router();
// defining the default route
//$router->setRoute('default', new \Jentin\Mvc\Route\Route('(/%module%)(/%controller%)(/%action%)'));
// router plugin (for controllers and views, where you can call $this->plugin('route')->getUrl())
//$routePlugin = new \Jentin\Mvc\Plugin\RouteUrl($router, $request);

// plugin broker for view renderer
$viewPluginBroker = new \Jentin\Core\Plugin\PluginBroker();
//$viewPluginBroker->register('route', array($routePlugin));
// enable layout?
$layoutEnabled = true;

// controller plugin broker
$controllerPluginBroker = new \Jentin\Core\Plugin\PluginBroker();
//$controllerPluginBroker->register('route', $routePlugin);
// view directory pattern
$viewDirPattern = __DIR__ . '/../app/%module%/view/%controller%';
$viewPluginArgs = array($viewDirPattern, $viewPluginBroker, $layoutEnabled);
$controllerPluginBroker->register('view', array('\Jentin\Mvc\Plugin\View', $viewPluginArgs));

// controller result listener converts string results into Html-Responses and array results into Json-Responses
$htmlJsonControllerResultListener = new \Jentin\Mvc\EventListener\HtmlJsonControllerResultListener();

$eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_RESULT,
    array($htmlJsonControllerResultListener, 'getResponse')
);

// controller directory pattern
$controllerDirPattern = __DIR__ . '/../app/%Module%/controllers';

// creating the http kernel
$modules = array('Default');
$httpKernel = new \Jentin\Mvc\HttpKernel($controllerDirPattern, $modules, $router, $eventDispatcher);
$httpKernel->setControllerPluginBroker($controllerPluginBroker);

// handling the request
$response = $httpKernel->handleRequest($request);
// sending the response
$response->sendResponse();

echo '<span style="font-size: 10px;">Execution time: ' . number_format(microtime(true) - $start, 4) . 's</span>';
