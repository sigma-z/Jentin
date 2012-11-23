<?php

$libDir = __DIR__ . '/../lib';
/** @noinspection PhpUndefinedConstantInspection */
$extLibDir = __DIR__ . '/../' . VENDOR_DIR;

require $libDir . '/Jentin/ClassLoader/NamespaceClassLoader.php';

$classLoader = new \Jentin\ClassLoader\NamespaceClassLoader();
$classLoader->setNamespace('Jentin', $libDir);
$classLoader->setNamespace('Symfony', $extLibDir);
$classLoader->setNamespace('Test', $libDir);
$classLoader->register();

define('FIXTURE_DIR', __DIR__ . '/fixtures');
