<?php

namespace Test\Jentin\Mvc\Response;

/**
 * RedirectResponseTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RedirectResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyRedirectUrlException()
    {
        $response = new \Jentin\Mvc\Response\RedirectResponse();
        $response->sendResponse();
    }


    public function testSetRedirectUrl()
    {
        $url = 'http://google.de/';
        $response = new \Jentin\Mvc\Response\RedirectResponse();
        $response->setRedirectUrl($url);

        $this->assertEquals($url, $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

}