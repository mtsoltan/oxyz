<?php
namespace App\Middleware;

class RouteHelper
{
    protected $di;

    public function __construct(&$di) {
        $this->di = &$di;
    }

    public function __invoke($request, $response, callable $next) {
        // add current_route to slim container as present in $request
        // this is required so that we can access route attribute of request outside actual controller
        // as the request in container is initialized with basic environment data before slim is initialized and route is determined
        // and later frozen so it can not be updated anymore.
        // slim goes around this by providing more actual request to controller or middleware at the moment of execution
        // however sometimes we want to know current route outside controller (eg. in utility for twig)
        // and since we can not access request from slim there, we have to make do with this ugly hack
        $currentRoute = array();
        if ($route = $request->getAttribute('route')) {
            $currentRoute['name'] = $route->getName();
            $currentRoute['groups'] = array();
            foreach($route->getGroups() as $group) {
                $currentRoute['groups'][] = $group->getPattern();
            }
            $currentRoute['methods'] = $route->getMethods();
            $currentRoute['arguments'] = $route->getArguments();
        }

        // the reason why we set values manually is that we can not just simply pass \Slim\Route instance
        // as invoke of \Slim\Route requires RequestInterface to be present, while we would provide it with PimpleContainer
        // to go around this we ask slim nicely about all potential values (and values of groups in loop)
        // and set them to an array manually. it is also worth to note that we can not simply use json_encode trick to convert object to array
        $this->di['current_route'] = $currentRoute;
        return $next($request, $response);
    }
}
