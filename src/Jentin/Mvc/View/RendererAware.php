<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\View;

/**
 * RendererAware.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface RendererAware
{

    /**
     * @abstract
     * @param RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer);

    /**
     * @abstract
     * @return RendererInterface
     */
    public function getRenderer();

}
