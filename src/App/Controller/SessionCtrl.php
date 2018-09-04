<?php

namespace App\Controller;

class SessionCtrl extends BaseCtrl
{
    const MIN_PASS_LENGTH     = 8;
    const RECOVERY_KEY_LENGTH = 32;
    const RECOVERY_TIMEOUT    = 3600; // 1 Hour
    const KEEP_LOGGED_TIME    = 31536000; // 1 Year

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function login($request, $response, $args) {
        $ip = $this->di['ip'];
        $model = $this->di['model.login_attempt'];
        $loginAttempts = $model->getByIP($ip);
        $remaining = $model::MAXIMUM_LOGIN_ATTEMPTS;
        if ($loginAttempts) {
            $remaining = $remaining - count($loginAttempts);
        }
        // Put captcha here if it's implemented.
        return $this->view->render($response, '@public/login.twig', [
            'attempts' => $remaining,
        ]);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function handleLogin($request, $response, $args) {
        $ip = $this->di['ip'];
        $strings = $this->di['strings'];
        // session_destroy(); // Why destroy the session here?

        $data = $request->getParsedBody();

        // Validate Field Presence
        $keys = array('username', 'password');
        if (!$this->checkForKeys($keys, $request, true)) {
            return $this->login($request, $response. []);
        }

        $data['username'] = preg_replace('/[^a-zA-Z0-9]/', '', $data['username']);

        $model = $this->di['model.login_attempt'];
        $loginAttempts = $model->getByIP($ip);
        if ($loginAttempts && count($loginAttempts) >= $model::MAXIMUM_LOGIN_ATTEMPTS) {
            // Use captcha if captcha is implemented.
            $this->flash->addMessageNow('flash__alert alert-danger disabled', sprintf($strings['notices.login_spam'], $model::BAN_TIME / 3600));
            return $this->login($request, $response, []);
        }

        $userModel = $this->di['model.user'];
        $user = $userModel->getByUsername($data['username']);
        $password = $data['password'];


        // no such user; report failure
        if (!$user) {
            $this->di['utility.encryption']->doesPasswordMatch($password, '$2y$10$lLp0.OXZjvB7a2KmmIQ//u23XgkSC4GNqJ8ztFdKup34h6Di4kFBy', '');
            return $this->logIncorrectLogin($request, $response, $ip, $this->getBrowserId());
        }

        // check if password is correct
        $is_ok = $this->di['utility.encryption']->doesPasswordMatch($password, $user->passhash);

        if (!$is_ok) {
            return $this->logIncorrectLogin($request, $response, $ip, $this->getBrowserId(), $user->id);
        }

        if ($user->state == $userModel::STATE_DISABLED) {
            $this->flash->addMessageNow('flash__alert alert-danger disabled', sprintf($strings['notices.account_disabled'], $user->state_text));
            return $this->login($request, $response, []);
        }

        $keepLogged = isset($data['keeplogged']);

        // if we got this far, then the login is okay
        if ($user->force_reset)
        {
            $recoveryKey = $this->di['utility.string']->generateRandomString(self::RECOVERY_KEY_LENGTH);
            $expiration = time() + self::RECOVERY_TIMEOUT;

            $user->recovery_key = $recoveryKey;
            $user->force_reset = $expiration; // Using force reset for recovery expiration time.
            $user->save();

            $this->flash->addMessageNow('flash__alert alert-danger', $strings['notices.password_reset']);
            return $this->view->render($response, '@public/reset.twig', array(
                'recovery_key' => $recoveryKey,
                'keep_logged' => $keepLogged,
                'min_pass_length' => self::MIN_PASS_LENGTH,
                'username' => $user->username
            ));
        }

        return $this->loginUser($request, $response, $user, $keepLogged);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function logout($request, $response, $args) {
        $user = $this->di['user'];
        $sessionId = $this->di['session.id'];
        // we need the cookie name
        $session_cookie_name = $this->di['config']['cookies.session_cookie_name'];
        $sessionModel = $this->di['model.session'];
        $session = $sessionModel->getByUserIdAndSessionId($user->id, $sessionId);
        if ($session) $session->delete();
        session_destroy();
        $response = \Dflydev\FigCookies\FigResponseCookies::expire($response, $session_cookie_name);

        return $response->withHeader('Location', $this->view_functions->pathFor('session:login'));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function handleResetPassword($request, $response, $args) {
        $data = $request->getParsedBody();
        // Validate Field Presence
        $keys = array('username', 'recovery_key', 'password', 'password2');
        if (!$this->checkForKeys($keys, $request)) {
            return $response->withHeader('Location', $this->di['utility.view']->pathFor('session:login'));
        }

        $userModel = $this->di['model.user'];
        $user = $userModel->getByUsername($data['username']);

        if (!hash_equals($user->recovery_key, $data['recovery_key']) || $user->force_reset < time()) {
            $this->flash->addMessage('flash__alert alert-danger', $strings['notices.token_expired']);
            return $response->withHeader('Location', $this->di['utility.view']->pathFor('session:login'));
        }

        // Validate Rules
        // TODO: Check those on JS to make sure users don't even reach here.
        if ($data['password'] !== $data['password2']) {
            $this->flash->addMessage('flash__alert alert-danger', $strings['notices.password_mismatch']);
            return $response->withHeader('Location', $this->di['utility.view']->pathFor('session:login'));
        }

        if (strlen($data['password']) < self::MIN_PASS_LENGTH) {
            $this->flash->addMessage('flash__alert alert-danger', $strings['notices.password_short']);
            return $response->withHeader('Location', $this->di['utility.view']->pathFor('session:login'));
        }

        $user->passhash = $this->di['utility.encryption']->makeHash($data['password']);
        $user->force_reset = 0;
        $user->recovery_key = '';
        $user->save();
        $keepLogged = isset($data['keep_logged']) ? $data['keep_logged'] : false;
        $response->write(var_dump($user));
        return $this->loginUser($request, $response, $user, $keepLogged);
    }

    private function loginUser($request, $response, $user, $keepLogged)
    {
        $ip = $this->di['ip'];
        $browser_id = $this->getBrowserId();
        // generate a session cookie
        $sessionID = $this->di['utility.string']->generateRandomString();

        $enc = $this->di['utility.encryption'];
        $cookieData = $enc->encrypt($enc->encrypt($sessionID . '|~|' . $user->id));

        $expiry = $keepLogged ? time() + self::KEEP_LOGGED_TIME : 0;

        $isSecure = !($this->di['config']['mode'] == 'development');

        // set the cookie
        $cookie = \Dflydev\FigCookies\SetCookie::create($this->di['config']['cookies.session_cookie_name'])
        ->withValue($cookieData)->withExpires($expiry)->withPath('/')->withDomain('')->withSecure($isSecure)->withHttpOnly(true);
        $response = \Dflydev\FigCookies\FigResponseCookies::set($response, $cookie);

        $_SESSION['temporary'] = !$keepLogged; // if I'm not being remembered, I'm temporary

        $now = time();

        $sessionModel = $this->di['model.session'];
        $s = $sessionModel->createEntity(array(
            'session_id' => $sessionID,
            'user_id' => $user->id,
            'browser' => $browser_id->Browser,
            'device' => $browser_id->OS,
            'ip' => $ip,
            'last_update' => $now,
        ))->save();

        // update last login
        $user->last_access = $now;
        $user->last_login = $now;
        $user->save();

        // clear login attempts
        $model = $this->di['model.login_attempt'];
        $model->deleteByUserId($user->id);

        // regenerate the SID, due to change in privileges
        @session_regenerate_id(true);

        return $response->withHeader('Location', $this->view_functions->pathFor('main:index'));
    }

    private function logIncorrectLogin($request, $response, $ip, $bid, $userID = 0) {
        $model = $this->di['model.login_attempt'];
        $previousAttempts = $model->getByIP($ip);

        $model->createEntity([
            'user_id' => $userID,
            'ip' => $ip,
            'browser' => $bid->Browser,
            'device' => $bid->OS,
        ])->save();

        $this->flash->addMessageNow('flash__alert alert-danger incorrect', $this->di['strings']['notices.login_incorrect']);
        return $this->login($request, $response);
    }
}
