<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin;

use Jentin\Mvc\HttpKernel;
use Jentin\Mvc\Plugin\PluginBroker;
use Jentin\Mvc\Plugin\RouteUrl;
use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Route\RouteInterface;
use Jentin\Mvc\Router\Router;
use Jentin\Mvc\Router\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Application
 * @author  Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Application
{

    /** @var RequestInterface */
    private $request;

    /** @var ResponseInterface */
    private $response;

    /** @var Router */
    private $router;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var string[] */
    private $modules = array();

    /** @var array */
    private $plugins = array();

    /** @var string */
    private $appRoot = '';

    /** @var string */
    private $controllerClassNamePattern = '';

    /** @var string */
    private $controllerPathPattern = '';

    /** @var string */
    private $viewPathPattern = '';

    /** @var bool */
    private $layoutEnabled = false;

    /** @var bool */
    private $viewPluginDisabled = false;


    public function __construct($appRoot, array $modules)
    {
        $this->appRoot = $appRoot;
        $this->modules = $modules;
        $this->controllerClassNamePattern = '\%Module%Module\%Controller%Controller';
        $this->viewPathPattern = $this->appRoot . '/%Module%/views/%controller%';
        $this->controllerPathPattern = $this->appRoot . '/%Module%/controllers';
    }


    protected function init()
    {
        $this->initRouter();
        $this->initPlugins();
    }


    protected function initRouter()
    {
        if (!$this->router) {
            $this->router = new Router();
        }
    }


    /**
     * @param RouterInterface $router
     * @return $this
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
        return $this;
    }


    /**
     * @param  string                $name
     * @param  RouteInterface|string $route
     * @return RouteInterface
     */
    public function addRoute($name, $route)
    {
        $this->initRouter();
        return $this->router->addRoute($name, $route);
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
     * @param  string $controllerPathPattern
     * @return $this
     */
    public function setControllerPathPattern($controllerPathPattern)
    {
        $this->controllerPathPattern = $controllerPathPattern;
        return $this;
    }


    /**
     * @param  string $viewPathPattern
     * @return $this
     */
    public function setViewPathPattern($viewPathPattern)
    {
        $this->viewPathPattern = $viewPathPattern;
        return $this;
    }


    /**
     * @param  bool $enabled
     * @return $this
     */
    public function enableLayoutView($enabled = true)
    {
        $this->layoutEnabled = $enabled;
        return $this;
    }


    /**
     * @param  bool $disable
     * @return $this
     */
    public function disableViewPlugin($disable = true)
    {
        $this->viewPluginDisabled = $disable;
        return $this;
    }


    protected function initPlugins()
    {
        $this->initRouter();

        // router plugin (for controllers and views, where you can call $this->plugin('route')->getUrl())
        $this->plugins['routeUrl'] = new RouteUrl($this->router, $this->request);

        if (!$this->viewPluginDisabled) {
            // plugin broker for view renderer
            $viewPluginBroker = new PluginBroker();
            $viewPluginBroker->register('route', $this->plugins['routeUrl']);
            // enable layout?
            $viewPluginArgs = array($this->viewPathPattern, $viewPluginBroker, $this->layoutEnabled);
            $this->plugins['view'] = array('\Jentin\Mvc\Plugin\View', $viewPluginArgs);
        }
    }


    /**
     * @return HttpKernel
     */
    public function getHttpKernel()
    {
        $this->init();

        // creating the http kernel
        $httpKernel = new HttpKernel($this->controllerPathPattern, $this->modules, $this->router, $this->eventDispatcher);
        // controller plugin broker
        $controllerPluginBroker = new PluginBroker();
        $controllerPluginBroker->register('route', $this->plugins['routeUrl']);
        if (!$this->viewPluginDisabled) {
            $controllerPluginBroker->register('view', $this->plugins['view']);
        }

        $httpKernel->setControllerClassNamePattern($this->controllerClassNamePattern);
        $httpKernel->setControllerPluginBroker($controllerPluginBroker);

        return $httpKernel;
    }


    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     */
    public function run(RequestInterface $request = null, ResponseInterface $response = null)
    {
        $this->request = $request ?: new Request();

        // handling the request
        $httpKernel = $this->getHttpKernel();
        $this->response = $httpKernel->handleRequest($this->request, $response);
        $this->response->sendResponse();
    }


    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

}
