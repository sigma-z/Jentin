<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Plugin;

use Jentin\Mvc\Controller\ControllerAware;
use Jentin\Mvc\Controller\ControllerInterface;
use Jentin\Core\Plugin\PluginBrokerInterface;
use \Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\View\RendererInterface;
use Jentin\Mvc\View\Renderer;
use Jentin\Core\Util;

/**
 * ViewRenderer
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class View implements ControllerAware
{

    /**
     * @var \Jentin\Core\Plugin\PluginBrokerInterface
     */
    protected $pluginBroker;
    /**
     * @var \Jentin\Mvc\View\RendererInterface
     */
    protected $renderer;
    /**
     * @var \Jentin\Mvc\Controller\ControllerInterface
     */
    protected $controller;
    /**
     * @var string
     */
    protected $viewDirPattern;
    /**
     * @var string
     */
    protected $layout = 'layout';
    /**
     * @var bool
     */
    protected $layoutEnabled = false;


    /**
     * constructor
     *
     * @param string                                    $viewDirPattern
     * @param \Jentin\Core\Plugin\PluginBrokerInterface $pluginBroker
     * @param bool                                      $layoutEnabled
     */
    public function __construct($viewDirPattern, PluginBrokerInterface $pluginBroker, $layoutEnabled = false)
    {
        $this->viewDirPattern   = $viewDirPattern;
        $this->pluginBroker     = $pluginBroker;
        $this->layoutEnabled    = $layoutEnabled;
    }


    /**
     * sets controller
     *
     * @param  \Jentin\Mvc\Controller\ControllerInterface $controller
     * @return View
     */
    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
        return $this;
    }


    /**
     * gets controller
     *
     * @return \Jentin\Mvc\Controller\ControllerInterface
     */
    public function getController()
    {
        return $this->controller;
    }


    /**
     * sets layout
     *
     * @param  string $layout
     * @return View
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        $this->layoutEnabled = true;
        return $this;
    }


    /**
     * gets layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }


    /**
     * sets layout enabled/disabled by flag
     *
     * @param bool $flag
     * @return View
     */
    public function setLayoutEnabled($flag = true)
    {
        $this->layoutEnabled = $flag;
        return $this;
    }


    /**
     * initializes view
     */
    protected function initRenderer()
    {
        $this->setRenderer(new Renderer($this->pluginBroker));
    }


    /**
     * sets view renderer
     *
     * @param  \Jentin\Mvc\View\RendererInterface $renderer
     * @return \Jentin\Mvc\Plugin\View
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }


    /**
     * gets renderer
     *
     * @return \Jentin\Mvc\View\RendererInterface
     */
    public function getRenderer()
    {
        if (is_null($this->renderer)) {
            $this->initRenderer();
        }
        return $this->renderer;
    }


    /**
     * sets template variable
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->getRenderer()->$name = $value;
    }


    /**
     * gets escaped template variable
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getRenderer()->esc($name);
    }


    /**
     * gets escaped template variable
     * @param  string $name
     * @return mixed
     */
    public function esc($name)
    {
        return $this->getRenderer()->esc($name);
    }


    /**
     * gets raw template variable
     * @param  string $name
     * @return mixed
     */
    public function raw($name)
    {
        return $this->getRenderer()->raw($name);
    }


    /**
     * renders view template
     *
     * @param  array  $vars
     * @param  string $name
     * @return string
     */
    public function render(array $vars = array(), $name = null)
    {
        if ($this->controller) {
            $this->setTemplatePathByRequest($this->controller->getRequest());
            if ($name === null) {
                $name = $this->controller->getRequest()->getActionName();
            }
        }
        $content = $this->getRenderer()->render($name, $vars);

        if ($this->layoutEnabled) {
            $content = $this->renderLayout($content);
        }

        return $content;
    }


    /**
     * renders layout
     *
     * @param   string $content
     * @param   string $layout
     * @return  string
     */
    public function renderLayout($content, $layout = null)
    {
        $renderer = $this->getRenderer();
        // try to set template path, if not set
        if (!$renderer->getTemplatePath() && $this->controller) {
            $this->setTemplatePathByRequest($this->controller->getRequest());
        }
        $layoutTemplate = $this->getLayoutTemplate($renderer->getTemplatePath(), $layout);
        $pathInfo = pathinfo($layoutTemplate);
        $renderer->setTemplatePath($pathInfo['dirname']);
        $vars['content'] = $content;
        $content = $renderer->render($pathInfo['filename'], $vars);
        return $content;
    }


    /**
     * sets template path by request
     *
     * @param  \Jentin\Mvc\Request\RequestInterface $request
     * @param  string $name
     */
    public function setTemplatePathByRequest(RequestInterface $request)
    {
        $params = array(
            'action'        => $request->getActionName(),
            'controller'    => $request->getControllerName(),
            'module'        => $request->getModuleName()
        );
        $templatePath = Util::parsePattern($this->viewDirPattern, $params);
        $this->getRenderer()->setTemplatePath($templatePath);
    }


    /**
     * gets layout template
     *
     * @param  string   $layoutDir
     * @param  string   $layout
     * @return mixed
     * @throws \DomainException
     */
    protected function getLayoutTemplate($layoutDir, $layout = null)
    {
        if (is_null($layout)) {
            $layout = $this->layout;
        }
        if (empty($layoutDir)) {
            $layoutDir = '.';
        }

        $templateExtension = $this->getRenderer()->getFileExtension();
        $templateName = $layout . ($templateExtension ? '.' . $templateExtension : '');
        $layouts = array(
            $layout,
            $layoutDir . '/' . $templateName,
            $layoutDir . '/../' . $templateName
        );

        foreach ($layouts as $layoutTemplate) {
            if (is_file($layoutTemplate) && is_readable($layoutTemplate)) {
                return $layoutTemplate;
            }
        }

        throw new \DomainException('Layout could not be found! (Checked paths ' . implode(', ', $layouts) . ')');
    }

}