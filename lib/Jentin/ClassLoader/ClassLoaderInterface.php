<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\ClassLoader;

/**
 * class loader interface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface ClassLoaderInterface
{

    /**
     * registers class loader
     */
    function register();

    /**
     * unregisters class loader
     */
    function unregister();

    /**
     * loads class
     * @param   string      $className
     * @return  boolean     true if class has been loaded
     */
    function loadClass($className);

}