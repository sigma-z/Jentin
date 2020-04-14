<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Jentin\Mvc;

use Jentin\Mvc\Request\Request;
use Jentin\Mvc\Route\Route;
use Jentin\Mvc\Router\Router;
use PHPUnit\Framework\TestCase;

/**
 * RouteTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouteTest extends TestCase
{

    /** @var Request */
    private $request;

    /** @var Route */
    private $route;

    /** @var bool */
    private $routeParseResult;


    /**
     * @expectedException \Jentin\Mvc\Route\RouteException
     */
    public function testParseException()
    {
        $this->givenIHaveARequestWithUri_andBaseUrl('/abc', '/test');
        $this->givenIHaveARouteWithPattern_andRouteParams('/', array());
        $this->whenIParseTheRoute();
    }


    /**
     * @dataProvider provideParse
     *
     * @param string  $requestUrl
     * @param string  $pattern
     * @param array   $routeParams
     * @param array   $expectedParams
     * @param string  $expectedModule
     * @param string  $expectedController
     * @param string  $expectedAction
     * @param boolean $doesMatch
     */
    public function testParse(
        $requestUrl,
        $pattern,
        array $routeParams,
        array $expectedParams,
        $expectedModule,
        $expectedController,
        $expectedAction,
        $doesMatch
    ) {
        $this->givenIHaveARequestWithUri_andBaseUrl($requestUrl, '/');
        $this->givenIHaveARouteWithPattern_andRouteParams($pattern, $routeParams);
        $this->whenIParseTheRoute();
        $this->thenTheRouteParseResultShouldBeEquals($doesMatch);
        if ($doesMatch) {
            $this->thenTheRequestParamsShouldBeEquals($expectedParams);
            $this->thenTheRequestModuleShouldBeEquals($expectedModule);
            $this->thenTheRequestControllerShouldBeEquals($expectedController);
            $this->thenTheRequestActionShouldBeEquals($expectedAction);
        }
    }


    /**
     * provider method for testParse()
     * @return array
     */
    public function provideParse()
    {
        $request = new Request();
        $defaultModule      = $request->getModuleName();
        $defaultController  = $request->getControllerName();
        $defaultAction      = $request->getActionName();

        $testData = array();

        // tests defaults
        $testData[] = array(
            'requestUrl'            => '/',
            'pattern'               => '/',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // test path matches
        $testData[] = array(
            'requestUrl'            => '/',
            'pattern'               => '/(%module%)(/%controller%)(/%action%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // test path matches
        $testData[] = array(
            'requestUrl'            => '/news',
            'pattern'               => '/news(/%action%)(/%id%)(.%format%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // tests route params are used for the request
        $testData[] = array(
            'requestUrl'            => '/news',
            'pattern'               => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('controller' => 'news123'),
            'expectedParams'        => array(),
            'expectedModule'        => 'news',
            'expectedController'    => 'news123',
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // tests special characters in url
        $testData[] = array(
            'requestUrl'            => '/!/test/index/index',
            'pattern'               => '/!/%module%(/%controller%)(/%action%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => 'test',
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 5 tests route params are used and overwrite url parsed params
        $testData[] = array(
            'requestUrl'            => '/news/test',
            'pattern'               => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('controller' => 'news'),
            'expectedParams'        => array(),
            'expectedModule'        => 'news',
            'expectedController'    => 'news',
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // tests route params are used and overwrites url parsed params
        $testData[] = array(
            'requestUrl'            => '/news/test/index',
            'pattern'               => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array(
                'controller'    => 'news',
                'id'            => '123'
            ),
            'expectedParams'        => array('id' => '123'),
            'expectedModule'        => 'news',
            'expectedController'    => 'news',
            'expectedAction'        => 'index',
            'doesMatch'             => true
        );

        // test that routeParams are stronger than url parsed parameters
        $testData[] = array(
            'requestUrl'            => '/news/test/action/xyz',
            'pattern'               => '/%module%(/%controller%)(/%action%)(/%id%)',
            'routeParams'           => array(
                'controller'    => 'news',
                'id'            => '123'
            ),
            'expectedParams'        => array('id' => '123'),
            'expectedModule'        => 'news',
            'expectedController'    => 'news',
            'expectedAction'        => 'action',
            'doesMatch'             => true
        );

        // test url does not match
        $testData[] = array(
            'requestUrl'            => '/news',
            'pattern'               => '/module(/%controller%)(/%action%)(/%id%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => false
        );

        // test url does not match, because of non optional parameter
        $testData[] = array(
            'requestUrl'            => '/module',
            'pattern'               => '/module/%controller%',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => false
        );

        // test default route without specifying action
        $testData[] = array(
            'requestUrl'            => '/module/controller',
            'pattern'               => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => 'module',
            'expectedController'    => 'controller',
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // test urls with query string
        // NOTE: Query string will not be parsed for request parameters by routing.
        //   To make the test successful, routeParams are equal expectedParams.
        $testData[] = array(
            'requestUrl'            => '/module/controller/index?_dc=123654789',
            'pattern'               => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('_dc' => '123654789'),
            'expectedParams'        => array('_dc' => '123654789'),
            'expectedModule'        => 'module',
            'expectedController'    => 'controller',
            'expectedAction'        => 'index',
            'doesMatch'             => true
        );

        // test urls end with slash
        $testData[] = array(
            'requestUrl'            => '/module/controller/index/',
            'pattern'               => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('_dc' => '123654789'),
            'expectedParams'        => array('_dc' => '123654789'),
            'expectedModule'        => 'module',
            'expectedController'    => 'controller',
            'expectedAction'        => 'index',
            'doesMatch'             => true
        );

        return $testData;
    }


    /**
     * @dataProvider provideGetUrl
     * @param string $pattern
     * @param array  $params
     * @param string $query
     * @param string $asterisk
     * @param string $expected
     */
    public function testGetUrl($pattern, $params, $query, $asterisk, $expected)
    {
        $route = new Route($pattern);
        $actual = $route->getUrl($params, $query, $asterisk);
        $this->assertEquals($expected, $actual);
    }


    public function provideGetUrl()
    {
        $testCases = array();

        $testCases[] = array(
            '(/%module%)(/%controller%)(/%action%)',
            array(),
            '',
            '',
            '/default/index/index'
        );

        $testCases[] = array(
            '(/%module%)(/%controller%)(/%action%)',
            array('controller' => 'test'),
            '',
            '',
            '/default/test/index'
        );

        $testCases[] = array(
            '/module(/%id%)(/%action%)',
            array(),
            '',
            '',
            '/module'
        );

        $testCases[] = array(
            '/module(/%id%)(/%action%)',
            array('id' => '123'),
            '',
            '',
            '/module/123/index'
        );

        $testCases[] = array(
            '/module(/%id%)(/%action%)',
            array('action' => 'action'),
            '',
            '',
            '/module'
        );

        return $testCases;
    }


    public function testRouteWithCallback()
    {
        $this->givenIHaveARequestWithUri_andBaseUrl('/abc/test', '/');
        $this->givenIHaveARouteWithPattern_andRouteParams_andCallback(
            Router::DEFAULT_ROUTE_PATTERN,
            array('id' => '123'),
            function (Request $request) {
                $request->setParam('callbackParam', 'abc');
            }
        );
        $this->whenIParseTheRoute();
        $this->whenICallTheRouteCallback();
        $this->thenTheRequestParamsShouldBeEquals(array('callbackParam' => 'abc', 'id' => '123'));
        $this->thenTheRequestModuleShouldBeEquals('abc');
        $this->thenTheRequestControllerShouldBeEquals('test');
        $this->thenTheRequestActionShouldBeEquals('index');
    }


    /**
     * @param string $requestUrl
     * @param string $baseUrl
     */
    private function givenIHaveARequestWithUri_andBaseUrl($requestUrl, $baseUrl)
    {
        $this->request = new Request(array(), array(), array('REQUEST_URI' => $requestUrl));
        $this->request->setBaseUrl($baseUrl);
    }


    /**
     * @param string $pattern
     * @param array  $routeParams
     */
    private function givenIHaveARouteWithPattern_andRouteParams($pattern, array $routeParams)
    {
        $this->route = new Route($pattern, $routeParams);
    }


    private function whenIParseTheRoute()
    {
        $this->routeParseResult = $this->route->parse($this->request);
    }


    /**
     * @param bool $doesMatch
     */
    private function thenTheRouteParseResultShouldBeEquals($doesMatch)
    {
        $this->assertEquals($doesMatch, $this->routeParseResult);
    }


    private function givenIHaveARouteWithPattern_andRouteParams_andCallback($pattern, $routeParams, $callback)
    {
        $this->route = new Route($pattern, $routeParams, $callback);
    }


    private function whenICallTheRouteCallback()
    {
        $this->route->callback($this->request);
    }


    /**
     * @param array $expectedParams
     */
    private function thenTheRequestParamsShouldBeEquals(array $expectedParams)
    {
        $this->assertEquals($expectedParams, $this->request->getParams(), 'Params mismatches');
    }


    /**
     * @param string $expectedModule
     */
    private function thenTheRequestModuleShouldBeEquals($expectedModule)
    {
        $this->assertEquals($expectedModule, $this->request->getModuleName(), 'Module mismatches');
    }


    /**
     * @param string $expectedController
     */
    private function thenTheRequestControllerShouldBeEquals($expectedController)
    {
        $this->assertEquals($expectedController, $this->request->getControllerName(), 'Controller mismatches');
    }


    /**
     * @param string $expectedAction
     */
    private function thenTheRequestActionShouldBeEquals($expectedAction)
    {
        $this->assertEquals($expectedAction, $this->request->getActionName(), 'Action mismatches');
    }

}
