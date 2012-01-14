<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Response;

/**
 * ResponseInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface ResponseInterface
{

    /**
     * append content
     *
     * @param string $content
     */
    public function appendContent($content);

    /**
     * gets content
     *
     * @return string
     */
    public function getContent();

    /**
     * sets content
     *
     * @param string $content
     */
    public function setContent($content);

}