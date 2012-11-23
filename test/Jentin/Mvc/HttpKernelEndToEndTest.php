<?php

namespace Test\Jentin\Mvc;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HttpKernelEndToEndTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Jentin\Mvc\HttpKernel
     */
    private $httpKernel;


    protected function setUp()
    {
        $controllerDirs = FIXTURE_DIR . '/modules/%Module%/controllers';
        $viewDirPattern = FIXTURE_DIR . '/modules/%Module%/views';
        $viewPluginBroker = new \Jentin\Core\Plugin\PluginBroker();
        $viewPlugin = array('\Jentin\Mvc\Plugin\View', array($viewDirPattern, $viewPluginBroker));

        $pluginBroker = new \Jentin\Core\Plugin\PluginBroker();
        $pluginBroker->register('view', $viewPlugin);

        $router = new \Jentin\Mvc\Router\Router();
        $router->setRoute('default', new \Jentin\Mvc\Route\Route('/(%module%)(/%controller%)(/%action%)'));

        $htmlJsonControllerResultListener = new \Jentin\Mvc\EventListener\HtmlJsonControllerResultListener();
        $eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
        $eventDispatcher->addListener(
            \Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_RESULT,
            array($htmlJsonControllerResultListener, 'getResponse')
        );

        $this->httpKernel = new \Jentin\Mvc\HttpKernel($controllerDirs, array('Test'), $router, $eventDispatcher);
        $this->httpKernel->setControllerPluginBroker($pluginBroker);
        $this->httpKernel->setControllerClassNamePattern('\%Module%Module\%Controller%Controller');
    }


    /**
     * @dataProvider provideHandleRequest
     *
     * @param \Jentin\Mvc\Request\Request$request
     * @param string $expectedResponseContent
     */
    public function testHandleRequest(\Jentin\Mvc\Request\Request $request, $expectedResponseContent)
    {
        $response = $this->httpKernel->handleRequest($request);
        $this->assertEquals($expectedResponseContent, $response->getContent());
    }


    public function provideHandleRequest()
    {
        $testCases = array();

        $request = new \Jentin\Mvc\Request\Request();
        $request->setBaseUrl('/');
        $request->setRequestUri('/');
        $request->setModuleName('test');
        $request->setControllerName('default');
        $request->setActionName('home');

        $testCases[] = array(
            'request' => $request,
            'expectedResponseContent' => 'It works!'
        );

        $request = new \Jentin\Mvc\Request\Request();
        $request->setBaseUrl('/');
        $request->setRequestUri('/test/default/home?_dc=321456987');
        $testCases[] = array(
            'request' => $request,
            'expectedResponseContent' => 'It works!'
        );

        $request = new \Jentin\Mvc\Request\Request();
        $request->setBaseUrl('/');
        $request->setRequestUri('/test/default/home?_dc=321456987#_dv12321');
        $testCases[] = array(
            'request' => $request,
            'expectedResponseContent' => 'It works!'
        );

        $request = new \Jentin\Mvc\Request\Request();
        $request->setBaseUrl('/');
        $request->setRequestUri('/test/default/home/?_dc=321456987#_dv12321');
        $testCases[] = array(
            'request' => $request,
            'expectedResponseContent' => 'It works!'
        );

        return $testCases;
    }


    public function testRedirect()
    {
        $request = new \Jentin\Mvc\Request\Request();
        $request->setBaseUrl('/');
        $request->setRequestUri('/');
        $request->setModuleName('test');
        $request->setControllerName('default');
        $request->setActionName('redirect');
        /** @var \Jentin\Mvc\Response\RedirectResponse $response */
        $response = $this->httpKernel->handleRequest($request);
        $this->assertInstanceOf('\Jentin\Mvc\Response\RedirectResponse', $response);
        $this->assertEquals('http://example.com/', $response->getRedirectUrl());
    }


    public function testHandleRequestWithLayout()
    {
        $request = new \Jentin\Mvc\Request\Request();
        $request->setBaseUrl('/');
        $request->setRequestUri('/');
        $request->setModuleName('test');
        $request->setControllerName('default');
        $request->setActionName('test-layout');

        $this->httpKernel->getControllerPluginBroker()->load('view')->setLayoutEnabled();

        $response = $this->httpKernel->handleRequest($request);
        $this->assertEquals('Layout works! Content is: Hello world!', $response->getContent());
    }

}
