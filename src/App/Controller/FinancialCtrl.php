<?php

namespace App\Controller;

class FinancialCtrl extends BaseCtrl
{
    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function all($request, $repsonse, $args) {
        // TODO: View all financials. A GET param should exist for type (order_id) and
        // TODO: one type for all order_id's greater than or equal to 20.
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function added($request, $repsonse, $args) {
        // TODO: Verification with cancel and edit buttons.
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function add($request, $repsonse, $args) {
        // TODO: Typical Add form.
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function handleAdd($request, $repsonse, $args) {
        // TODO: Typical handleAdd
    }
    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function edit($request, $repsonse, $args) {
        /* TODO: Handle state changes, type changes, and other entity_data changes.
         * In $this::all, we should have small edit buttons beside values.
         * Just like the comments in OrderCtrl.
         * This should be abstracted in its own twig template and view function.
         */
    }
}
