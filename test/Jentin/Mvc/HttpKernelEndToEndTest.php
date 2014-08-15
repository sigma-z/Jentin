<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Jentin\Mvc;

use Jentin\Mvc\Plugin\PluginBroker;
use Jentin\Mvc\Event\MvcEvent;
use Jentin\Mvc\EventListener\HtmlJsonControllerResultListener;
use Jentin\Mvc\HttpKernel;
use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\Response;
use Jentin\Mvc\Route\Route;
use Jentin\Mvc\Router\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HttpKernelEndToEndTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var HttpKernel
     */
    private $httpKernel;


    protected function setUp()
    {
        $controllerDirs = FIXTURE_DIR . '/modules/%Module%/controllers';
        $viewDirPattern = FIXTURE_DIR . '/modules/%Module%/views';
        $viewPluginBroker = new PluginBroker();
        $viewPlugin = array('\Jentin\Mvc\Plugin\View', array($viewDirPattern, $viewPluginBroker));

        $pluginBroker = new PluginBroker();
        $pluginBroker->register('view', $viewPlugin);

        $router = new Router();
        $router->setRoute('default', new Route('/(%module%)(/%controller%)(/%action%)'));

        $htmlJsonControllerResultListener = new HtmlJsonControllerResultListener();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            MvcEvent::ON_CONTROLLER_RESULT,
            array($htmlJsonControllerResultListener, 'getResponse')
        );

        $this->httpKernel = new HttpKernel($controllerDirs, array('Test'), $router, $eventDispatcher);
        $this->httpKernel->setControllerPluginBroker($pluginBroker);
        $this->httpKernel->setControllerClassNamePattern('\%Module%Module\%Controller%Controller');
    }


    /**
     * @dataProvider provideHandleRequest
     *
     * @param RequestInterface $request
     * @param string           $expectedResponseContent
     */
    public function testHandleRequest(RequestInterface $request, $expectedResponseContent)
    {
        $response = $this->httpKernel->handleRequest($request);
        $this->assertEquals($expectedResponseContent, $response->getContent());
    }


    /**
     * @return array[]
     */
    public function provideHandleRequest()
    {
        $testCases = array();

        $testCases[] = array(
            'request' => $this->getRequest('/', 'test', 'default', 'home'),
            'expectedResponseContent' => 'It works!'
        );

        $testCases[] = array(
            'request' => $this->getRequest('/test/default/home?_dc=321456987'),
            'expectedResponseContent' => 'It works!'
        );

        $testCases[] = array(
            'request' => $this->getRequest('/test/default/home?_dc=321456987#_dv12321'),
            'expectedResponseContent' => 'It works!'
        );

        return $testCases;
    }


    public function testRedirect()
    {
        $request = $this->getRequest('/', 'test', 'default', 'redirect');
        /** @var \Jentin\Mvc\Response\RedirectResponse $response */
        $response = $this->httpKernel->handleRequest($request);
        $this->assertInstanceOf('\Jentin\Mvc\Response\RedirectResponse', $response);
        $this->assertEquals('http://example.com/', $response->getRedirectUrl());
    }


    public function testHandleRequestWithLayout()
    {
        $request = $this->getRequest('/', 'test', 'default', 'test-layout');

        $this->httpKernel->getControllerPluginBroker()->load('view')->setLayoutEnabled();

        $response = $this->httpKernel->handleRequest($request);
        $this->assertEquals('Layout works! Content is: Hello world!', $response->getContent());
    }


    public function testCallback()
    {
        $responseContent = 'It works with callbacks, too!';
        $request = $this->getRequest('/', 'test', 'default', 'index');

        $callback = function() use($responseContent) {
            return new Response($responseContent);
        };
        $callbackRoute = new Route('/(%module%)(/%controller%)(/%action%)', array(), $callback);
        $this->httpKernel->getRouter()->setRoutes(array('callback' => $callbackRoute));

        $response = $this->httpKernel->handleRequest($request);
        $this->assertEquals($responseContent, $response->getContent());
    }


    /**
     * @param  string $requestUri
     * @param  string $module
     * @param  string $controller
     * @param  string $action
     * @return Request
     */
    private function getRequest($requestUri = '/', $module = '', $controller = '', $action = '')
    {
        $request = new Request();
        $request->setBaseUrl('/');
        $request->setRequestUri($requestUri);
        if ($module)        $request->setModuleName($module);
        if ($controller)    $request->setControllerName($controller);
        if ($action)        $request->setActionName($action);

        return $request;
    }

}
