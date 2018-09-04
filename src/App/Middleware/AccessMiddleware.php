<?php
namespace App\Middleware;

/**
 * Middleware used to control access to routes.
 *
 * $di['user'] might get changed when calling ensureLoggedInOrTorrentPass
 *
 */
class AccessMiddleware
{
  private $di;
  private $functionToCall;

  public function __construct(&$di)
  {
    $this->di = $di;
    $this->functionToCall = null;
  }

  public function __invoke($request, $response, $next)
  {
    if (!is_callable($this->functionToCall)) {
      return $next($request, $response);
    }
    else {
      $function = $this->functionToCall;
      return $function($request, $response, $next);
    }
  }

  private function addCallable(callable $callable)
  {
    $this->functionToCall = $callable;
  }

  /**
   * This middleware function ensures that the user is logged in.
   * If the user is not logged in, she is redirected to the login page.
   *
   * @return AccessMiddleware
   */
  public function ensureLoggedIn()
  {
    $clone = clone $this;

    $clone->addCallable(function($request, $response, $next) {
      $user = $this->di['user'];

      // the user is logged in; continue down the stuack
      if ($user) {
        return $next($request, $response);
      }

      // the user is not logged in

      // redirect to login page
      $loginUrl = $this->di['utility.view']->pathFor('session:login');
      return $response->withHeader('Location', $loginUrl)->withStatus(303);
    });

    return $clone;
  }

  /**
   * This middleware function ensures that the user is logged out.
   * If the user is logged in, she is shown 404 not found error.
   *
   * @throws \App\Exception\NotFound
   *
   * @return AccessMiddleware
   */
  public function ensureLoggedOut()
  {
    $clone = clone $this;

    $clone->addCallable(function($request, $response, $next) {
      $user = $this->di['user'];

      // the user is logged out; continue down the stuack
      if (is_null($user)) {
        return $next($request, $response);
      }

      // user is logged in, redirect to index page
      $loginUrl = $this->di['utility.view']->pathFor('file:index');
    });

    return $clone;
  }

  /**
   * This middleware function ensures that the user has the required permission.
   *
   * @throws \App\Exception\AccessDenied
   *
   * @return AccessMiddleware
   */
  public function requirePermission($permission)
  {
    $clone = clone $this;

    $clone->addCallable(function($request, $response, $next) use ($permission, $minLevel) {
      $user = $this->di['user'];

      if ($user && $user->hasPermission($permission)) {
        // user has the permission, go ahead
        return $next($request, $response);
      }

      // throw access denied exception, to be handled by the appropriate error handler
      throw new \App\Exception\AccessDenied();
    });

    return $clone;
  }
}
