<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Jentin\Mvc\Response;

use Jentin\Mvc\Response\RedirectResponse;

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
        $response = new RedirectResponse();
        $response->sendResponse();
    }


    public function testSetRedirectUrl()
    {
        $url = 'http://google.de/';
        $response = new RedirectResponse();
        $response->setRedirectUrl($url);

        $this->assertEquals($url, $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

}
