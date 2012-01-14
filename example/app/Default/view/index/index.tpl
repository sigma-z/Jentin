It's lightweight and object orientend programmed.
It components are loose coupled, introduced by interfaces, clean, maintainable and extensible.
It requires php 5.3 or greater.

<div class="menu">
    <a href="">On GitHub</a>
    <a href="http://www.sigma-scripts.de/Jentin/docs">Documentation (coming soon)</a>
    <br/>
</div>

<h2>Features</h2>
<ul>
    <li>Template rendering with plugins</li>
    <li>Request routing</li>
    <li>
        Plugins (for controller and view renderer)
        <ul>
            <li>RouteUrl - creates url for by given route name and route params</li>
            <li>View - helper for rendering view templates</li>
        </ul>
    </li>
    <li>
        Event handling
        <ul>
            <li>onRoute - dispatched on routing the request</li>
            <li>onController - dispatched creating corresponding controller</li>
            <li>onControllerDispatch - dispatched on controller dispatch</li>
            <li>onControllerResult - dispatched after controller has been dispatched</li>
            <li>onFilterResponse - dispatched before response is ready to sent</li>
            <li>custom events may be defined</li>
        </ul>
    </li>
</ul>
