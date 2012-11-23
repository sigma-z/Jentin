<?php

namespace Test\Jentin\Mvc;

/**
 * RouteTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Jentin\Mvc\Route\RouteException
     */
    public function testParseException()
    {
        // request object
        $server = array('REQUEST_URI' => '/abc');
        $request = new \Jentin\Mvc\Request\Request(array(), $server);
        $request->setBaseUrl('/test');

        // route
        $route = new \Jentin\Mvc\Route\Route('/');
        // parse route
        $route->parse($request);
    }


    /**
     * @dataProvider provideParse
     *
     * @param   string  $requestUrl
     * @param   string  $pattern
     * @param   array   $routeParams
     * @param   array   $expectedParams
     * @param   string  $expectedModule
     * @param   string  $expectedController
     * @param   string  $expectedAction
     * @param   boolean $doesMatch
     */
    public function testParse(
            $requestUrl,
            $pattern,
            array $routeParams,
            array $expectedParams,
            $expectedModule,
            $expectedController,
            $expectedAction,
            $doesMatch)
    {
        // request object
        $server= array('REQUEST_URI' => $requestUrl);
        $request = new \Jentin\Mvc\Request\Request(array(), $server);
        $request->setBaseUrl('/');

        // route
        $route = new \Jentin\Mvc\Route\Route($pattern, $routeParams);
        // parse route
        $actual = $route->parse($request);

        // check, if route did match
        $this->assertEquals($doesMatch, $actual);
        if ($doesMatch) {
            // check params in request object
            $this->assertEquals($expectedParams, $request->getParams(), 'Params mismatches');
            $this->assertEquals($expectedModule, $request->getModuleName(), 'Module mismatches');
            $this->assertEquals($expectedController, $request->getControllerName(), 'Controller mismatches');
            $this->assertEquals($expectedAction, $request->getActionName(), 'Action mismatches');
        }
    }


    /**
     * provider method for testParse()
     * @return array
     */
    public function provideParse()
    {
        $request = new \Jentin\Mvc\Request\Request();
        $defaultModule      = $request->getModuleName();
        $defaultController  = $request->getControllerName();
        $defaultAction      = $request->getActionName();

        $testData = array();

        // 0 tests defaults
        $testData[] = array(
            'requestUrl'            => '/',
            'pattern'              => '/',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 1 test path matches
        $testData[] = array(
            'requestUrl'            => '/',
            'pattern'              => '/(%module%)(/%controller%)(/%action%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 2 test path matches
        $testData[] = array(
            'requestUrl'            => '/news',
            'pattern'              => '/news(/%action%)(/%id%)(.%format%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 3 tests route params are used for the request
        $testData[] = array(
            'requestUrl'            => '/news',
            'pattern'              => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('controller' => 'news123'),
            'expectedParams'        => array(),
            'expectedModule'        => 'news',
            'expectedController'    => 'news123',
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 4 tests special characters in url
        $testData[] = array(
            'requestUrl'            => '/!/test/index/index',
            'pattern'              => '/!/%module%(/%controller%)(/%action%)',
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
            'pattern'              => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('controller' => 'news'),
            'expectedParams'        => array(),
            'expectedModule'        => 'news',
            'expectedController'    => 'news',
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 6 tests route params are used and overwrites url parsed params
        $testData[] = array(
            'requestUrl'            => '/news/test/index',
            'pattern'              => '/%module%(/%controller%)(/%action%)',
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

        // 7 test that routeParams are stronger than url parsed parameters
        $testData[] = array(
            'requestUrl'            => '/news/test/action/xyz',
            'pattern'              => '/%module%(/%controller%)(/%action%)(/%id%)',
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

        // 8 test url does not match
        $testData[] = array(
            'requestUrl'            => '/news',
            'pattern'              => '/module(/%controller%)(/%action%)(/%id%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => false
        );

        // 9 test url does not match, because of non optional parameter
        $testData[] = array(
            'requestUrl'            => '/module',
            'pattern'              => '/module/%controller%',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => $defaultModule,
            'expectedController'    => $defaultController,
            'expectedAction'        => $defaultAction,
            'doesMatch'             => false
        );

        // 10 test default route without specifying action
        $testData[] = array(
            'requestUrl'            => '/module/controller',
            'pattern'              => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array(),
            'expectedParams'        => array(),
            'expectedModule'        => 'module',
            'expectedController'    => 'controller',
            'expectedAction'        => $defaultAction,
            'doesMatch'             => true
        );

        // 11 test urls with query string
        // NOTE: Query string will not be parsed for request parameters by routing.
        //   To make the test successful, routeParams are equal expectedParams.
        $testData[] = array(
            'requestUrl'            => '/module/controller/index?_dc=123654789',
            'pattern'              => '/%module%(/%controller%)(/%action%)',
            'routeParams'           => array('_dc' => '123654789'),
            'expectedParams'        => array('_dc' => '123654789'),
            'expectedModule'        => 'module',
            'expectedController'    => 'controller',
            'expectedAction'        => 'index',
            'doesMatch'             => true
        );

        // 12 test urls end with slash
        $testData[] = array(
            'requestUrl'            => '/module/controller/index/',
            'pattern'              => '/%module%(/%controller%)(/%action%)',
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
     * @param string $expected
     */
    public function testGetUrl($pattern, $params, $expected)
    {
        $route = new \Jentin\Mvc\Route\Route($pattern);
        $actual = $route->getUrl($params);
        $this->assertEquals($expected, $actual);
    }


    public function provideGetUrl()
    {
        $testData = array();

        $testData[] = array(
            '(/%module%)(/%controller%)(/%action%)',
            array(),
            ''
        );

        $testData[] = array(
            '/module(/%id%)(/%action%)',
            array(),
            '/module'
        );

        $testData[] = array(
            '/module(/%id%)(/%action%)',
            array('id' => '123'),
            '/module/123'
        );

        $testData[] = array(
            '/module(/%id%)(/%action%)',
            array('action' => 'action'),
            '/module'
        );

        return $testData;
    }

}