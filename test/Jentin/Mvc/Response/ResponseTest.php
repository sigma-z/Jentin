<?php

namespace Test\Jentin\Mvc;

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
        $response->setHeader('My-Test-Header', null);
        $this->assertEquals(null, $response->getHeader('My-Test-Header'));
    }


//    public function testSetContentAsJson()
//    {
//        $data = array(
//            'success'   => true,
//            'recordId'  => 5
//        );
//        $response = new \Jentin\Mvc\Response\Response();
//        $response->setContentAsJson($data);
//        $this->assertEquals('text/x-json', $response->getHeader('Content-Type'));
//
//        $decodedContent = @json_decode($response->getContent(), true);
//        $this->assertEquals($data, $decodedContent);
//    }
//
//
//    public function sendResponse()
//    {
//
//    }

}