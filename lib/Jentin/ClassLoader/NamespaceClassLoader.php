<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\ClassLoader;

if (!class_exists('\Jentin\ClassLoader\ClassLoaderInterface', false)) {
    require_once __DIR__ . '/ClassLoaderInterface.php';
}

/**
 * ClassLoader using namespaces
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class NamespaceClassLoader implements ClassLoaderInterface
{

    /**
     * file extension for class filenames
     * @var string
     */
    private $fileExtension = '.php';
    /**
     * class namespaces as hash (namespaces as keys and directories as values)
     * @var array
     */
    private $namespaces = array();
    /**
     * namespace omissions as array
     * @var array
     */
    private $namespaceOmissions = array();
    /**
     * namespace separator
     * @var string
     */
    private $namespaceSeparator = '\\';


    /**
     * constructor
     *
     * @param   array   $namespaces hash (namespaces as keys and directories as values)
     */
    public function __construct(array $namespaces = array())
    {
        $this->setNamespaces($namespaces);
    }


    /**
     * Registers this ClassLoader on the SPL autoload stack.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }


    /**
     * Removes this ClassLoader from the SPL autoload stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }


    /**
     * sets directories for namespaces
     *
     * @param   array  $namespaces
     */
    public function setNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace => $directory) {
            $this->setNamespace($namespace, $directory);
        }
    }


    /**
     * sets directories for a namespace
     *
     * @param   string  $namespace
     * @param   array   $directory
     * @throws  \InvalidArgumentException
     */
    public function setNamespace($namespace, $directory)
    {
        if (!is_readable($directory)) {
            throw new \InvalidArgumentException("Directory: '$directory' does not exist or is not readable!");
        }
        if ($namespace[0] === $this->namespaceSeparator) {
            $namespace = substr($namespace, 1);
        }
        $this->namespaces[$namespace] = $directory;
    }


    /**
     * sets omissions for namespaces
     *
     * @param array $omissions
     */
    public function setNamespaceOmissions(array $omissions)
    {
        foreach ($omissions as &$namespace) {
            if ($namespace[0] === $this->namespaceSeparator) {
                $namespace = substr($namespace, 1);
            }
        }
        $this->namespaceOmissions = $omissions;
    }


    /**
     * set namespace to be suppressed
     *
     * @param string $omittedNamespace
     */
    public function setNamespaceOmission($omittedNamespace)
    {
        if ($omittedNamespace[0] === $this->namespaceSeparator) {
            $omittedNamespace = substr($omittedNamespace, 1);
        }
        if (!in_array($omittedNamespace, $this->namespaceOmissions)) {
            $this->namespaceOmissions[] = $omittedNamespace;
        }
    }


    /**
     * sets file extension for class filenames
     *
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }


    /**
     * gets file extension of class filenames
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }


    /**
     * sets namespace separator
     *
     * @param string
     */
    public function setNamespaceSeparator($separator)
    {
        $this->namespaceSeparator = $separator;
    }


    /**
     * gets namespace separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }


    /**
     * Loads the given class or interface.
     *
     * @param   string  $class
     * @return  boolean TRUE if the class has been successfully loaded, FALSE otherwise.
     */
    public function loadClass($class)
    {
        if (($classFile = $this->getClassFile($class))) {
            /** @noinspection PhpIncludeInspection */
            require $classFile;
            return class_exists($class, false);
        }
        return false;
    }


    /**
     * gets class file for a given class name
     *
     * @param   string $class
     * @return  string class file with path
     */
    private function getClassFile($class)
    {
        if ($this->namespaceSeparator == $class[0]) {
            $class = substr($class, 1);
        }

        $pos = strrpos($class, $this->namespaceSeparator);
        if ($pos === false) {
            return false;
        }

        // namespace of class
        $classNamespace = substr($class, 0, $pos);
        // class base name
        $className = substr($class, $pos + 1);

        foreach ($this->namespaces as $namespace => $directory) {
            if (0 === strpos($classNamespace, $namespace)) {
                $path = $this->getClassPath($classNamespace);
                $file = $directory . DIRECTORY_SEPARATOR . $path
                      . $className . $this->fileExtension;

                if (file_exists($file)) {
                    return $file;
                }
                return false;
            }
        }
        return false;
    }


    /**
     * gets relative path to class by class namespace
     *
     * @param   string  $classNamespace
     * @return  string
     */
    private function getClassPath($classNamespace)
    {
        $path = $classNamespace;
        // if namespace should be omitted, remove it from class namespace
        foreach ($this->namespaceOmissions as $namespaceOmission) {
            if (0 === strpos($classNamespace, $namespaceOmission)) {
                $path = substr($classNamespace, strlen($namespaceOmission) + 1);
                break;
            }
        }

        // replace namespace separator with directory separator
        if (!empty($path)) {
            $path = str_replace($this->namespaceSeparator,
                                DIRECTORY_SEPARATOR,
                                $path);
            $path .= DIRECTORY_SEPARATOR;
        }

        return $path;
    }

}