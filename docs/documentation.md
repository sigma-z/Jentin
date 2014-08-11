Getting started
===


In general
---

This implementation of the [MVC (Model-View-Controller) pattern](http://martinfowler.com/eaaCatalog/modelViewController.html)
just provides the basis for an MVC application. It does not provide a Model layer. 
In fact even the provided View layer is optional. 


Installation
---

Jentin can be installed with [Composer](http://www.getcomposer.org).

Add the following requirement in your ``composer.json`` file:

```js
{
    "require": {
        "sigma-z/jentin": "*"
    }
}
```

Call ``composer install`` from your command line to add ``Jentin`` to your ``vendor`` folder.
If you don't know how Composer works, please check out
their [Getting Started](http://getcomposer.org/doc/00-intro.md) to set up.

Jentin requires the [Symfony EventDispatcher](https://github.com/symfony/EventDispatcher).


Configuration
---

To map your requests to your front controller you usually use for Apache a ``.htaccess`` file. 
The document root should be a folder containing your front controller (ie. index.php) and all the client stuff
like css, javascript and other static files. The document root should be the only folder web server should have access to.  

```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [L]
RewriteRule ^.*$ index.php [NC]
```

Class loading
---

Most times you be satisfied by loading the ``vendor/autoload.php`` created by Composer. 

But you can also use the class loader provided by Jentin ``\Jentin\ClassLoader\NamespaceClassLoader`` or any other sufficient class loader.

```php
<?php
require '/path/to/Jentin/ClassLoader/NamespaceClassLoader.php';

// class loader
$classLoader = new \Jentin\ClassLoader\NamespaceClassLoader();

// define where to load the Jentin classes from
$classLoader->setNamespace('Jentin', '/path/to/Jentin');    

// define where to load the Symfony EventDispatcher classes from
$classLoader->setNamespace('Symfony', '/path/to/Symfony');
  
// register class loader for autoloading 
$classLoader->register();

```

Front controller
---

Usually the front controller (ie. index.php) is the only php file that should be directly accessible by the web server.
In this file you normally boot your application that handles the request.

In our example there is no application wrapped around Jentin, which you should do for a real application.
 
```php
<?php
require __DIR__  . '/../../vendor/autoload.php';

// creating the http kernel
$controllerDirPattern = __DIR__ . '/../app/%Module%/controllers';
$modules = array('Default');
$router = new \Jentin\Mvc\Router\Router();
$eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$httpKernel = new \Jentin\Mvc\HttpKernel($controllerDirPattern, $modules, $router, $eventDispatcher);

// handling the request
$request = new \Jentin\Mvc\Request\Request();
$response = $httpKernel->handleRequest($request);

// sending the response
$response->sendResponse();
```

Routing a request
===



Creating and sending a response
=== 

Plugins
===


Views
---

RouteUrl
---

Caching
---

