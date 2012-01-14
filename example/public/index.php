<?php

$libDir = __DIR__ . '/../../lib';
$extLibDir = __DIR__ . '/../../../ext_libraries/';
require $libDir . '/Jentin/ClassLoader/NamespaceClassLoader.php';

// register class namespaces to class loader
$classLoader = new \Jentin\ClassLoader\NamespaceClassLoader();
$classLoader->setNamespace('Jentin', $libDir);
$classLoader->setNamespace('Symfony', $extLibDir);
$classLoader->register();

// request
$request = new \Jentin\Mvc\Request\Request();
// router for routing
$router = new \Jentin\Mvc\Router\Router();
// default route
$router->setRoute('default', new \Jentin\Mvc\Route\Route('(/%module%)(/%controller%)(/%action%)'));
// router plugin (for controllers and views, where you can call $this->plugin('route')->getUrl())
$routePlugin = new \Jentin\Mvc\Plugin\RouteUrl($router, $request);

// view directory pattern
$viewDirPattern = __DIR__ . '/../app/%module%/view/%controller%';
// plugin broker for view renderer
$viewPluginBroker = new \Jentin\Core\Plugin\PluginBroker();
$viewPluginBroker->register('route', array($routePlugin));
// enable layout?
$layoutEnabled = true;

// controller plugin broker
$controllerPluginBroker = new \Jentin\Core\Plugin\PluginBroker();
$controllerPluginBroker->register('route', $routePlugin);
$viewPluginArgs = array($viewDirPattern, $viewPluginBroker, $layoutEnabled);
$controllerPluginBroker->register('view', array('\Jentin\Mvc\Plugin\View', $viewPluginArgs));

// controller result listener converts string results into Html-Responses and array results into Json-Responses
$htmlJsonControllerResultListener = new \Jentin\Mvc\EventListener\HtmlJsonControllerResultListener();

// event dispatcher
$eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_RESULT,
    array($htmlJsonControllerResultListener, 'getResponse')
);

// controller directory pattern
$controllerDirPattern = __DIR__ . '/../app/%Module%/controllers';

// http kernel
$httpKernel = new \Jentin\Mvc\HttpKernel($controllerDirPattern, array('Default'), $router, $eventDispatcher);
$httpKernel->setControllerPluginBroker($controllerPluginBroker);

// http kernel handles the request
$response = $httpKernel->handleRequest($request);
// send response
$response->sendResponse();
