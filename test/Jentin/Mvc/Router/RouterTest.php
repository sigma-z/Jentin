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

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
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
        $this->request = new Request(array(), $server);
        $this->request->setBaseUrl('/');
    }


    private function whenIRouteTheRequest()
    {
        $this->matchingRoute = $this->router->route($this->request);
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

}
