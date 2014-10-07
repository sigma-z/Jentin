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

    /**
     * sets file extension
     *
     * @param   string $fileExtension
     * @return  Renderer
     */
    public function setFileExtension($fileExtension);

    /**
     * gets file extension
     *
     * @return  string
     */
    public function getFileExtension();

    /**
     * sets template path
     *
     * @param   string  $path
     * @return  RendererInterface
     */
    public function setTemplatePath($path);


    /**
     * gets template path
     *
     * @return  string
     */
    public function getTemplatePath();

    /**
     * sets template encoding
     *
     * @param   string  $encoding
     * @return  RendererInterface
     */
    public function setEncoding($encoding);

    /**
     * gets template encoding
     *
     * @return string
     */
    public function getEncoding();

    /**
     * sets vars
     *
     * @param   array   $vars
     * @return  RendererInterface
     */
    public function setVars(array $vars);

    /**
     * gets vars
     *
     * @return array
     */
    public function getVars();

    /**
     * renders view template
     *
     * @param   string  $name
     * @param   array   $vars
     * @return  string
     * @throws RendererException
     */
    public function render($name, array $vars = null);

    /**
     * gets template file
     *
     * @param  string $name template name without extension
     * @return string
     */
    public function getFile($name);

    /**
     * gets escaped value of a template variable
     *
     * @param   string  $name
     * @return  mixed
     */
    public function esc($name);

    /**
     * gets the raw value of a template variable
     *
     * @param   string  $name
     * @return  mixed
     */
    public function raw($name);

    /**
     * returns true, if view var is set
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name);

}
