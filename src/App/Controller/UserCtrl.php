<?php

namespace App\Controller;

class UserCtrl extends BaseCtrl
{
    const DEFAULT_PASSLENGTH = 10;

    // TODO: Make view and edit!

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function add($request, $response, $args) {
        return $this->view->render($response, '@private/user/add.twig');
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function handleAdd($request, $response, $args) {
        $strings = $this->di['strings'];
        $data = $request->getParsedBody();

        // Validate CSRF
        if (!$this->doSimpleValidation($request, true)) {
            return $this->create($request, $response, $data);
        }

        // Validate Field Presence
        $keys = array('username', 'state_text');
        if (!$this->checkForKeys($keys, $request, true)) {
            return $this->create($request, $response, $data);
        }

        $data['username'] = preg_replace('/[^0-9a-zA-Z]/', '', $data['username']);

        $userModel = $this->di['model.user'];
        $user = $userModel->getByUsername($data['username']);

        if ($user) {
            $this->flash->addMessageNow('flash__alert alert-danger', $strings['notices.duplicate_user']);
            return $this->create($request, $response, $data);
        }

        // Save User
        $password = $this->di['utility.string']->generateRandomString(self::DEFAULT_PASSLENGTH);
        $data['passhash'] = $this->di['utility.encryption']->makeHash($password);
        $data['ip'] = '127.0.0.1';
        $user = $userModel->createUser($data);
        if (!$user) {
            $this->flash->addMessageNow('flash__alert alert-danger', $strings['notices.database_error']);
            return $this->create($request, $response, $data);
        }

        $this->flash->addMessageNow('flash__alert alert-success', sprintf($strings['notices.create_successful'], $password));
        return $this->create($request, $response, $data);
    }
}