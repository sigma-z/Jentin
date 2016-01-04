Getting started
===

In general
---

This implementation of the [MVC (Model-View-Controller) pattern](http://martinfowler.com/eaaCatalog/modelViewController.html)
just provides the basis for an MVC application. It does not provide a Model layer.
In fact even the provided View layer is optional.

Jentin requires the [Symfony EventDispatcher](https://github.com/symfony/EventDispatcher).


License
---

Jentin MVC Framework is released under **BSD-3-Clause** license.


Installation
---

Jentin can be installed with [Composer](http://www.getcomposer.org).

If you don't know how Composer works, please check out
their [Getting Started](http://getcomposer.org/doc/00-intro.md) to set up.

Create a ``composer.json`` file with the following requirement:

```json
{
    "require": {
        "sigma-z/jentin": "~1"
    }
}
```

Now call the ``install`` command from your command line:

```bash
composer install
```

If you like you can create the default app structure as an easy start for using Jentin, you can do so by executing this script:

```bash
php vendor/sigma-z/jentin/installDefaultApp.php
```

After installing your file structure looks like:

```
`- app
   `- Default
      `- controllers
         `- IndexController
      `- views
         `- index
            `- index.phtml
`- public
   `- .htaccess
   `- index.php
`- vendor
   `- composer
   `- sigma-z
      `- jentin
   `- symfony
      `- event-dispatcher
   `- autoload.php
`- composer.json
`- composer.lock
```

If you set up your web server that ``public`` is your document root then you can request
``http://your-host/`` in your browser and you should see:

```
Jentin MVC Framework has been installed successfully.
```


Configuration
---

To map your requests to your front controller you usually use for Apache an ``.htaccess`` file.
The document root should be the directory (i.e. public) containing your front controller (ie. index.php) and all the client stuff
like css, javascript and other static files. The document root should be the only directory where web server should have access to.

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

Auto loading the Jentin classes is done by requiring the ``vendor/autoload.php`` file created by Composer.

```php
<?php
require '/path/to/vendor/autoload.php';
```


Front controller
---

Usually the front controller (ie. index.php) is the only php file that should be directly accessible by the web server.
In this file you normally boot your application that handles the request.

In our example there is no application wrapped around Jentin, which you should do for a real application.

```php
<?php
require __DIR__  . '/../vendor/autoload.php';

// creating app
$app = new \Jentin\Application(__DIR__ . '/../app', array('Default', /* and more */));
$app->run();
```

Routing a request
===

You can define routes to map requests to their corresponding controllers.

This the default route pattern ``(/%module%)(/%controller%)(/%action%)(/*)``, which is used as fallback,
if no other route has matched the request url.

When a request is routed the placeholders (marked with % as delimiter, here module, controller, and action) are mapped as request parameters.
The brackets mark that a placeholder is optional. A request defines a ``module``, a ``controller``, and an ``action``.

Imagine a request is sent with the url ``http://your-host/``. Request object when matching the default route,
will then have the following properties:

- module: default
- controller: index
- action: index

The urls ``http://your-host/`` and ``http://your-host/default/index/index`` will lead to the same result.

The router routes the request by finding a route that matches the request url. The first route that matches will be used for the request
by parsing the placeholders into request parameters.

Let's see an example:

```php
$app = new \Jentin\Application(__DIR__ . '/../app', array('Default', /* and more */));
$app->addRoute('test', new Route('/test/%module%/%action%'));
$app->addRoute('test2', '/test(/%module%)'); // internally it will create a Route instance
```

The url ``http://your-host/test/admin/list-records`` will match route 'test'.

The url ``http://your-host/test/test-module`` will match the route 'test2', because ``/%action%`` is required for the route 'test'.


Route with parameters
---

You can define a route with parameters that will be mapped as request parameters, if the route pattern matches.

```php
$route = new Route('(/%module%)(/%controller%)(/%action%)', array('sid' => '123session-key'));
```

Here the request will have access to the parameter 'sid' defined by the route.


Route with a callback
---

A route can have a callback that is called by the ``HttpKernel``, if the route pattern matches.
It this case the callback is the action method, that means you do not have to create a controller class.

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


Default controller directory structure
---

The front controller script ``public/index.php`` should be the only php file that can be accessed from your web server.

The ``app`` directory contains the modules as directories. Each module has ``controllers`` and ``views`` as subdirectories.
Each controller has its own view directory ``views/<controller name>``. And each action of the controller can have a view script.

```
`- app
   `- Default
      `- controllers
         `- IndexController
      `- views
         `- index
            `- index.phtml
`- public
   `- index.php
```


Customized controller directory structure
---

Of course the directory structure can be customized in the front controller script public/index.php.

The controller path pattern uses placeholders:

- ``%Module%`` camel cased name, like Default, AppStore
- ``%module%`` lower cased name dash separated, like default, app-store
- ``%Controller%`` camel cased name, like Index, AppStore
- ``%controller%`` lower cased name dash separated, like index, app-store


For example:

```php
$app = new \Jentin\Application($appPath, $modules);
$app->setControllerPathPattern($appPath . '/%Module%');
$app->setViewPathPattern($appPath . '/%Module%/views/%controller%');
$app->run();
```

Directory structure:

```
`- app
   `- default
      `- IndexController
      `- views
         `- index
            `- index.phtml
`- public
   `- index.php
```


Controller class naming
---

The default pattern for naming controller classes is ``\%Module%Module\%Controller%Controller``.

The same placeholder rules as above applies to the controller class name pattern.
As you can see you can customize the controller name as you wish to fit your needs.

In the example a custom namespace is added:

```php
$app = new \Jentin\Application($appPath, $modules);
$app->setControllerClassNamePattern('\MyWorld\%Module%Module\%Controller%Controller');
$app->run();
```


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

The url http://your-host/de/hello-world/json will output '{"message": "hello world"}".

The url http://your-host/default/hello-world/html will output 'hello world'.


If you like to have it much more easier, then you can return a string for html responses and an array for json responses.
Jentin will automatically convert the result to a response instance. If you want to map your response results on your own, then
take a look at the event ``ON_FILTER_RESPONSE``.

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
        return new RedirectResponse('http://your-host/');
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

Jentin supports plugins for controllers and views. A plugin is class or a class name, that will be instantiated when it is used.
The plugin class does not have to implement a certain interface or extend a special class.

To make the plugin available in your controller, you must register the plugin.

```php
$myPlugin = new MyPlugin();
$controllerPluginBroker = new PluginBroker();
$controllerPluginBroker->register('myPlugin', $myPlugin);

$httpKernel = new HttpKernel(/* http kernel arguments */);
$httpKernel->setControllerPluginBroker($controllerPluginBroker);
```

This is how you can use your plugin in the controller class.

```php
class SomeController extends Controller
{
    public function someAction()
    {
        return $this->plugin('myPlugin')->createResponse();
    }
}
```


Views
---

**Note:** This plugin is enabled by default.

The view feature itself is a plugin for the controller. But it also can be extended with plugins.
The view plugin broker is an argument for the view class, which a plugin for the controller.

```php
$myViewPlugin = new MyViewPlugin();
$viewPluginBroker = new PluginBroker();
$viewPluginBroker->register('myViewPlugin', $myViewPlugin);

$view = new \Jentin\Mvc\Plugin\View($viewPathPattern, $viewPluginBroker, $layoutEnabled);
$controllerPluginBroker = new PluginBroker();
$controllerPluginBroker->register('view', $view);

$httpKernel = new HttpKernel(/* http kernel arguments */);
$httpKernel->setControllerPluginBroker($controllerPluginBroker);
```

This is how you disable the view plugin:

```php
$app = new \Jentin\Application(/* arguments */);
$app->disableViewPlugin();
$app->run();
```


View variables
...

Assigning variables to the view object:

```php
$view->homeUrl = $homeUrl;
$view->welcomeTitle = $welcomeTitle;
```

Accessing the variable in the view script:

```html
<h1><?php echo $this->welcomeTitle ?></h1>

<a href="<?php echo $this->raw('homeUrl') ?>">Back to home</a>
```

``$this->welcomeTitle`` will escape the value for use in HTML.
You can also write in a more explicit way ``$this->esc('welcomeTitle')`` which does the same.

If you do not want a value to be escaped, then you can use ``$this->raw('homeUrl')`` to get the raw value.


You can also define your own escaping:

```php
$view->getRenderer()->setEscapeCallback(function ($value, Jentin\Mvc\View\Renderer $renderer) {
    // your code
});
```


View layouts
...

Enabling layouts.

```
$app = new \Jentin\Application($appPath, $modules);
$app->enableLayoutView();   // enabling layouts
$app->run();
```

When a view is rendered, it looks for a ``layout.phtml`` file.

First it checks if this file is located in the same directory like the view that has been rendered.
If there is no layout file, it looks into the parent directory.

```
`- app
   `- default
      `- IndexController
      `- views
         `- index
            `- index.phtml
            `- layout.phtml     // controller layout
         `- layout.phtml        // module layout
```


RouteUrl
---

**Note:** This plugin is enabled by default.

The RouteUrl plugin is useful to build urls for a specific route and request parameters.

```php
class SomeController extends Controller
{
    public function someAction()
    {
        return $this->plugin('route')->url('hello', array(
            'module' => 'default',
            'controller' => 'index',
            'action' => 'index',
            'name' => 'John'
        ));
    }
```

If the route 'hello' looks like '/%module%/%controller%/%action%(/%name%)' for example,
it will create the url '/default/index/index/John' as the result.

This plugin is available in controller classes and views, too.


Caching
---

There is no caching implementation in Jentin. But you can use the event dispatcher to implement your own cache easily.
You also should take a look at [Varnish](https://www.varnish-cache.org/).
