<?php

namespace Test\Jentin\Core;

use Jentin\Core\Util;

/**
 * UtilTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideGetCamelcased
     * @param string $string
     * @param string $expected
     */
    public function testGetCamelcased($string, $expected)
    {
        $actual = Util::getCamelcased($string);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @return array[]
     */
    public function provideGetCamelcased()
    {
        $testData = array();
        $testData[] = array('',             '');
        $testData[] = array('test',         'Test');
        $testData[] = array('t-e-s-t',      'TEST');
        $testData[] = array('t-ES-t',       'TEsT');
        $testData[] = array('tESt',         'Test');
        $testData[] = array('te-##-st',     'TeSt');
        $testData[] = array('te-test.-/st', 'TeTestSt');
        $testData[] = array('te+1234+st',   'Te1234St');
        $testData[] = array('te+12.34+st',  'Te1234St');
        $testData[] = array('te+12_34+st',  'Te12_34St');

        return $testData;
    }


    /**
     * @dataProvider getParsePattern
     * @param           $pattern
     * @param   array   $params
     * @param   string  $delimiter
     * @param   boolean $camelCased
     * @param   string  $expected
     */
    public function testParsePattern($pattern, $params, $delimiter, $camelCased, $expected)
    {
        $actual = Util::parsePattern($pattern, $params, $delimiter, $camelCased);
        $this->assertEquals($expected, $actual);
    }


    public function getParsePattern()
    {
        $testData = array();
        $testData[] = array(
            '',
            array(),
            '%',
            true,
            ''
        );
        $testData[] = array(
            '%test% hello %world%',
            array(),
            '',
            true,
            '%test% hello %world%'
        );
        $testData[] = array(
            '%%',
            array(),
            '%',
            true,
            '%%'
        );
        $testData[] = array(
            'Hello $name$!',
            array('name' => 'User'),
            '$',
            true,
            'Hello User!'
        );
        $testData[] = array(
            'greeting: %greeting%',
            array('greeting' => 'hello user'),
            '%',
            true,
            'greeting: hello user'
        );
        $testData[] = array(
            'greeting: %Greeting%',
            array('greeting' => 'hello-user'),
            '%',
            true,
            'greeting: HelloUser'
        );
        $testData[] = array(
            'greeting: %Greeting%',
            array('greeting' => 'hello-user'),
            '%',
            false,
            'greeting: %Greeting%'
        );
        return $testData;
    }

}