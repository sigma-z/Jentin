<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc;

use Jentin\Mvc\Event\RouteCallbackEvent;
use Jentin\Mvc\Response\JsonResponse;
use Jentin\Mvc\Response\Response;
use Jentin\Mvc\Route\RouteInterface;
use Jentin\Mvc\Router\RouterInterface;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Controller\ControllerInterface;
use Jentin\Mvc\Util\Util;
use Jentin\Mvc\Plugin\PluginBrokerInterface;
use Jentin\Mvc\Plugin\PluginBroker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Jentin\Mvc\Event\MvcEvent;
use Jentin\Mvc\Event\RouteEvent;
use Jentin\Mvc\Event\ControllerEvent;
use Jentin\Mvc\Event\ResponseFilterEvent;

/**
 * HttpKernel
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HttpKernel
{

    const VERSION = '1.0.1';


    /**
     * controller class name pattern
     * NOTE: %module% and %controller% will be replaced with module name and controller name
     * @var string
     */
    protected $controllerClassNamePattern = '\%Module%Module\%Controller%Controller';

    /** @var RouterInterface */
    protected $router;

    /**
     * controller pattern (could look like ../%module%/%controller%/controllers)
     * @var string
     */
    protected $controllerPathPattern = '';

    /**
     * modules, that are active for the dispatching process
     * @var array
     */
    protected $modules = array();

    /** @var EventDispatcher */
    protected $eventDispatcher;

    /** @var PluginBrokerInterface */
    protected $controllerPluginBroker;

    /** @var RequestInterface */
    protected $request;

    /** @var ResponseInterface|mixed */
    protected $response;


    /**
     * constructor
     *
     * @param   string                        $controllerPathPattern
     * @param   array                         $modules
     * @param   RouterInterface               $router
     * @param   null|EventDispatcherInterface $eventDispatcher
     * @param   null|PluginBrokerInterface    $controllerPluginBroker
     */
    public function __construct(
        $controllerPathPattern,
        array $modules,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher = null,
        PluginBrokerInterface $controllerPluginBroker = null
    ) {
        $this->controllerPathPattern = $controllerPathPattern;
        $this->modules = $modules;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->controllerPluginBroker = $controllerPluginBroker;
    }


    /**
     * gets router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }


    /**
     * gets controller class name pattern
     *
     * @return string
     */
    public function getControllerClassNamePattern()
    {
        return $this->controllerClassNamePattern;
    }


    /**
     * sets controller name pattern
     *
     * @param   string $pattern
     * @return  HttpKernel
     */
    public function setControllerClassNamePattern($pattern)
    {
        $this->controllerClassNamePattern = $pattern;
        return $this;
    }


    /**
     * sets controller plugin broker
     *
     * @param  PluginBrokerInterface $pluginBroker
     * @return HttpKernel
     */
    public function setControllerPluginBroker(PluginBrokerInterface $pluginBroker)
    {
        $this->controllerPluginBroker = $pluginBroker;
        return $this;
    }


    /**
     * gets controller plugin broker
     *
     * @return PluginBrokerInterface
     */
    public function getControllerPluginBroker()
    {
        if ($this->controllerPluginBroker === null) {
            $this->controllerPluginBroker = new PluginBroker();
        }
        return $this->controllerPluginBroker;
    }


    /**
     * sets event dispatcher
     *
     * @param  EventDispatcherInterface $eventDispatcher
     * @return HttpKernel
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }


    /**
     * gets event dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }
        return $this->eventDispatcher;
    }


    /**
     * handles request
     *
     * @param  RequestInterface  $request
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function handleRequest(RequestInterface $request, ResponseInterface $response = null)
    {
        $this->request = $request;
        if ($response) {
            $this->response = $response;
        }
        $this->route();

        $responseFilterEvent = new ResponseFilterEvent($this->request, $this->response);
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->dispatch(MvcEvent::ON_FILTER_RESPONSE, $responseFilterEvent);

        $response = $responseFilterEvent->getResponse();
        return $this->convertResponseIntoHtmlOrJsonResponse($response);
    }


    private function route()
    {
        // EVENT onRoute
        $routeEvent = new RouteEvent($this->request);
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->dispatch(MvcEvent::ON_ROUTE, $routeEvent);
        if ($routeEvent->hasResponse()) {
            $this->response = $routeEvent->getResponse();
            if ($this->request->isDispatched()) {
                return;
            }
        }

        // routes the request
        $route = $this->router->route($this->request);
        if ($route && $route->hasCallback()) {
            $this->dispatchRouteCallback($route);
            $this->request->setDispatched(true);
            return;
        }

        $this->dispatchController();
    }


    /**
     * @param  Route\RouteInterface $route
     * @return bool|ResponseInterface
     */
    private function dispatchRouteCallback(RouteInterface $route)
    {
        $callbackEvent = new RouteCallbackEvent($this->request, $route, $this->response);
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->dispatch(MvcEvent::ON_ROUTE_CALLBACK, $callbackEvent);
        if ($callbackEvent->hasResponse()) {
            $this->response = $callbackEvent->getResponse();
            if ($this->request->isDispatched()) {
                return;
            }
        }
        $response = $route->callback($this->request, $this->response);
        if ($response) {
            $this->response = $response;
        }
    }


    private function dispatchController()
    {
        // create controller
        $controller = $this->newController();

        // EVENT onController
        $controllerEvent = new ControllerEvent($controller);
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->dispatch(MvcEvent::ON_CONTROLLER, $controllerEvent);
        if ($controllerEvent->hasResponse()) {
            $this->response = $controllerEvent->getResponse();
            if ($this->request->isDispatched()) {
                return;
            }
        }

        $controller = $controllerEvent->getController();

        $this->request->setDispatched(true);

        // dispatch controller action
        $controller->dispatch();

        $this->response = $controller->getResponse();
    }


    /**
     * creates controller instance
     *
     * @throws HttpKernelException
     * @return ControllerInterface
     */
    public function newController()
    {
        $controllerClass = $this->loadControllerClass();
        $eventDispatcher = $this->getEventDispatcher();
        $pluginBroker = $this->getControllerPluginBroker();
        return new $controllerClass($this->request, $eventDispatcher, $pluginBroker, $this->response);
    }


    /**
     * loads controller class
     *
     * @throws HttpKernelException
     * @return string
     */
    protected function loadControllerClass()
    {
        $moduleName     = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        // fully qualified controller class name
        $fullQualifiedClassName = $this->getControllerClassName($moduleName, $controllerName);

        // load controller class
        if (!class_exists($fullQualifiedClassName, false)) {
            // relative controller class name (without namespace)
            $posLastBackslash = strrpos($fullQualifiedClassName, '\\');
            // class name
            $className = substr($fullQualifiedClassName, $posLastBackslash + 1);
            // path to controller classes for that module
            $controllerPath = $this->getControllerPath($moduleName, $controllerName);
            // controller class file
            $classFile = $controllerPath . DIRECTORY_SEPARATOR . $className . '.php';

            if (!is_file($classFile)) {
                throw new HttpKernelException("Could not find file '$classFile' for class '$fullQualifiedClassName'!");
            }
            /** @noinspection PhpIncludeInspection */
            require_once $classFile;
        }

        return $fullQualifiedClassName;
    }


    /**
     * gets controller class name
     *
     * @param   string  $moduleName
     * @param   string  $controllerName
     * @return  string
     */
    public function getControllerClassName($moduleName, $controllerName)
    {
        $params = array('module' => $moduleName, 'controller' => $controllerName);
        // absolute controller class name (with namespace)
        $fullQualifiedClassName = Util::parsePattern($this->controllerClassNamePattern, $params);
        // add namespace separator to make the class name absolute
        if ($fullQualifiedClassName[0] != '\\') {
            $fullQualifiedClassName = '\\' . $fullQualifiedClassName;
        }
        return $fullQualifiedClassName;
    }


    /**
     * gets controller path
     *
     * @param   string  $moduleName
     * @param   string  $controllerName
     * @return  string
     *
     * @throws  HttpKernelException if controller directory is not a directory
     */
    public function getControllerPath($moduleName, $controllerName)
    {
        $moduleNameCamelCased = Util::getCamelCased($moduleName);
        if (!in_array($moduleNameCamelCased, $this->modules)) {
            throw new HttpKernelException("Module '$moduleNameCamelCased' is not defined!");
        }

        $params = array(
            'controller'    => $controllerName,
            'module'        => $moduleName
        );
        $controllerDir = Util::parsePattern($this->controllerPathPattern, $params);
        if (!is_dir($controllerDir)) {
            $controllerNameCamelCased = Util::getCamelCased($controllerName);
            throw new HttpKernelException(
                    "Controller path for module '$moduleNameCamelCased' and"
                    . " controller '$controllerNameCamelCased' is not defined!"
                    . ' Expected to be: ' . $controllerDir
            );
        }

        return $controllerDir;
    }


    /**
     * @param  ResponseInterface|mixed $responseContent
     * @return ResponseInterface
     */
    private function convertResponseIntoHtmlOrJsonResponse($responseContent)
    {
        if ($responseContent instanceof ResponseInterface) {
            return $responseContent;
        }

        if (is_array($responseContent)) {
            $response = new JsonResponse();
            $response->setContent($responseContent);
        }
        else {
            $response = new Response();
            $response->setContentType('text/html; charset=utf-8');
            $response->setContent((string)$responseContent);
        }
        return $response;
    }

}
