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
    public function all($request, $response, $args) {
        // TODO: View all financials. A GET param should exist for type (order_id) and
        // TODO: one type for all order_id's greater than or equal to 20.
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function added($request, $response, $args) {
        // TODO: Verification with cancel and edit buttons.
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function add($request, $response, $args) {
        /** @var \App\Model\Order $orderModel */
        $orderModel = $this->di['model.order'];
        $specialItems = $orderModel->getByEntityData(['product_id' => 0]);

        $rv = $this->view->render($response, '@private/financial/add.twig', [
            'order_id' => $request->getQueryParam('order_id'),
            'special_items' => $specialItems,
        ]);

        return $rv;
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function handleAdd($request, $response, $args) {
        // TODO: Typical handleAdd
    }
    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function edit($request, $response, $args) {
        /* TODO: Handle state changes, type changes, and other entity_data changes.
         * In $this::all, we should have small edit buttons beside values.
         * Just like the comments in OrderCtrl.
         * This should be abstracted in its own twig template and view function.
         */
    }
}
