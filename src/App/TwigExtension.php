<?php

namespace App;

class TwigExtension extends \Twig_Extension
{
    /**
     * @var \App\Utilities\View
     */
    private $view_functions;

    public function __construct(\App\Utilities\View $view_functions) {
        $this->view_functions = $view_functions;
    }

    public function getName() {
        return 'slim';
    }

    public function getFunctions() {
        $fn = $this->view_functions;

        // map function names in twig to function names implemented in
        // the view functions utility
        $functionMappings = array(
            'file' => 'getFile',
            'user' => 'getUser',
            'base_url' => 'baseUrl',
            'current_url' => 'currentUrl',
            'current_path' => 'currentPath',
            'current_route' => 'currentRoute',
            'config' => 'config',
            'string' => 'string',
            'di' => 'getDi',
            'url' => 'pathFor',
            'cssurl' => 'cssUrl',
            'jsurl' => 'jsUrl',
            'imgurl' => 'imgUrl',
            'flashMessages' => 'getFlashMessages',
            'csrf_token_input' => 'csrfTokenInput',
            'csrf_token_array' => 'csrfTokenArray',
            'md5' => 'getMd5',
            'active_session_count' => 'getActiveSessionCount',
        );

        $functions = array();
        foreach ($functionMappings as $nameFrom => $nameTo)
        {
            $callable = array($fn, $nameTo);
            if (!is_callable($callable)) throw new \Exception("Function $nameTo does not exist in view functions");
            $functions[] = new \Twig_SimpleFunction($nameFrom, $callable);
        }

        return $functions;
    }
}
