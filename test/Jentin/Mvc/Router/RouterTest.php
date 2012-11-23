<?php

namespace Test\Jentin\Mvc\Router;

use Jentin\Mvc\Request\Request;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Jentin\Mvc\Router\Router
     */
    private $router;


    protected function setUp()
    {
        $this->router = new \Jentin\Mvc\Router\Router();
        $this->router->setRoute('default', new \Jentin\Mvc\Route\Route('/%module%(/%controller%)(/%action%)'));
        $this->router->setRoute('test', new \Jentin\Mvc\Route\Route('/test(/%module%)'));
    }


    /**
     * @dataProvider provideRoute
     * @param \Jentin\Mvc\Request\Request $request
     * @param   boolean $expectedMatch
     * @param   string  $expectedModule
     * @param   string  $expectedController
     * @param   string  $expectedAction
     */
    public function testRoute(Request $request, $expectedMatch, $expectedModule, $expectedController, $expectedAction)
    {
        $actualMatch = $this->router->route($request);
        $this->assertEquals($expectedMatch, $actualMatch);
        $this->assertEquals($expectedModule, $request->getModuleName());
        $this->assertEquals($expectedController, $request->getControllerName());
        $this->assertEquals($expectedAction, $request->getActionName());
    }


    public function provideRoute()
    {
        $testData = array();

        $server = array('REQUEST_URI' => '/test');
        $request = new \Jentin\Mvc\Request\Request(array(), $server);
        $request->setBaseUrl('/');
        $testData[] = array($request, true, 'default', 'index', 'index');

        $server = array('REQUEST_URI' => '/some/action');
        $request = new \Jentin\Mvc\Request\Request(array(), $server);
        $request->setBaseUrl('/');
        $testData[] = array($request, true, 'some', 'action', 'index');

        $server = array('REQUEST_URI' => '/some/action');
        $request = new \Jentin\Mvc\Request\Request(array(), $server);
        $request->setBaseUrl('');
        $testData[] = array($request, false, 'default', 'index', 'index');

        $server = array('REQUEST_URI' => '/some/action?_dc=123654789');
        $request = new \Jentin\Mvc\Request\Request(array(), $server);
        $request->setBaseUrl('/');
        $testData[] = array($request, true, 'some', 'action', 'index');

        return $testData;
    }

}
