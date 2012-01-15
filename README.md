Jentin MVC Framework
===

It's lightweight and object orientend programmed.
It components are loose coupled, introduced by interfaces, clean, maintainable and extensible.

Requires
---
 * PHP 5.3 or greater
 * Symfony's EventDispatcher (https://github.com/symfony/EventDispatcher)

[On GitHub]: https://github.com/sigma-z/Jentin
[Documentation (coming soon)]: http://www.sigma-scripts.de/Jentin/docs

Features
---
 * Template rendering with plugins
 * Request routing
 * Plugins (for controller and view renderer)
   * RouteUrl - creates url for by given route name and route params
   * View - helper for rendering view templates
 * Event handling
   * onRoute - dispatched on routing the request
   * onController - dispatched creating corresponding controller
   * onControllerDispatch - dispatched on controller dispatch
   * onControllerResult - dispatched after controller has been dispatched
   * onFilterResponse - dispatched before response is ready to sent
   * custom events may be defined