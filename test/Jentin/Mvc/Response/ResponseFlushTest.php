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

/**
 * @author  Steffen Zeidler <sigma_z@sigma-scripts.de>
 * @created 01.08.2014
 */
class ResponseFlushTest extends \PHPUnit_Framework_TestCase
{

    /** @var MockResponseFlush */
    private $response;


    public function testFlushSendsHeader()
    {
        $this->givenIHaveAResponse();
        $this->whenISetHeaderName_withValue('X-My-Custom-Header', 'test');
        $this->whenIAppendTheResponseContentWith('Lorem ipsum!');
        $this->whenIFlushTheResponse();
        $this->thenTheHeadersShouldBeSent();
        $this->thenTheSentResponseContentShouldBeEquals('Lorem ipsum!');
        $this->thenTheResponseContentShouldBeEquals('');
        $this->thenTheResponseHeadersShouldBeEmpty();
    }


    private function givenIHaveAResponse()
    {
        $this->response = new MockResponseFlush();
    }


    /**
     * @param string $headerName
     * @param string $headerValue
     */
    private function whenISetHeaderName_withValue($headerName, $headerValue)
    {
        $this->response->setHeader($headerName, $headerValue);
    }


    /**
     * @param string $content
     */
    private function whenIAppendTheResponseContentWith($content)
    {
        $this->response->appendContent($content);
    }


    private function whenIFlushTheResponse()
    {
        $this->response->flushResponse();
    }


    private function thenTheHeadersShouldBeSent()
    {
        $this->assertTrue($this->response->canSendHeaders(false));
    }


    /**
     * @param string $responseContent
     */
    private function thenTheSentResponseContentShouldBeEquals($responseContent)
    {
        $this->assertEquals($responseContent, $this->response->contentSent);
    }


    /**
     * @param string $responseContent
     */
    private function thenTheResponseContentShouldBeEquals($responseContent)
    {
        $this->assertEquals($responseContent, $this->response->getContent());
    }


    private function thenTheResponseHeadersShouldBeEmpty()
    {
        $this->assertFalse($this->response->hasHeaders());
    }

}


/**
 * @author  Steffen Zeidler <sigma_z@sigma-scripts.de>
 * @created 01.08.2014
 */
class MockResponseFlush extends Response
{

    /** @var array */
    public $headersSent = array();

    /** @var string */
    public $contentSent = '';


    /**
     * @param string $header
     * @param bool   $overwrite
     */
    protected function sendHeader($header, $overwrite = true)
    {
        $this->headersSent[] = $header;
    }


    public function sendResponse()
    {
        $this->sendHeaders(false);
        $this->contentSent .= $this->content;
    }

}
