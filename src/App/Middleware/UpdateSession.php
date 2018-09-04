<?php

namespace App\Middleware;

use App\Utilities\BrowserID;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Rsp;

/**
 * This middleware updates the user session (LastAccess, LastUpdate etc.)
 *
 * The middleware depends on user being set.
 *
 * $di['user'] may be changed by this middleware.
 *
 */
class UpdateSession
{
    const UPDATE_INTERVAL = '- 15 minutes'; // period of 3 minutes

    private $di;

    public function __construct(&$di) {
        $this->di = $di;
    }

    public function __invoke(Req $request, Rsp $response, callable $next) {
        $user = $this->di['user'];

        if (!$user) {
            return $next($request, $response);
        }

        $timeUpdateIntervalAgo = new \DateTime(self::UPDATE_INTERVAL);
        $timeUpdateIntervalAgo = $timeUpdateIntervalAgo->getTimestamp();
        if ($user->last_access < $timeUpdateIntervalAgo)
        {
            $user->last_access = time();
            $user->save();
            $this->di['user'] = $user;

            self::updateSession($this->di);
        }

        return $next($request, $response);
    }

    // this is static helper function. when needed it can be executed outside this middleware to force update session
    // example of such usage is UpdateIp middleware. this function does not update LastAccess of user entity itself since
    // its whole point is to keep session up to date, not user entity.
    public static function updateSession($di) {
        $browserId = BrowserID::identify($di['environment']['HTTP_USER_AGENT']);

        $sessionModel = $di['model.session'];
        $session = $sessionModel->getByUserIdAndSessionId($di['user']->id, $di['user']->session_id);
        $session->last_update = time();
        $session->browser = $browserId->Browser;
        $session->device = $browserId->OS;
        $session->ip = $di['user']->ip; // we assume its always up to date when executing

        $session->save();
    }
}
