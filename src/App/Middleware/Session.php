<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Rsp;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;

/**
 * This middleware handles restoring of login sessions.
 *
 * $di['user'], $di['ip'] and $di['session.id'] may get chagned by this middleware.
 *
 */
class Session
{
    protected $cookie_name;
    protected $di;
    protected $useDatabaseSessions = false;

    protected static $sessionOverrideRouteAllowList = array();

    protected static $sessionOverrideRouteGroupAllowList = array();

    public function __construct(&$di) // $this->di must be set at the very end because SessionHandler may modify $di
    {
        if ($this->useDatabaseSessions) {
            $handler = new \App\Utilities\SessionHandler($di);
            $di['handler'] = $handler;
            session_set_save_handler($handler, true);
        }
        session_name('transient');
        $isSecure = !($di['config']['mode'] == 'development');
        session_set_cookie_params(0, '/', '', $isSecure, true);
        session_cache_limiter('');
        session_save_path($di['config']['cookies.session_save_path']);
        session_start();
        if (isset($handler) && strlen(session_id()) != $handler::SESSION_ID_LENGTH) {
            session_regenerate_id(true);
        }

        $this->di = $di;
        $this->cookie_name = $di['config']['cookies.session_cookie_name'];

        \Tracy\Debugger::dispatch();
    }

    public function __invoke(Req $request, Rsp $response, callable $next) {
        // some of routes should be excluded from checking session and always assume anonymous user
        // we can do that in middleware because we told slim to determineRouteBeforeAppMiddleware
        $route = $request->getAttribute('route');
        if ($route)
        {
            $name = $route->getName();
            $group = $route->getGroups();

            if (in_array($name, self::$sessionOverrideRouteAllowList)) {
                return $next($request, $response); // call the next middleware
            }

            // check allowed groups
            if (array_key_exists(0, $group) && $group[0] instanceof \Slim\RouteGroup) {
                foreach(self::$sessionOverrideRouteGroupAllowList as $pattern) {
                    if ($group[0]->getPattern() === $pattern) {
                        return $next($request, $response); // call the next middleware
                    }
                }
            }
        }

        $crypto = $this->di['utility.encryption'];
        // is the login cookie even set?
        $cookie = FigRequestCookies::get($request, $this->cookie_name);

        $decryptedCookie = $crypto->decrypt($cookie->getValue());
        if ($decryptedCookie) {
            $decryptedCookie = $crypto->decrypt($decryptedCookie);
            // who the hell came up with this anyway
        }

        // no cookie is set; continue down the middleware stack
        if (!$decryptedCookie) {
            return $next($request, $response);
        }

        $c = explode('|~|', $decryptedCookie);

        if (count($c) != 2) {
            return $next($request, $response);
        }

        list ($sessionId, $uid) = $c;
        if (!$sessionId || !$uid) {
            return $this->logout($request, $response); // do not call the next middleware
        }

        $uid = intval($uid);

        // check if we think this session exists
        $sessionModel = $this->di['model.session'];
        $session = $sessionModel->getByUserIdAndSessionId($uid, $sessionId);

        if (!$session) { // nope
            return $this->logout($request, $response); // do not call the next middleware
        }

        $this->di['session.id'] = $sessionId;
        $userModel = $this->di['model.user'];
        $user = $userModel->getByUserId($uid);
        if ($user) {
            $user->session_id = $sessionId; // no need to save, just set
            if ($user->state == $userModel::STATE_ENABLED) {
                $this->di['user'] = $user;
            } else {
                return $this->di['view']->render($response, '@public/error/disabled.twig', ['username' => $user->Username])->withStatus(403);
            }
        }

        $response = $next($request, $response);
        return $response;
    }

    private function logout(Req $request, Rsp $response) {
        //throw new \App\Exception\AccessDenied('SESSION->LOGOUT GETS CALLED');
        // we don't use the standard "logout" method here
        // because that implies that a session actually exists
        // which in this case, it doesn't (or is invalid)
        $response = $response->withStatus(307)->withHeader('Location', $this->di['utility.view']->pathFor('session:login'));
        $response = FigResponseCookies::expire($response, $this->cookie_name);
        session_destroy();
        return $response;
    }
}
