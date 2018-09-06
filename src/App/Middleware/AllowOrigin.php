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

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param callable $next Next middleware to be called.
     * @return callable
     */
    public function __invoke($request, $response, $next)
    {
        $origin = implode($request->getHeader('Origin'));
        $response = $response->withHeader('Vary', 'Origin');
        if (preg_match('%'.$this->di['config']['site.origin'].'%', $origin)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        } else {
            $response = $response->withHeader('Access-Control-Allow-Origin', $this->di['config']['site.site_url']);
        }

        return $next($request, $response); // Call the next middleware
    }
}
