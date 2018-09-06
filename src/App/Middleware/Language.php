<?php

namespace App\Middleware;

/**
 * This middleware sets the language.
 *
 */
class Language
{
    protected $di;
    protected $strings = ['strings', 'forms'];

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
        $stringUtils = new \App\Utilities\StringUtils($this->di);
        $stringUtils->changeLanguage($request->getParam('lang'));
        $stringUtils->injectStrings($this->di, $this->strings);
        unset($stringUtils);

        // Fix indian numerals.
        $params = $request->getParsedBody();
        if ($params){
            foreach ($params as $key => $param) {
                $params[$key] = preg_replace_callback('%[٠١٢٣٤٥٦٧٨٩]+%u',
                    function ($matches) {
                        $rv = '';
                        foreach (str_split($matches[0], 2) as $match) {
                            bdump($match);
                            switch ($match) {
                                case '٠': $rv .= '0'; break;
                                case '١': $rv .= '1'; break;
                                case '٢': $rv .= '2'; break;
                                case '٣': $rv .= '3'; break;
                                case '٤': $rv .= '4'; break;
                                case '٥': $rv .= '5'; break;
                                case '٦': $rv .= '6'; break;
                                case '٧': $rv .= '7'; break;
                                case '٨': $rv .= '8'; break;
                                case '٩': $rv .= '9'; break;
                                default: $rv .= '';
                            }
                        }
                        return $rv;
                    }, $param);
            }
            $request = $request->withParsedBody($params);
        }

        return $next($request, $response); // Call the next middleware
    }
}
