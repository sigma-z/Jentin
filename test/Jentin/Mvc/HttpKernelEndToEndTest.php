<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Jentin\Mvc;

use Jentin\Mvc\Event\RouteEvent;
use Jentin\Mvc\Plugin\PluginBroker;
use Jentin\Mvc\Event\MvcEvent;
use Jentin\Mvc\EventListener\AutoConvertResponseIntoHtmlOrJsonListener;
use Jentin\Mvc\HttpKernel;
use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\RedirectResponse;
use Jentin\Mvc\Response\Response;
use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Route\Route;
use Jentin\Mvc\Router\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HttpKernelEndToEndTest extends \PHPUnit_Framework_TestCase
{

    /** @var HttpKernel */
    private $httpKernel;

    /** @var ResponseInterface */
    private $response;


    /**
     * @dataProvider provideHandleRequest
     *
     * @param RequestInterface $request
     * @param string           $expectedResponseContent
     */
    public function testHandleRequest(RequestInterface $request, $expectedResponseContent)
    {
        $this->givenIHaveAHttpKernel();
        $this->whenIHandleTheRequest($request);
        $this->thenTheResponseContentShouldBeEquals($expectedResponseContent);
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

        $this->givenIHaveAHttpKernel();
        $this->whenIHandleTheRequest($request);
        $this->thenTheResponseShouldBeARedirectTo('http://example.com/');
    }


    public function testHandleRequestWithLayout()
    {
        $request = $this->getRequest('/', 'test', 'default', 'test-layout');

        $this->givenIHaveAHttpKernel();
        $this->givenIHaveLayoutEnabled();
        $this->whenIHandleTheRequest($request);
        $this->thenTheResponseContentShouldBeEquals('Layout works! Content is: Hello world!');
    }


    public function testCallback()
    {
        $request = $this->getRequest('/', 'test', 'default', 'index');
        $responseContent = 'It works with callbacks, too!';
        $callback = function() use($responseContent) {
            return new Response($responseContent);
        };

        $this->givenIHaveAHttpKernel();
        $this->givenIDefinedARouteWithCallback('callback', '/(%module%)(/%controller%)(/%action%)', $callback);
        $this->whenIHandleTheRequest($request);
        $this->thenTheResponseContentShouldBeEquals($responseContent);
    }


    public function testSetResponseByOnRouteEvent()
    {
        $request = $this->getRequest('/', 'test', 'default', 'no-return-response');

        $this->givenIHaveAHttpKernel();
        $this->givenIHaveDefinedAnOnRouteEvent(function (RouteEvent $routeEvent) {
            $response = new Response();
            $response->setHeader('X-On-Route-Event', '1');
            $routeEvent->setResponse($response);
        });
        $this->whenIHandleTheRequest($request);
        $this->thenTheResponseContentShouldBeEquals('It works!');
        $this->thenTheResponseShouldHaveAHeaderWithName_andValue('X-On-Route-Event', '1');
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


    private function givenIHaveAHttpKernel()
    {
        $controllerDirs = FIXTURE_DIR . '/modules/%Module%/controllers';
        $viewDirPattern = FIXTURE_DIR . '/modules/%Module%/views';
        $viewPluginBroker = new PluginBroker();
        $viewPlugin = array('\Jentin\Mvc\Plugin\View', array($viewDirPattern, $viewPluginBroker));

        $pluginBroker = new PluginBroker();
        $pluginBroker->register('view', $viewPlugin);

        $htmlJsonControllerResultListener = new AutoConvertResponseIntoHtmlOrJsonListener();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            MvcEvent::ON_FILTER_RESPONSE,
            array($htmlJsonControllerResultListener, 'getResponse')
        );

        $this->httpKernel = new HttpKernel($controllerDirs, array('Test'), new Router(), $eventDispatcher);
        $this->httpKernel->setControllerPluginBroker($pluginBroker);
        $this->httpKernel->setControllerClassNamePattern('\%Module%Module\%Controller%Controller');
    }


    /**
     * @param RequestInterface $request
     */
    private function whenIHandleTheRequest(RequestInterface $request)
    {
        $this->response = $this->httpKernel->handleRequest($request);
    }


    /**
     * @param $expectedResponseContent
     */
    private function thenTheResponseContentShouldBeEquals($expectedResponseContent)
    {
        $this->assertEquals($expectedResponseContent, $this->response->getContent());
    }


    /**
     * @param string $redirectUrl
     */
    private function thenTheResponseShouldBeARedirectTo($redirectUrl)
    {
        /** @var RedirectResponse $response */
        $response = $this->response;
        $this->assertInstanceOf('\Jentin\Mvc\Response\RedirectResponse', $response);
        $this->assertEquals($redirectUrl, $response->getRedirectUrl());
    }


    private function givenIHaveLayoutEnabled()
    {
        $this->httpKernel->getControllerPluginBroker()->load('view')->setLayoutEnabled();
    }


    /**
     * @param string   $name
     * @param string   $pattern
     * @param callable $callback
     */
    private function givenIDefinedARouteWithCallback($name, $pattern, $callback)
    {
        $callbackRoute = new Route($pattern, array(), $callback);
        $this->httpKernel->getRouter()->setRoute($name, $callbackRoute);
    }


    /**
     * @param callable $listener
     */
    private function givenIHaveDefinedAnOnRouteEvent($listener)
    {
        $this->httpKernel->getEventDispatcher()->addListener(MvcEvent::ON_ROUTE, $listener);
    }


    /**
     * @param string $headerName
     * @param string $headerValue
     */
    private function thenTheResponseShouldHaveAHeaderWithName_andValue($headerName, $headerValue)
    {
        $actualValue = $this->response->getHeader($headerName);
        $this->assertEquals($headerValue, $actualValue);
    }
}
