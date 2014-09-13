<?php

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 * Date: 13.09.2014
 */

if (!is_dir('app')) {
    echo "\nGenerating Jentin default app files:\n";

    // creating index controller
    $controllerPath = 'app/Default/controllers';
    $indexControllerFile = $controllerPath . '/IndexController.php';

    if (!file_exists($indexControllerFile)) {
        if (!is_dir($controllerPath)) {
            mkdir($controllerPath, 0750, true);
        }

        $indexController = <<<'EOT'
<?php

namespace DefaultModule;

/**
 * DefaultController
 */
class IndexController extends \Jentin\Mvc\Controller\Controller
{

    public function indexAction()
    {
        $view = $this->plugin('view');
        return $view->render();
    }

}
EOT;
        file_put_contents($indexControllerFile, $indexController);
        echo " `- created file: $indexControllerFile\n";
    }


    // creating index view
    $viewPath = 'app/Default/views/index';
    $viewFile = $viewPath . '/index.phtml';
    if (!file_exists($viewFile)) {
        if (!is_dir($viewPath)) {
            mkdir($viewPath, 0750, true);
        }

        file_put_contents($viewFile, 'Jentin MVC Framework has been installed successfully.');
        echo " `- created file: $viewFile\n";
    }


    // creating front controller
    $frontControllerDir = 'public';
    if (!is_dir($frontControllerDir)) {
        mkdir($frontControllerDir, 0750);
    }

    $frontControllerFile = $frontControllerDir . '/index.php';
    if (!file_exists($frontControllerFile)) {
        $frontController = <<<'EOT'
<?php
require __DIR__  . '/../vendor/autoload.php';

$app = new \Jentin\Application(__DIR__ . '/../app', array('Default'));
$app->run();
EOT;
        file_put_contents($frontControllerFile, $frontController);
        echo " `- created file: $frontControllerFile\n";
    }


    // creating .htaccess file
    $htaccessFile = $frontControllerDir . '/.htaccess';
    if (!file_exists($htaccessFile)) {
        $htaccess = <<<'EOT'
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [L]
RewriteRule ^.*$ index.php [NC]
EOT;
        file_put_contents($htaccessFile, $htaccess);
        echo " `- created file: $htaccessFile\n";
    }

    echo "\nDefault Jentin app has been created successfully!\n";
}
