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

You can define routes to map requests to their corresponding controllers.

The default route uses the pattern: ``(/%module%)(/%controller%)(/%action%)``

When a request is routed the placeholders (marked with % as delimiter, here module, controller, and action) are mapped as request parameters.
The brackets marks that a placeholder is optional. A request has the parameters ``module``, ``controller``, and ``action``
defined by default, even if none of your defined routes will match.

Imagine a request is sent to the url ``http://your-domain/`` the route will match. Request object will then have the following parameters:

- module: default
- controller: index
- action: index

Tthe urls ``http://your-domain/`` and ``http://your-domain/default/index/index`` will lead to the same result.

The router routes the request by finding a matching route. The first route that matches the pattern will be used for the request.

Let's see an example:

```php
$router = new Router();
$router->setRoute('test', new Route('/test/%module%/%action%'));
$router->setRoute('test2', new Route('/test(/%module%)'));
```

The url ``http://your-domain/test/admin/list-records`` will match route 'test'.

The url ``http://your-domain/test/test-module`` will match the route 'test2', because ``/%action%`` is required for the route 'test'.


Route with parameters
---

You can define a route with parameters that will be mapped as request parameters, if the route pattern matches.

```php
$route = new Route('(/%module%)(/%controller%)(/%action%)', array('sid' => '123session-key'));
```

Here the request will have access to the parameter 'sid' defined by the route.


Route with a callback
---

A route can have a callback that is called by the ``HttpKernel``, if the route pattern matches. It this case the callback is the action method,
that means you do not have to create a controller class.

```php
$route = new Route('(/%module%)(/%controller%)(/%action%)', array(), function() {
    return new Response('hello world!');
});
```

This routes will create a response with 'hello world!' as content.


Controllers and responses
===

A controller is a piece of code, often a class, that is called to create a response. It processes the request
and uses models and views to create the response.


HTML and JSON responses
---

Controller that creates a simple 'hello world' response as json and html:

```php
namespace DefaultModule;

use Jentin\Mvc\Controller\Controller;
use Jentin\Mvc\Response\Response;
use Jentin\Mvc\Response\JsonResponse;

class HelloWorldController extends Controller
{
    public function jsonAction()
    {
        return new JsonResponse(array('message' => 'hello world'));
    }

    public function htmlAction()
    {
        return new Response('hello world');
    }
}

```

The url http://your-domain/default/hello-world/json will output '{"message": "hello world"}".

The url http://your-domain/default/hello-world/html will output 'hello world'.


If you like to have it much more easier, then try out the ``AutoConvertResponseIntoHtmlOrJsonListener`` class.
Just let the listener listen to the event ``\Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_RESULT``.
It will create the response automatically by converting an array to a
json response and all the other to an html response.

```php
$htmlJsonControllerResultListener = new \Jentin\Mvc\EventListener\AutoConvertResponseIntoHtmlOrJsonListener();
$eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_RESULT,
    array($htmlJsonControllerResultListener, 'getResponse')
);
```

This enables you to simplify your controllers with the same result.

```php
use Jentin\Mvc\Controller\Controller;

class HelloWorldController extends Controller
{
    public function jsonAction()
    {
        return array('message' => 'hello world');
    }

    public function htmlAction()
    {
        return 'hello world';
    }
}
```

Redirect responses
---

```php
use Jentin\Mvc\Controller\Controller;
use Jentin\Mvc\Response\RedirectResponse;

class SomeController extends Controller
{
    public function redirectAction()
    {
        return new RedirectResponse('http://your-domain/');
    }
}
```


Flush responses
---

A conventional response sends its data after the whole response data has been collected to the client.
Flush responses are used to send there output immediately.

```php
use Jentin\Mvc\Controller\Controller;
use Jentin\Mvc\Response\RedirectResponse;

class SomeController extends Controller
{
    public function redirectAction()
    {
        $response = new Response();
        $response->setContent('hello world');
        $response->flushResponse();
        $response->setContent('hello world a second time');
        $response->flushResponse();
        return $response;
    }
}
```

The method ``flushResponse()`` sends its buffered output immediately.

**Note:** that you normally can not set headers to your response once you flushed the response.


Events to hook in
===

ON_ROUTE event
---

This event is called before the request is processed by the router to find the corresponding route.
Useful if you want to manipulate the request before routing.
You can create a response, add headers or content to it and it will available in the controller class or the route callback.

The ON_ROUTE event will be dispatched by providing a RouteEvent instance as argument.
The event itself provides access to the request and the response.
In this example a JsonResponse will be created which will be used for further processing in the HttpKernel class.

```php
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_ROUTE,
    function (\Jentin\Mvc\Event\RouteEvent $event) {
        $response = new JsonResponse();
        $event->setResponse($response);
    }
);
```


ON_ROUTE_CALLBACK event
---

This event is called when a route callback is called.

The ON_ROUTE_CALLBACK event will be dispatched by providing a RouteCallbackEvent instance as argument.
The event itself provides access to the request, the route, and the response.

```php
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_ROUTE_CALLBACK,
    function (\Jentin\Mvc\Event\RouteCallbackEvent $event) {
        // .. code ..
    }
);
```


ON_CONTROLLER event
---

This event is called before the controller will be dispatched.

The ON_CONTROLLER event will be dispatched by providing a ControllerEvent instance as argument.
The event itself provides access to the controller, the request (via controller), and the response (via controller).

```php
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER,
    function (\Jentin\Mvc\Event\ControllerEvent $event) {
        // .. code ..
    }
);
```


ON_CONTROLLER_DISPATCH event
---

This event is called when the controller is dispatching. It is right before the controller action method is called.
The preDispatch() method of the controller has been called already at this time.

The ON_CONTROLLER event will be dispatched by providing a ControllerEvent instance as argument.
The event itself provides access to the controller, the request (via controller), and the response (via controller).

```php
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_CONTROLLER_DISPATCH,
    function (\Jentin\Mvc\Event\ControllerEvent $event) {
        // .. code ..
    }
);
```


ON_FILTER_RESPONSE event
---

This event is called after the dispatch process is done.
It can be used to manipulate the response that has been built by the controller class.

The ON_FILTER_RESPONSE event will be dispatched by providing a ResponseFilterEvent instance as argument.
The event itself provides access to the request and the response.

In the example the controller result can be a string or array instead of an instance of ResponseInterface.
The event listener converts the data to a JsonResponse if it's an array, otherwise it creates a html response.

**Note**: This is an example, to show how to use the `ON_FILTER_RESPONSE` event.
By default the HttpKernel already does the conversion described above.

```php
$eventDispatcher->addListener(
    \Jentin\Mvc\Event\MvcEvent::ON_FILTER_RESPONSE,
    function (\Jentin\Mvc\Event\ResponseFilterEvent $event) {
        $responseContent = $event->getResponse();
        if ($responseContent instanceof ResponseInterface) {
            return $responseContent;
        }

        if (is_array($responseContent)) {
            $response = new JsonResponse();
            $response->setContent($responseContent);
        }
        else {
            $response = new Response();
            $response->setContentType('text/html; charset=utf-8');
            $response->setContent((string)$responseContent);
        }
        $event->setResponse($response);
        return $response;
    }
);
```


Plugins
===


Views
---

RouteUrl
---

Caching
---

