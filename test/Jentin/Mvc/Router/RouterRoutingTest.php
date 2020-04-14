<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Jentin\Mvc\Router;

use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Route\Route;
use Jentin\Mvc\Router\Router;
use PHPUnit\Framework\TestCase;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouterRoutingTest extends TestCase
{

    /** @var Router */
    private $router;

    /** @var Route */
    private $matchingRoute;

    /** @var Request */
    private $request;


    public function testRoutingARequestWithoutARoute()
    {
        $this->givenIHaveARouter();
        $this->givenIHaveARequestWithUri('/some/action');
        $this->whenIRouteTheRequest();
        $this->thenItShouldHaveRoutedByTheRoute('default');
        $this->thenTheRoutedRequestShouldHaveTheModule_andController_andAction('some', 'action', 'index');
    }


    /**
     * @dataProvider provideRoutingARequest
     * @param string  $requestUri
     * @param boolean $expectedRouteName
     * @param string  $expectedModule
     * @param string  $expectedController
     * @param string  $expectedAction
     */
    public function testRoutingARequest($requestUri, $expectedRouteName, $expectedModule, $expectedController, $expectedAction)
    {
        $this->givenIHaveARouter();
        $this->givenIHaveDefinedTheRoute_withPattern('default', '/%module%(/%controller%)(/%action%)');
        $this->givenIHaveDefinedTheRoute_withPattern('test', '/test(/%module%)');
        $this->givenIHaveARequestWithUri($requestUri);
        $this->whenIRouteTheRequest();
        $this->thenItShouldHaveRoutedByTheRoute($expectedRouteName);
        $this->thenTheRoutedRequestShouldHaveTheModule_andController_andAction($expectedModule, $expectedController, $expectedAction);
    }


    /**
     * @return array[]
     */
    public function provideRoutingARequest()
    {
        return array(
            'test-route' => array(
                'requestUri' => '/test',
                'expectedRouteName' => 'test',
                'expectedModule' => 'default',
                'expectedController' => 'index',
                'expectedAction' => 'index'
            ),
            'default-route' => array(
                'requestUri' => '/some/action',
                'expectedRouteName' => 'default',
                'expectedModule' => 'some',
                'expectedController' => 'action',
                'expectedAction' => 'index'
            ),
            'default-route2' => array(
                'requestUri' => '/some/action?_dc=123654789',
                'expectedRouteName' => 'default',
                'expectedModule' => 'some',
                'expectedController' => 'action',
                'expectedAction' => 'index'
            ),
        );
    }


    public function testCustomPlaceholderToBeMappedAsRequestParameter()
    {
        $this->givenIHaveARouter();
        $this->givenIHaveDefinedTheRoute_withPattern('hello', '/%module%(/%controller%)(/%action%)(/%name%)');
        $this->givenIHaveARequestWithUri('/default/index/index/john-doe');
        $this->whenIRouteTheRequest();
        $this->thenItShouldHaveRoutedByTheRoute('hello');
        $this->thenTheRequestShouldHaveTheParameter_withValue('name', 'john-doe');
        $this->thenTheRequestShouldHaveTheModule('default');
        $this->thenTheRequestShouldHaveTheController('index');
        $this->thenTheRequestShouldHaveTheAction('index');
    }


    public function testCustomPlaceholderWithDefaultRoute()
    {
        $this->givenIHaveARouter();
        $this->givenIHaveARequestWithUri('/default/index/index/john-doe');
        $this->whenIRouteTheRequest();
        $this->thenItShouldHaveRoutedByTheRoute('default');
        $this->thenTheRequestShouldHaveTheModule('default');
        $this->thenTheRequestShouldHaveTheController('index');
        $this->thenTheRequestShouldHaveTheAction('index');
    }


    public function testAddRouteAsStringWillCreateARouteInstance()
    {
        $this->givenIHaveARouter();
        $this->whenIAddRouteForName_asString('hello', '/%module%(/%controller%)(/%action%)(/%name%)');
        $this->thenItShouldHaveARouteInstanceForRoute('hello');
    }


    public function testCustomDefaultRoute()
    {
        $this->givenIHaveARouter();
        $this->givenIHaveDefinedTheRoute_withPattern('default', '/!(/%module%)(/%controller%)(/%action%)');
        $this->givenIHaveARequestWithUri('/!/default/download/index');
        $this->whenIRouteTheRequest();
        $this->thenItShouldHaveRoutedByTheRoute('default');
        $this->thenTheRequestShouldHaveTheModule('default');
        $this->thenTheRequestShouldHaveTheController('download');
        $this->thenTheRequestShouldHaveTheAction('index');
    }


    private function givenIHaveARouter()
    {
        $this->router = new Router();
    }


    /**
     * @param string $routeName
     * @param string $pattern
     */
    private function givenIHaveDefinedTheRoute_withPattern($routeName, $pattern)
    {
        $this->router->setRoute($routeName, new Route($pattern));
    }


    /**
     * @param string $requestUri
     */
    private function givenIHaveARequestWithUri($requestUri)
    {
        $server = array('REQUEST_URI' => $requestUri);
        $this->request = new Request(array(), array(), $server);
        $this->request->setBaseUrl('/');
    }


    private function whenIRouteTheRequest()
    {
        $this->matchingRoute = $this->router->route($this->request);
    }


    /**
     * @param string $name
     * @param string $pattern
     */
    private function whenIAddRouteForName_asString($name, $pattern)
    {
        $this->router->addRoute($name, $pattern);
    }


    /**
     * @param string $expectedRouteName
     */
    private function thenItShouldHaveRoutedByTheRoute($expectedRouteName)
    {
        if ($expectedRouteName) {
            $expectedRoute = $this->router->getRoute($expectedRouteName);
            $this->assertEquals($expectedRoute, $this->matchingRoute);
        }
        else {
            $this->assertNull($this->matchingRoute);
        }
    }


    /**
     * @param string $moduleName
     * @param string $controllerName
     * @param string $actionName
     */
    private function thenTheRoutedRequestShouldHaveTheModule_andController_andAction($moduleName, $controllerName, $actionName)
    {
        $this->assertEquals($moduleName, $this->request->getModuleName());
        $this->assertEquals($controllerName, $this->request->getControllerName());
        $this->assertEquals($actionName, $this->request->getActionName());
    }


    /**
     * @param string $paramName
     * @param string $value
     */
    private function thenTheRequestShouldHaveTheParameter_withValue($paramName, $value)
    {
        $this->assertTrue(
            $this->request->hasParam($paramName),
            "Expected to have a request parameter '$paramName'!"
        );

        $this->assertEquals(
            $value,
            $this->request->getParam($paramName),
            "Expected that the request parameter '$paramName' is equals $value!"
        );
    }


    /**
     * @param string $moduleName
     */
    private function thenTheRequestShouldHaveTheModule($moduleName)
    {
        $this->assertEquals($moduleName, $this->request->getModuleName(), "Expected that module is $moduleName!");
    }


    /**
     * @param string $controllerName
     */
    private function thenTheRequestShouldHaveTheController($controllerName)
    {
        $this->assertEquals($controllerName, $this->request->getControllerName(), "Expected that controller is $controllerName!");
    }


    /**
     * @param string $actionName
     */
    private function thenTheRequestShouldHaveTheAction($actionName)
    {
        $this->assertEquals($actionName, $this->request->getActionName(), "Expected that action is $actionName!");
    }


    private function thenItShouldHaveARouteInstanceForRoute($name)
    {
        $route = $this->router->getRoute($name);
        $this->assertNotNull($route);
        $this->assertInstanceOf('\Jentin\Mvc\Route\Route', $route);
    }
}
