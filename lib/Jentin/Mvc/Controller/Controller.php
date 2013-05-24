<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Controller;

use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Core\Util;
use Jentin\Core\Plugin\PluginBrokerInterface;
use Jentin\Core\Plugin\Pluggable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Controller
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Controller implements ControllerInterface, Pluggable
{

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \Jentin\Core\Plugin\PluginBrokerInterface
     */
    protected $pluginBroker;


    /**
     * constructor
     *
     * @param \Jentin\Mvc\Request\RequestInterface                          $request
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface   $eventDispatcher
     * @param \Jentin\Core\Plugin\PluginBrokerInterface                     $pluginBroker
     */
    public function __construct(
            RequestInterface $request,
            EventDispatcherInterface $eventDispatcher,
            PluginBrokerInterface $pluginBroker
        )
    {
        $this->request = $request;
        $this->eventDispatcher = $eventDispatcher;
        $this->pluginBroker = $pluginBroker;
    }


    /**
     * sets request
     *
     * @param   \Jentin\Mvc\Request\RequestInterface $request
     * @return  Controller
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }


    /**
     * gets request
     *
     * @return  \Jentin\Mvc\Request\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * sets plugin broker
     *
     * @param \Jentin\Core\Plugin\PluginBrokerInterface $pluginBroker
     * @return Controller
     */
    public function setPluginBroker(PluginBrokerInterface $pluginBroker)
    {
        $this->pluginBroker = $pluginBroker;
        return $this;
    }


    /**
     * gets plugin broker
     *
     * @return \Jentin\Core\Plugin\PluginBrokerInterface
     */
    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }


    /**
     * sets event dispatcher
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @return Controller
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }


    /**
     * gets event dispatcher
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
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
     *
     * @param  mixed $response
     * @return mixed
     */
    public function postDispatch($response)
    {
        return $response;
    }


    /**
     * dispatches
     *
     * @return  ResponseInterface
     * @throws  ControllerException if action method is not found
     */
    public function dispatch()
    {
        $controllerEvent = new \Jentin\Mvc\Event\ControllerEvent($this);
        $this->eventDispatcher->dispatch(\Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_DISPATCH, $controllerEvent);
        if ($controllerEvent->isPropagationStopped()) {
            return $controllerEvent->getResponse();
        }

        $actionMethod = $this->getActionMethod();
        if (method_exists($this, $actionMethod)) {
            return call_user_func(array($this, $actionMethod));
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
        $actionMethod = Util::getCamelcased($this->request->getActionName()) . 'Action';
        $actionMethod = lcfirst($actionMethod);
        return $actionMethod;
    }

}