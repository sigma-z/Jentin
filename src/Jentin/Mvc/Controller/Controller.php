<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Controller;

use Jentin\Mvc\Event\ControllerEvent;
use Jentin\Mvc\Event\MvcEvent;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Util\Util;
use Jentin\Mvc\Plugin\PluginBrokerInterface;
use Jentin\Mvc\Plugin\Pluggable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Controller
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Controller implements ControllerInterface, Pluggable
{

    /** @var RequestInterface */
    protected $request;

    /** @var ResponseInterface|mixed */
    protected $response;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var PluginBrokerInterface */
    protected $pluginBroker;


    /**
     * constructor
     *
     * @param RequestInterface         $request
     * @param EventDispatcherInterface $eventDispatcher
     * @param PluginBrokerInterface    $pluginBroker
     * @param ResponseInterface        $response
     */
    public function __construct(
        RequestInterface $request,
        EventDispatcherInterface $eventDispatcher,
        PluginBrokerInterface $pluginBroker,
        ResponseInterface $response = null
    ) {
        $this->request = $request;
        $this->eventDispatcher = $eventDispatcher;
        $this->pluginBroker = $pluginBroker;
        $this->response = $response;
    }


    /**
     * sets request
     *
     * @param   RequestInterface $request
     * @return  $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }


    /**
     * gets request
     *
     * @return  RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * @param  ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * Returns the response, which can be a response object or a mixed type (eg. string for HTML or array for JSON)
     *
     * @return ResponseInterface|mixed
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * sets plugin broker
     *
     * @param  PluginBrokerInterface $pluginBroker
     * @return $this
     */
    public function setPluginBroker(PluginBrokerInterface $pluginBroker)
    {
        $this->pluginBroker = $pluginBroker;
        return $this;
    }


    /**
     * gets plugin broker
     *
     * @return PluginBrokerInterface
     */
    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }


    /**
     * sets event dispatcher
     *
     * @param  EventDispatcherInterface $eventDispatcher
     * @return $this
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
        return $this->eventDispatcher;
    }


    /**
     * gets (and loads) plugin by a given name
     *
     * @param   string  $name
     * @return  object|callable
     */
    public function plugin($name)
    {
        $plugin = $this->pluginBroker->load($name);
        if ($plugin instanceof ControllerAware && $this !== $plugin->getController()) {
            $plugin->setController($this);
        }
        return $plugin;
    }


    /**
     * Executed before Controller::dispatch()
     */
    public function preDispatch()
    {
    }


    /**
     * Executed after Controller::dispatch()
     */
    public function postDispatch()
    {
    }


    public function dispatch()
    {
        $this->preDispatch();
        $this->processDispatch();
        $this->postDispatch();
    }


    /**
     * @throws ControllerException
     */
    protected function processDispatch()
    {
        $controllerEvent = new ControllerEvent($this);
        $this->eventDispatcher->dispatch(MvcEvent::ON_CONTROLLER_DISPATCH, $controllerEvent);
        if ($controllerEvent->hasResponse()) {
            $this->response = $controllerEvent->getResponse();
            return;
        }
        $actionMethod = $this->getActionMethod();
        if (method_exists($this, $actionMethod)) {
            $response = call_user_func(array($this, $actionMethod));
            if ($response) {
                $this->response = $response;
            }
            return;
        }
        $moduleName = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        throw new ControllerException(
            "The controller $controllerName (module: $moduleName) does not implement method $actionMethod!"
        );
    }


    /**
     * gets action method
     *
     * @return string
     */
    public function getActionMethod()
    {
        $actionMethod = Util::getCamelCased($this->request->getActionName()) . 'Action';
        $actionMethod = lcfirst($actionMethod);
        return $actionMethod;
    }

}
