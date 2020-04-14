<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Jentin\Mvc\Response;

use Jentin\Mvc\Response\Response;
use PHPUnit\Framework\TestCase;

/**
 * ResponseTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class ResponseTest extends TestCase
{

    /** @var Response */
    private $response;


    public function testSetHeader()
    {
        $this->givenIHaveResponse();
        $this->whenISetAHeader_withValue('My-Test-Header', 'hello world');
        $this->thenItShouldHaveTheHeader_withValue('My-Test-Header', 'hello world');

        $this->whenIUnsetTheHeader('My-Test-Header');
        $this->thenItShouldNotHaveTheHeader('My-Test-Header');
    }


    public function testSetHeaderWithReplaceOption()
    {
        $this->givenIHaveResponse();
        $this->whenISetAReplacingHeader_withValue('My-Test-Header', 'hello world');
        $this->whenISetAReplacingHeader_withValue('My-Test-Header', 'hello my world');
        $this->thenItShouldHaveTheHeader_withValue('My-Test-Header', 'hello my world');
    }


    public function testSetHeaderWithoutReplaceOption()
    {
        $this->givenIHaveResponse();
        $this->whenISetANonReplacingHeader_withValue('My-Test-Header', 'hello world');
        $this->whenISetANonReplacingHeader_withValue('My-Test-Header', 'hello my world');

        $headerValue = array('hello world', 'hello my world');
        $this->thenItShouldHaveTheHeader_withValue('My-Test-Header', $headerValue);
    }


    public function testSetContentType()
    {
        $this->givenIHaveResponse();
        $this->whenISetTheContentType('text/json;');
        $this->whenISetTheContentType('text/x-json; charset=UTF-8');
        $this->thenItShouldHaveTheHeader_withValue('Content-Type', 'text/x-json; charset=UTF-8');
    }


    private function givenIHaveResponse()
    {
        $this->response = new Response();
    }


    /**
     * @param string $headerName
     * @param string $headerValue
     */
    private function whenISetAHeader_withValue($headerName, $headerValue)
    {
        $this->response->setHeader($headerName, $headerValue);
    }


    /**
     * @param string $headerName
     * @param string $headerValue
     */
    private function thenItShouldHaveTheHeader_withValue($headerName, $headerValue)
    {
        $this->assertEquals($headerValue, $this->response->getHeader($headerName));
    }


    /**
     * @param string $headerName
     */
    private function whenIUnsetTheHeader($headerName)
    {
        $this->response->unsetHeader($headerName);
    }


    /**
     * @param string $headerName
     */
    private function thenItShouldNotHaveTheHeader($headerName)
    {
        $this->assertEquals(null, $this->response->getHeader($headerName));
    }


    /**
     * @param string $contentType
     */
    private function whenISetTheContentType($contentType)
    {
        $this->response->setContentType($contentType);
    }


    /**
     * @param string $headerName
     * @param string $headerValue
     */
    private function whenISetANonReplacingHeader_withValue($headerName, $headerValue)
    {
        $this->response->setHeader($headerName, $headerValue, false);
    }


    /**
     * @param string $headerName
     * @param string $headerValue
     */
    private function whenISetAReplacingHeader_withValue($headerName, $headerValue)
    {
        $this->response->setHeader($headerName, $headerValue, true);
    }

}
