<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$start = microtime(true);

require __DIR__  . '/../../vendor/autoload.php';

$app = new \Jentin\Application(__DIR__ . '/../app', array('Default'));
$app->run();

if (0 === strpos($app->getResponse()->getHeader('Content-Type'), 'text/html')) {
    echo '<span style="font-size: 10px;">Execution time: ' . number_format(microtime(true) - $start, 4) . 's</span>';
}
