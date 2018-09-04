<?php

namespace App\Utilities;

use \ParagonIE\AntiCSRF\AntiCSRF;

class View
{
    private $di;

    public function __construct(\Slim\Container $di) {
        $this->di = $di;
    }

    public function cssUrl($file) {
        $min = sprintf('/static/css/%s.min.css', $file);
        if (file_exists(PUBLIC_ROOT . $min)) {
            return sprintf('%s?t=%s', $min, filemtime(PUBLIC_ROOT . $min));
        }
        $max = sprintf('/static/css/%s.css', $file);
        if (file_exists(PUBLIC_ROOT . $max)) {
            return sprintf('%s?t=%s', $max, filemtime(PUBLIC_ROOT . $max));
        }
        return '';
    }

    public function jsUrl($file) {
        $min = sprintf('/static/js/%s.min.js', $file);
        if (file_exists(PUBLIC_ROOT . $min)) {
            return sprintf('%s?t=%s', $min, filemtime(PUBLIC_ROOT . $min));
        }
        $max = sprintf('/static/js/%s.js', $file);
        if (file_exists(PUBLIC_ROOT . $max)) {
            return sprintf('%s?t=%s', $max, filemtime(PUBLIC_ROOT . $max));
        }
        return '';
    }

    public function imgUrl($file) {
        $file = sprintf('/static/images/%s', $file);
        return sprintf('%s?t=%s', $file, filemtime(PUBLIC_ROOT . $file));
    }

    public function pathFor($name, $data = [], $queryParams = []) {
        return $this->di['router']->pathFor($name, $data, $queryParams);
    }

    public function baseUrl() {
        $uri = $this->di['request']->getUri();
        $uri = $uri->withUserInfo('');

        $scheme = $uri->getScheme();
        $authority = $uri->getAuthority();

        return "$scheme://$authority";
    }

    public function currentUrl() {
        return $this->baseUrl() . $this->di['request']->getUri()->getPath();
    }

    public function currentPath() {
        return $this->di['request']->getUri()->getPath();
    }

    public function currentRoute() {
        return $this->di['current_route']['name'];
    }

    public function config($key) {
        return $this->di['config'][$key];
    }

    public function string($key, ...$vars) {
        $matches = [];
        if (preg_match('%(strings::[^.]+)\.(.+)%', $key, $matches)) {
            return sprintf($this->di[$matches[1]][$matches[2]]);
        }
        return sprintf($this->di['strings'][$key], ...$vars);
    }

    public function getDi() {
        return $this->di;
    }

    /**
     * @param null|int $userId
     * @return \App\Entity\User
     */
    public function getUser($userId = null) {
        if (!$userId) return $this->di['user'];
        else return $this->di['model.user']->getById($userId);
    }

    /**
     * @param int $fileId
     * @return \App\Entity\File
     */
    public function getFile($fileId) {
        return $this->di['model.file']->getById($fileId);
    }

    public function getFlashMessages() {
        return $this->di['flash']->getMessages();
    }

    public function csrfTokenInput($lock_to = null) {
        static $csrf;
        if ($csrf === null) {
            $csrf = new AntiCSRF;
        }
        return $csrf->insertToken($lock_to, false);
    }

    public function csrfTokenArray($lock_to = null) {
        static $csrf;
        if ($csrf === null) {
            $csrf = new AntiCSRF;
        }
        return $csrf->getTokenArray($lock_to);
    }

    public function getMd5($val) {
        return md5($val);
    }

    public function getActiveSessionCount() {
        return count($this->di['model.session']->getRecentByUserId($this->getUser()->id));
    }
}
