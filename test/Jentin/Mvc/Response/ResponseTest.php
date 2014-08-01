<?php

namespace Test\Jentin\Mvc\Response;

use Jentin\Mvc\Response\Response;

/**
 * ResponseTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testSetHeader()
    {
        $response = new Response();
        $response->setHeader('My-Test-Header', 'hello world');
        $this->assertEquals('hello world', $response->getHeader('My-Test-Header'));
        $response->unsetHeader('My-Test-Header', null);
        $this->assertEquals(null, $response->getHeader('My-Test-Header'));
    }

}
