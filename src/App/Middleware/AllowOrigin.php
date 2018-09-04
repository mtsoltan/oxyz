<?php

namespace App\Middleware;

/**
 * This middleware sets Access-Control-Allow-Origin on responses to allow config.api.referer as origin.
 *
 */
class AllowOrigin
{
    protected $di;

    public function __construct($di)
    {
        $this->di = $di;
    }

    public function __invoke($request, $response, $next)
    {
        // $response = $response->withHeader('Access-Control-Allow-Origin', '*'); // Allow www, ., and other subdomains to use serve.

        return $next($request, $response); // Call the next middleware
    }
}
