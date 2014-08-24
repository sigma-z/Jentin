<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin;

use Jentin\Mvc\Event\MvcEvent;
use Jentin\Mvc\EventListener\AutoConvertResponseIntoHtmlOrJsonListener;
use Jentin\Mvc\HttpKernel;
use Jentin\Mvc\Plugin\PluginBroker;
use Jentin\Mvc\Plugin\RouteUrl;
use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Router\Router;
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


    public function __construct($appRoot, array $modules)
    {
        $this->appRoot = $appRoot;
        $this->modules = $modules;
    }


    protected function init()
    {
        $this->initRouter();
        $this->initPlugins();
        $this->initEventDispatcher();
    }


    protected function initRouter()
    {
        $this->router = new Router();
    }


    protected function initEventDispatcher()
    {
        // listener converts string results into Html-Responses and array results into Json-Responses
        $htmlJsonControllerResultListener = new AutoConvertResponseIntoHtmlOrJsonListener();

        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addListener(
            MvcEvent::ON_FILTER_RESPONSE,
            array($htmlJsonControllerResultListener, 'getResponse')
        );
    }


    protected function initPlugins()
    {
        // router plugin (for controllers and views, where you can call $this->plugin('route')->getUrl())
        $this->plugins['routeUrl'] = new RouteUrl($this->router, $this->request);
        // view directory pattern
        $viewDirPattern = $this->appRoot . '/%module%/view/%controller%';

        // plugin broker for view renderer
        $viewPluginBroker = new PluginBroker();
        $viewPluginBroker->register('route', array($this->plugins['routeUrl']));
        // enable layout?
        $layoutEnabled = true;
        $viewPluginArgs = array($viewDirPattern, $viewPluginBroker, $layoutEnabled);
        $this->plugins['view'] = array('\Jentin\Mvc\Plugin\View', $viewPluginArgs);
    }


    /**
     * @return HttpKernel
     */
    public function getHttpKernel()
    {
        $this->init();

        // controller directory pattern
        $controllerDirPattern = $this->appRoot . '/%Module%/controllers';
        // creating the http kernel
        $httpKernel = new HttpKernel($controllerDirPattern, $this->modules, $this->router, $this->eventDispatcher);
        // controller plugin broker
        $controllerPluginBroker = new PluginBroker();
        $controllerPluginBroker->register('route', $this->plugins['routeUrl']);
        $controllerPluginBroker->register('view', $this->plugins['view']);

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
