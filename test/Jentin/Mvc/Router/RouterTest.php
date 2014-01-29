<?php

namespace Test\Jentin\Mvc\Router;

use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Router\Router;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Router
     */
    private $router;


    protected function setUp()
    {
        $this->router = new Router();
        $this->router->setRoute('default', new \Jentin\Mvc\Route\Route('/%module%(/%controller%)(/%action%)'));
        $this->router->setRoute('test', new \Jentin\Mvc\Route\Route('/test(/%module%)'));
    }


    /**
     * @dataProvider provideRoute
     * @param Request $request
     * @param boolean $expectedRouteName
     * @param string  $expectedModule
     * @param string  $expectedController
     * @param string  $expectedAction
     */
    public function testRoute(Request $request, $expectedRouteName, $expectedModule, $expectedController, $expectedAction)
    {
        $actualRoute = $this->router->route($request);
        if ($expectedRouteName) {
            $expectedRoute = $this->router->getRoute($expectedRouteName);
            $this->assertEquals($expectedRoute, $actualRoute);
        }
        else {
            $this->assertNull($actualRoute);
        }
        $this->assertEquals($expectedModule, $request->getModuleName());
        $this->assertEquals($expectedController, $request->getControllerName());
        $this->assertEquals($expectedAction, $request->getActionName());
    }


    public function provideRoute()
    {
        $testData = array();

        $server = array('REQUEST_URI' => '/test');
        $request = new Request(array(), $server);
        $request->setBaseUrl('/');
        $testData[] = array($request, 'test', 'default', 'index', 'index');

        $server = array('REQUEST_URI' => '/some/action');
        $request = new Request(array(), $server);
        $request->setBaseUrl('/');
        $testData[] = array($request, 'default', 'some', 'action', 'index');

        $server = array('REQUEST_URI' => '/some/action');
        $request = new Request(array(), $server);
        $request->setBaseUrl('');
        $testData[] = array($request, null, 'default', 'index', 'index');

        $server = array('REQUEST_URI' => '/some/action?_dc=123654789');
        $request = new Request(array(), $server);
        $request->setBaseUrl('/');
        $testData[] = array($request, 'default', 'some', 'action', 'index');

        return $testData;
    }

}
