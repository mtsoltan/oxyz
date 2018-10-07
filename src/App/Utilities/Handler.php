<?php

namespace App\Utilities;

class Handler
{
    const XHR_REQUEST_PROOF = 'jsenabled'; // request parameter name to prove this is an XHR request. If you change this, change it in global.js!

    private $di;

    /**
     * @var \Slim\Http\Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $referer;
    protected $refererBase;
    protected $newCsrf;

    /**
     * Optional
     * @var string
     */
    protected $successRedirParams = '';

    /**
     * @param \Slim\Container $di The dependency injection container.
     * @param \Slim\Http\Request $request The request for parsing parameters.
     */
    public function __construct($di, $request)
    {
        $this->request = $request;
        $this->refererBase = $di['config']['site.site_url'];
        $this->di = $di;
        $this->setReferer($this->request->getHeader('referer')[0]);
    }

    /**
     * Set a custom referer.
     * @param string $referer
     * @return \App\Utilities\Handler this
     */
    public function setReferer($referer)
    {
        $referer = parse_url($referer);
        // Only allow redirects to main site.
        $this->referer = $this->refererBase . $referer['path'];
        if (isset($referer['query'])) $this->referer .= '?' . $referer['query'];
        if (isset($referer['fragment'])) $this->referer .= '#' . $referer['fragment'];
        return $this;
    }

    /**
     * Set custom query parameters for redirect.
     * @param array $params
     * @return \App\Utilities\Handler this
     */
    public function setSuccessRedirParams($params)
    {
        $this->successRedirParams = '?' . http_build_query($params);
        return $this;
    }

    public function getHeaderUrl()
    {
        return $this->referer . $this->successRedirParams;
    }

    /**
     * Regenerates the CSRF token and sends it in the response JSON.
     * Does nothing in case JS is disabled (redirect).
     * If path is null, uses the current route path.
     * @param string $path
     * @return string The CSRF token, just in case we need it.
     */
    public function regenerateToken($path = null) {
        $util = $this->di['utility.view'];

        if (is_null($path)) {
            $path = $util->pathFor($this->di['current_route']['name'], $this->di['current_route']['arguments']);
        }
        $this->newCsrf = $util->csrfTokenInput($path);
        return $this->newCsrf;
    }

    /**
     * Responds with success.
     *
     * Curls to referer and returns a JSON response if request was an XHR request.
     * Redirects to referer if this is a browser request.
     * Passes the query parameters provided using setSuccessRedirParams on curl or redirect.
     * @param array $json
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function respondWithJson($json, $response = NULL)
    {
        if (!$response) $response = new \Slim\Http\Response();

        if (!$this->requestIsXhr()) {
            // TODO: Use flash here.
            return $response->withHeader('Location',  $this->getHeaderUrl())->withStatus(303);
        }
        if ($this->newCsrf) $json['csrf'] = $this->newCsrf;
        return $response->withJson($json)->withStatus(200);
    }

    /**
     * Responds with failure.
     *
     * Returns a JSON response if request was an XHR request.
     * Redirects to referer if this is a browser request.
     * Passes the query parameters provided using setSuccessRedirParams on curl or redirect.
     * @param string|array $error
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function respondWithError($error, $response = NULL, $statusCode = 403)
    {
        if (!$response) $response = new \Slim\Http\Response();
        if (is_array($error)) $error = implode('<br>', $error);

        if (!$this->requestIsXhr()) {
            // TODO: Use flash here.
            return $response->withHeader('Location',  $this->getHeaderUrl())->withStatus(303);
        }
        $json = array('error' => $error);
        if ($this->newCsrf) $json['csrf'] = $this->newCsrf;
        return $response->withJson($json)->withStatus($statusCode);
    }

    /**
     * Is the request an XHR request or a normal browser request?
     */
    public function requestIsXhr()
    {
        if (strtolower($this->request->getMethod()) == 'get') {
            $referer = $this->request->getQueryParams()[self::XHR_REQUEST_PROOF];
        } else {
            $referer = $this->request->getParsedBodyParam(self::XHR_REQUEST_PROOF);
        }
        return (bool)($referer);
    }

}
