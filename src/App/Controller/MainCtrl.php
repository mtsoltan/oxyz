<?php

namespace App\Controller;

class MainCtrl extends BaseCtrl
{

    /**
     * Preflight options response for progress tracking.
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param $args array Not used.
     * @return \Slim\Http\Response
     */
    public function preflight($request, $response, $args) {
        return $response->withStatus(200);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function index($request, $response, $args) {
        return $this->view->render($response, '@public/index.twig', array(
            'services' => $this->di['model.product']->getEnabledServices()
        ));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function dashboard($request, $response, $args) {
        return $this->view->render($response, '@private/dashboard.twig');
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function products($request, $response, $args) {
        throw new \App\Exception\NotFound();
        return $this->view->render($response, '@private/index.twig');
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Slim\Http\Response The rendered view.
     */
    public function contact($request, $response, $args) {
        return $this->view->render($response, '@public/contact.twig');
    }
}