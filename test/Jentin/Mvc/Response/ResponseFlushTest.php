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


    public function testFlushResponseSendsHeader()
    {
        $this->givenIHaveAResponse();
        $this->whenISetHeaderName_withValue('X-My-Custom-Header', 'test');
        $this->whenIAppendTheResponseContentWith('Lorem ipsum!');
        $this->whenIFlushTheResponse();
        $this->thenTheHeadersShouldBeSent();
        $this->thenSendingHeadersIsNotPossible();
        $this->thenTheSentResponseContentShouldBeEquals('Lorem ipsum!');
        $this->thenTheResponseContentShouldBeEquals('');
        $this->thenTheResponseHaveSentTheHeader('X-My-Custom-Header: test');
    }


    public function testFlushResponseDoesNotThrowHeadersAlreadySentException()
    {
        $this->givenIHaveAResponse();
        $this->whenISetHeaderName_withValue('X-My-Custom-Header', 'test');
        $this->whenIAppendTheResponseContentWith("Lorem ipsum!\n");
        $this->whenIFlushTheResponse();
        $this->whenIAppendTheResponseContentWith("Lorem ipsum!\n");
        $this->whenIFlushTheResponse();
        $this->thenTheSentResponseContentShouldBeEquals("Lorem ipsum!\nLorem ipsum!\n");
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
        $this->assertTrue($this->response->headersSent);
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


    /**
     * @param string $header
     */
    private function thenTheResponseHaveSentTheHeader($header)
    {
        $headerSent = $this->response->getHeaderSent();
        $this->assertContains($header, $headerSent);
    }


    private function thenSendingHeadersIsNotPossible()
    {
        $this->assertFalse($this->response->canSendHeaders(false));
    }

}


/**
 * @author  Steffen Zeidler <sigma_z@sigma-scripts.de>
 * @created 01.08.2014
 */
class MockResponseFlush extends Response
{

    /** @var string */
    public $contentSent = '';

    /** @var bool */
    public $headersSent = false;


    /**
     * @param  bool $throwExceptionOnHeadersSent
     * @return bool
     */
    public function canSendHeaders($throwExceptionOnHeadersSent = true)
    {
        return $this->headersSent === false;
    }


    protected function sendHeader($header, $replace = true)
    {
    }


    public function sendContent()
    {
        $this->headersSent = true;
        $this->contentSent .= $this->content;
    }

}
