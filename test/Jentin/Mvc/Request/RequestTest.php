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

/**
 * RequestTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideGetBaseUrl
     *
     * @param   array   $server
     * @param   string  $expectedBaseUrl
     */
    public function testGetBaseUrl(array $server, $expectedBaseUrl)
    {
        $request = new Request(array(), array(), $server);
        $this->assertEquals($expectedBaseUrl, $request->getBaseUrl());
    }


    /**
     * @return array[]
     */
    public function provideGetBaseUrl()
    {
        $testData = array();

        // testcase 1: request uri contains script file
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123/test.php'),
            '/test/abc/123/'
        );
        // testcase 2: request uri contains only path with slash at the end
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123/'),
            '/test/abc/123/'
        );
        // testcase 3: request uri contains only path without slash at the end
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123'),
            '/test/abc/'
        );
        // testcase 4: request uri contains script name and query string
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123/info.php?test=123&huhu=on'),
            '/test/abc/123/'
        );
        // testcase 5: request uri contains query string
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123?test=123&huhu=on'),
            '/test/abc/'
        );
        // testcase 6: request uri contains query string and anchor
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123?test=123&huhu=on#test'),
            '/test/abc/'
        );
        // testcase 7: request uri contains anchor
        $testData[] = array(
            array('REQUEST_URI' => '/test/abc/123#test'),
            '/test/abc/'
        );
        // testcase 8: request uri contains base path
        $testData[] = array(
            array(
                'REQUEST_URI' => '/test/abc/123/',
                'SCRIPT_NAME' => '/test/abc/test.php'
            ),
            '/test/abc/'
        );

        return $testData;
    }


    /**
     * @dataProvider provideGetHost
     *
     * @param   array   $server
     * @param   string  $expectedHost
     */
    public function testGetHost(array $server, $expectedHost)
    {
        $request = new Request(array(), array(), $server);
        $this->assertEquals($expectedHost, $request->getHost());
    }


    /**
     * @return array[]
     */
    public function provideGetHost()
    {
        $testData = array();

        // test case 1: host by HTTP_HOST
        $testData[] = array(
            array('HTTP_HOST' => 'localhost'),
            'localhost'
        );
        // test case 2: host by SERVER_NAME
        $testData[] = array(
            array('SERVER_NAME' => 'localhost'),
            'localhost'
        );

        return $testData;
    }


    public function testGetScheme()
    {
        $server = array('HTTPS' => 'on');
        $request = new Request(array(), array(), $server);
        $this->assertEquals('https', $request->getScheme());
    }


    public function testPostParamsPreferredOverGetParams()
    {
        $request = new Request(array('test' => 'post'), array('test' => 'get'));
        $this->assertEquals('post', $request->getParam('test'));
        $this->assertTrue($request->isGet('test'));
        $this->assertTrue($request->isPost('test'));
    }


    /**
     * @dataProvider provideRequestUrls
     *
     * @param string $url
     * @param array  $expected
     */
    public function testGetQuery($url, $expected)
    {
        $server = array('REQUEST_URI' => $url);
        $request = new Request(array(), array(), $server);
        $this->assertEquals($expected['query'], $request->getQuery());
    }


    /**
     * @dataProvider provideRequestUrls
     *
     * @param string $url
     * @param array  $expected
     */
    public function testGetFragment($url, $expected)
    {
        $server = array('REQUEST_URI' => $url);
        $request = new Request(array(), array(), $server);
        $this->assertEquals($expected['fragment'], $request->getFragment());
    }


    /**
     * @return array[]
     */
    public function provideRequestUrls()
    {
        $testCases = array();

        $testCases[] = array(
            '/test/abc/action?hello=world#?_de=123',
            array(
                'query' => 'hello=world',
                'fragment' => '?_de=123'
            )
        );

        $testCases[] = array(
            '/test/abc/action',
            array(
                'query' => '',
                'fragment' => ''
            )
        );

        $testCases[] = array(
            '/test/abc/action#_dc=123',
            array(
                'query' => '',
                'fragment' => '_dc=123'
            )
        );

        $testCases[] = array(
            '/test/abc/action#_dc=123?hello=world',
            array(
                'query' => '',
                'fragment' => '_dc=123?hello=world'
            )
        );

        $testCases[] = array(
            '/test/abc/action?hello=world',
            array(
                'query' => 'hello=world',
                'fragment' => ''
            )
        );

        $testCases[] = array(
            '/test/abc/action?##hello=world',
            array(
                'query' => '',
                'fragment' => '#hello=world'
            )
        );

        $testCases[] = array(
            '/test/abc/action??##hello=world',
            array(
                'query' => '?',
                'fragment' => '#hello=world'
            )
        );

        return $testCases;
    }


    public function testGetServer()
    {
        $serverVars = array(
            'REQUEST_URI' => '/test/abc/123/test.php',
            'HTTPS' => 'on'
        );
        $request = new Request(array(), array(), $serverVars);

        $this->assertEquals($serverVars['REQUEST_URI'], $request->getServer('REQUEST_URI'));
        $this->assertNull($request->getServer('TEST_SOMETHING'));
        $this->assertEquals('Something', $request->getServer('TEST_SOMETHING', 'Something'));
        $this->assertInternalType('array', $request->getServer());
    }

}
