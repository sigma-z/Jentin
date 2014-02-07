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
use Jentin\Mvc\Route\RouteInterface;
use Jentin\Mvc\Router\RouterInterface;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Controller\ControllerInterface;
use Jentin\Core\Util;
use Jentin\Core\Plugin\PluginBrokerInterface;
use Jentin\Core\Plugin\PluginBroker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Jentin\Mvc\Event\MvcEvent;
use Jentin\Mvc\Event\RouteEvent;
use Jentin\Mvc\Event\ControllerEvent;
use Jentin\Mvc\Event\ControllerResultEvent;
use Jentin\Mvc\Event\ResponseFilterEvent;

/**
 * HttpKernel
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HttpKernel
{

    const VERSION = '1.0 alpha';


    /**
     * controller class name pattern
     * NOTE: %module% and %controller% will be replaced with module name and controller name
     * @var string
     */
    protected $controllerClassNamePattern = '\%Module%Module\%Controller%Controller';
    /**
     * router
     * @var RouterInterface
     */
    protected $router;
    /**
     * controller pattern (could look like ../%module%/%controller%/controllers)
     * @var string
     */
    protected $controllerDirPattern = '';
    /**
     * modules, that are active for the dispatching process
     * @var array
     */
    protected $modules = array();
    /**
     * event dispatcher
     * @var EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * plugin broker for controllers
     * @var PluginBrokerInterface
     */
    protected $controllerPluginBroker;


    /**
     * constructor
     *
     * @param   string                                                              $controllerDirPattern
     * @param   array                                                               $modules
     * @param   \Jentin\Mvc\Router\RouterInterface                                  $router
     * @param   null|\Symfony\Component\EventDispatcher\EventDispatcherInterface    $eventDispatcher
     * @param   null|\Jentin\Core\Plugin\PluginBrokerInterface                      $controllerPluginBroker
     */
    public function __construct(
            $controllerDirPattern,
            array $modules,
            RouterInterface $router,
            EventDispatcherInterface $eventDispatcher = null,
            PluginBrokerInterface $controllerPluginBroker = null
        )
    {
        $this->controllerDirPattern = $controllerDirPattern;
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
     * @param \Jentin\Core\Plugin\PluginBrokerInterface $pluginBroker
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
     * @param   RequestInterface $request
     * @throws  \DomainException
     * @return  ResponseInterface  $response
     */
    public function handleRequest(RequestInterface $request)
    {
        $eventDispatcher = $this->getEventDispatcher();

        $response = $this->route($request);
        if (!($response instanceof ResponseInterface)) {
            throw new \DomainException(
                'Response type is not valid! You gave: '
                . (is_object($response) ? get_class($response) : gettype($response))
            );
        }

        // EVENT onResponse
        $responseFilterEvent = new ResponseFilterEvent($response);
        $eventDispatcher->dispatch(MvcEvent::ON_FILTER_RESPONSE, $responseFilterEvent);

        // return response
        return $responseFilterEvent->getResponse();
    }


    /**
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    private function route(RequestInterface $request)
    {
        // EVENT onRoute
        $eventDispatcher = $this->getEventDispatcher();
        $routeEvent = new RouteEvent($request);
        $eventDispatcher->dispatch(MvcEvent::ON_ROUTE, $routeEvent);
        if ($routeEvent->hasResponse()) {
            return $routeEvent->getResponse();
        }

        // routes the request
        $route = $this->router->route($request);
        if ($route && $route->hasCallback()) {
            return $this->dispatchRouteCallback($request, $route);
        }

        return $this->dispatchController($request);
    }


    /**
     * @param  RequestInterface     $request
     * @param  Route\RouteInterface $route
     * @return bool|ResponseInterface
     */
    private function dispatchRouteCallback(RequestInterface $request, RouteInterface $route)
    {
        $callbackEvent = new RouteCallbackEvent($request, $route);
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->dispatch(MvcEvent::ON_ROUTE_CALLBACK, $callbackEvent);
        if ($callbackEvent->hasResponse()) {
            return $callbackEvent->getResponse();
        }
        return $route->callback();
    }


    private function dispatchController(RequestInterface $request)
    {
        // create controller
        $controller = $this->newController($request);

        // EVENT onController
        $controllerEvent = new ControllerEvent($controller);
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->dispatch(MvcEvent::ON_CONTROLLER, $controllerEvent);
        if ($controllerEvent->hasResponse()) {
            return $controllerEvent->getResponse();
        }

        $controller = $controllerEvent->getController();
        // controller dispatch
        $controller->preDispatch();
        $response = $controller->dispatch();
        $response = $controller->postDispatch($response);

        if (!($response instanceof ResponseInterface)) {
            $controllerResultEvent = new ControllerResultEvent($controller, $response);
            $eventDispatcher->dispatch(MvcEvent::ON_CONTROLLER_RESULT, $controllerResultEvent);
            $response = $controllerResultEvent->getResponse();
        }
        return $response;
    }


    /**
     * creates controller instance
     *
     * @param   RequestInterface $request
     * @return  ControllerInterface
     */
    public function newController(RequestInterface $request)
    {
        $controllerClass = $this->loadControllerClass($request);
        $eventDispatcher = $this->getEventDispatcher();
        $pluginBroker = $this->getControllerPluginBroker();
        return new $controllerClass($request, $eventDispatcher, $pluginBroker);
    }


    /**
     * loads controller class
     *
     * @param  RequestInterface $request
     * @throws HttpKernelException
     * @return string
     */
    protected function loadControllerClass(RequestInterface $request)
    {
        $moduleName     = $request->getModuleName();
        $controllerName = $request->getControllerName();
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
        $moduleNameCamelCased = Util::getCamelcased($moduleName);
        if (!in_array($moduleNameCamelCased, $this->modules)) {
            throw new HttpKernelException("Module '$moduleNameCamelCased' is not defined!");
        }

        $params = array(
            'controller'    => $controllerName,
            'module'        => $moduleName
        );
        $controllerDir = Util::parsePattern($this->controllerDirPattern, $params);
        if (!is_dir($controllerDir)) {
            $controllerNameCamelCased = Util::getCamelcased($controllerName);
            throw new HttpKernelException(
                    "Controller path for module '$moduleNameCamelCased' and"
                    . " controller '$controllerNameCamelCased' is not defined!"
                    . ' Expected to be: ' . $controllerDir
            );
        }

        return $controllerDir;
    }

}
