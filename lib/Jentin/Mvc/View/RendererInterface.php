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
 * RendererInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface RendererInterface
{

    public function setFileExtension($fileExtension);

    public function getFileExtension();

    public function setTemplatePath($path);

    public function getTemplatePath();

    public function setEncoding($encoding);

    public function getEncoding();

    public function setVars(array $vars);

    public function getVars();

    public function render($name, array $vars = null);

    public function getFile($name);

}