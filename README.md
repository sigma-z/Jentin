Jentin MVC Framework
===
[![Latest Stable Version](https://img.shields.io/packagist/v/sigma-z/jentin.svg?style=flat-square)](https://packagist.org/packages/sigma-z/jentin)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg?style=flat-square)](https://php.net/)
[![CI Status](https://github.com/sigma-z/simtt/workflows/Continuous%20Integration/badge.svg)](https://github.com/sigma-z/jentin/actions)

It's lightweight and object orientated programmed.
It components are loose coupled, introduced by interfaces, clean, maintainable and extensible.

Requires
---
 * PHP 7.1.3 or greater
 * Symfony's EventDispatcher (https://github.com/symfony/EventDispatcher)

[On GitHub]: https://github.com/sigma-z/Jentin
[Documentation (coming soon)]: http://www.sigma-scripts.de/Jentin/docs

Features
---
 * Template rendering with the View plugin
 * Request routing
 * Plugins (for controller and view renderer)
   * RouteUrl - creates url for by given route name and route params
   * View - helper for rendering view templates
 * Event handling
   * onRoute - dispatched on routing the request
   * onRouteCallback - dispatched on routing when route is using a callback
   * onController - dispatched creating corresponding controller
   * onControllerDispatch - dispatched on controller dispatch
   * onFilterResponse - dispatched before response is ready to sent
   * custom events may be defined
