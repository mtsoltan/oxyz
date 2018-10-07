<?php

namespace App\Controller;

abstract class BaseCtrl
{
    /** @var \Slim\Container */
    protected $di;

    /**
     * @var \Slim\Views\Twig
     */
    protected $view;

    /**
     * @var \Slim\Flash\Messages
     */
    protected $flash;

    /**
     * @var \App\Entity\Entity
     */
    protected $user;

    /**
     * @var \App\Utilities\View
     */
    protected $view_functions;

    /**
     * @var \Slim\Http\Environment
     */
    protected $environment;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $event;

    public function setDependencies($di) {
        $this->flash = $di['flash'];
        $this->view_functions = $di['utility.view'];
        $this->environment = $di['environment'];
        $this->view = $di['view'];
    }

    public function __construct($di) {
        $this->di = $di;

        $this->setDependencies($di);
    }

    /**
     * @param $request
     * @return bool Valid
     *
     * Perform POST or GET validation, but only for CSRF, no fields
     */
    protected function doSimpleValidation($request, $immediate = false) {
        if ($request->isGet()) { // Handle GET query params
            $customServer = $_SERVER;
            $params = $request->getQueryParams();
            $customServer['REQUEST_URI'] = strtok($customServer['REQUEST_URI'], '?'); // Strip query params
            $csrf = new \ParagonIE\AntiCSRF\AntiCSRF($params, $_SESSION, $customServer);
        } else { // POST
            $csrf = new \ParagonIE\AntiCSRF\AntiCSRF();
        }
        $result = $csrf->validateRequest();
        if (!$result) {
            if ($immediate)
                $this->flash->addMessageNow('flash__alert alert-danger', $this->di['strings']['notices.invalid_csrf_token']);
            else
                $this->flash->addMessage('flash__alert alert-danger', $this->di['strings']['notices.invalid_csrf_token']);
        }

        if ($result) {
            //
        }

        return $result;
    }

    /**
     * @param array $keys
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param boolean $immediate
     */
    protected function checkForKeys($keys, $request, $immediate = false) {
        if ($request->isGet()) {
            $data = $request->getQueryParams();
        } else {
            $data = $request->getParsedBody();
        }
        foreach ($keys as $key) {
            $exists = array_key_exists($key, $data) && isset($data[$key]);
            $notEmptyString = $exists && is_string($data[$key]) && strlen($data[$key]);
            $notEmptyArray = $exists && is_array($data[$key]) && count($data[$key]);
            $valid = $notEmptyString || $notEmptyArray;
            if (!$valid) {
                if ($immediate) {
                    $this->flash->addMessageNow('flash__alert alert-danger', $this->di['strings.forms']['required.all']);
                } else {
                    $this->flash->addMessage('flash__alert alert-danger', $this->di['strings.forms']['required.all']);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $args The arguments object passed to the handler by the router.
     * @throws \App\Exception\NoSuchXException
     * @return \App\Entity\User
     */
    protected function getUserFromArgs($args) {
        $userModel = $this->di['model.user'];
        $user = $userModel->getById($args['id']);
        if (!$user) throw new \App\Exception\NoSuchXException('User');
        return $user;
    }

    /**
     * stdClass with OS and Browser keys.
     * @return \stdClass
     */
    protected function getBrowserId() {
        return \App\Utilities\BrowserID::identify($this->di['environment']['HTTP_USER_AGENT']);
    }
}
