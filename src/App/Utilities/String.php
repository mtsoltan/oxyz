<?php

namespace App\Utilities;

class String
{
    private $store;
    private $storeKey = 'lang';
    private $defaultLanguage = 'en';
    private $defaultLanguageFile = 'strings';
    private $di;

    public function __construct($di, &$store = null) {
        $this->di = $di;
        if (is_null($store)) {
            $store = $_SESSION;
        }
        $this->store = $store;
    }

    public function generateRandomString($len=32) {
        $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $this->randomize($charset, $len);
    }

    public function randomize($OK, $len) {
        $token = '';
        $max = mb_strlen($OK, '8bit') - 1;
        for ($i = 0; $i < $len; $i++) {
            $token .= $OK[random_int(0, $max)];
        }

        return str_shuffle($token);
    }

    public function changeLanguage($newLang) {
        if (is_string($newLang) && strlen($newLang) == 2) {
            $this->store[$this->storeKey] = $newLang;
        }
    }

    public function injectStrings(&$di, $files = null) {
        if (is_null($files)) {
            $files = array($this->defaultLanguageFile);
        }
        if (!isset($this->store[$this->storeKey])) {
            $this->changeLanguage($this->defaultLanguage);
        }
        foreach ($files as $file) {
            $di[$this->defaultLanguageFile .
                ($file == $this->defaultLanguageFile ? '' : '::' . $file)] =
                    $this->loadStrings($this->store[$this->storeKey], $file);
        }
        $di['language'] = $this->store[$this->storeKey];
    }

    private function loadStrings($lang = null, $langFile = null, $configPath='config/') {
        if (is_null($lang)) {
            $lang = $this->defaultLanguage;
        }
        if (is_null($langFile)) {
            $langFile = $this->defaultLanguageFile;
        }
        if ($configPath[0] !== '/' && strpos($configPath, '://') === false) {
            $configPath = BASE_ROOT . '/' . $configPath;
        }
        $strings = \App\ConfigLoader::loadFile($configPath . $langFile . '.' . $lang . '.ini');

        return $strings;
    }
}
