<?php

namespace App\Controller;

class OrderCtrl extends BaseCtrl
{
    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function all($request, $response, $args) {
        /** @var \App\Model\Order $orderModel */
        $orderModel = $this->di['model.order'];
        $data = [];
        if (!is_null($product_id = $request->getQueryParam('product_id'))) {
            if (ctype_digit("$product_id")) {
                $data['product_id'] = $product_id;
            }
        }
        if (!is_null($state = $request->getQueryParam('state'))) {
            if (ctype_digit("$state")) {
                $data['state'] = $state;
            }
        }
        if (!$data) {
            $data = ['state' => $orderModel::STATE_PENDING];
            $listKeys = 1;
        } else  {
            $listKeys = $request->getQueryParam('listkeys');
        }

        $orders = $orderModel->getSorted($data);
        $orders = array_filter($orders, function ($o) { return !$o->product_id; });
        $idedProduct = null;
        if (!isset($data['product_id'])) {
            $products = $this->di['model.product']->getAllServices(); // Including disabled.
        } else {
            $idedProduct = $this->di['model.product']->getById($data['product_id']);
            $products = [$idedProduct];
        }

        $productsKeyed = [];
        foreach ($products as $product) {
            $productsKeyed[$product->id] = $product;
        }
        $keystores = $this->di['model.keystore']->getByEntityData(['entity_type' => $products[0]->getKeyType()]);
        $keystoresKeyed = [];
        foreach ($keystores as $keystore) {
            $keystoresKeyed[$keystore->id] = $keystore;
        }

        return $this->view->render($response, '@private/order/all.twig', array(
            'orders' => $orders,
            'products' => $productsKeyed,
            'keystore' => $keystoresKeyed,
            'color_id' => \App\Entity\Product::KEY_COLOR,
            'list_keys' => $listKeys ? true : false,
            'orders_title' => $this->view_functions->string('titles.order_view_dynamic',
                is_null($idedProduct) ? $this->view_functions->string('titles.order_view_pid') : $idedProduct->name,
                isset($data['state']) ? $this->view_functions->string('enum.pstate.' . $data['state']) : ''),
            'states' => [
                'pending' => $orderModel::STATE_PENDING,
                'cancelled' => $orderModel::STATE_CANCELLED,
                'finalized' => $orderModel::STATE_FINALIZED,
                'rolled' => $orderModel::STATE_ROLLED,
            ],
        ));
    }

    /**
     * TODO: Build this
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function edit($request, $response, $args) {
        // TODO: Finalizing deletes file
        /** @var \App\Model\Order $orderModel */
        $orderModel = $this->di['model.order'];
        $user = $this->di['user'];
        $strings = $this->di['strings'];
        $data = $request->getParsedBody();
        $handler = new \App\Utilities\Handler($this->di, $request);
        $handler->setReferer($this->view_functions->pathFor('order:all'));

        // Validate CSRF
        if (!$this->doSimpleValidation($request, true)) {
            return $handler->respondWithError($strings['notices.invalid_csrf_token'], $response);
        }

        if (!$user->hasPermission('file_edit')) {
            return $handler->respondWithError($strings['notices.insufficient_permission'], $response);
        }

        // Validate Field Presence
        /** @var \App\Entity\Order $order */
        $order = $orderModel->getById(isset($args['order']) ? $args['order'] : 0);
        if (!$order) {
            return $handler->respondWithError($this->di['strings.forms']['required.all'], $response);
        }

        // Check what type of request this is and perform action.
        $action = isset($data['action']) ? $data['action'] : '';

        switch ($data['action']) {
            case 'note':
                if(isset($data['note'])) { // XSS vector here.
                    $order->note = $data['note']; // No htmlspecialchars, and we display raw.
                    $order = $order->save();
                    return $handler->respondWithJson(array(
                        'note' => $order->note,
                        'csrf' => $this->view_functions->csrfTokenInput(
                            $this->view_functions->pathFor('order:edit', ['order' => $order->id])),
                    ), $response);
                }
                break;
            case 'finalize':
                // $order->finalize();
                return $handler->respondWithJson(array(
                    'success' => $strings['notices.action_success'],
                    'csrf' => $this->view_functions->csrfTokenInput(
                        $this->view_functions->pathFor('order:edit', ['order' => $order->id])),
                ), $response);
                break;
            case 'cancel':
                // $order->cancel();
                return $handler->respondWithJson(array(
                    'success' => $strings['notices.action_success'],
                    'csrf' => $this->view_functions->csrfTokenInput(
                        $this->view_functions->pathFor('order:edit', ['order' => $order->id])),
                ), $response);
                break;
            case 'roll':
                // $order->rollback();
                return $handler->respondWithJson(array(
                    'success' => $strings['notices.action_success'],
                    'csrf' => $this->view_functions->csrfTokenInput(
                        $this->view_functions->pathFor('order:edit', ['order' => $order->id])),
                ), $response);
                break;
            case 'blacklist':
                // $order->blacklist();
                return $handler->respondWithJson(array(
                    'success' => $strings['notices.action_success'],
                    'csrf' => $this->view_functions->csrfTokenInput(
                        $this->view_functions->pathFor('order:edit', ['order' => $order->id])),
                ), $response);
                break;
            default:
                return $handler->respondWithError($this->di['strings::forms']['required.all'], $response);
        }
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function added($request, $response, $args) {
        return $this->view->render($response, '@public/order/added.twig', array());
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args Not used.
     * @return \Psr\Http\Message\ResponseInterface The rendered view.
     */
    public function add($request, $response, $args) {
        $productModel = $this->di['model.product'];
        $product = $productModel->getById($args['product']);

        if ($product->type !== $productModel::TYPE_SERVICE) {
            throw new \App\Exception\NoSuchXException('Service');
        }
        if ($product->state == $productModel::STATE_DISABLED) {
            throw new \App\Exception\NoSuchXException('Service');
        }

        $builder = new \App\Utilities\FormBuilder($this->di);

        $rv = $this->view->render($response, '@public/order/add.twig', [
            'product' => $product,
            'form' => $builder->buildForm($product),
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
        $productModel = $this->di['model.product'];
        $product = $productModel->getById($args['product']);

        if ($product->type !== $productModel::TYPE_SERVICE) {
            throw new \App\Exception\NoSuchXException('Service');
        }
        if ($product->state == $productModel::STATE_DISABLED) {
            throw new \App\Exception\NoSuchXException('Service');
        }

        $strings = $this->di['strings'];
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $handler = new \App\Utilities\Handler($this->di, $request);
        $handler->setReferer($this->view_functions->pathFor('order:added'));

        // Validate CSRF
        if (!$this->doSimpleValidation($request, true)) {
            return $handler->respondWithError($strings['notices.invalid_csrf_token'], $response);
        }

        $errors = array();
        $returnedFiles = array();

        // Validate Input Data
        $builder = new \App\Utilities\FormBuilder($this->di);
        $builder->buildForm($product);
        $validatedData = $builder->validateInput($data, $files, $product, true, $errors);

        bdump($data, 'data');
        bdump($validatedData, 'final');

        if ($errors) {
            $handler->regenerateToken();
            return $handler->respondWithError($errors, $response);
        }

        // Handle File
        if ($files) foreach($files as $file) {
            if (!$file->file) { // Last file[] input is always empty.
                // $errors[] = sprintf($strings['notices.file_empty'], $file->getClientFilename());
                continue;
            }

            $fileId = $this->di['utility.file']->attemptFileSave(
                $file->getStream()->getContents(),
                $file->getClientFilename(),
                $file->getSize(),
                $errors);
        }
        if ($errors) {
            $handler->regenerateToken();
            return $handler->respondWithError($errors, $response);
        }

        // Handle Customer
        $customerModel = $this->di['model.customer'];
        $browserId = $this->getBrowserId();
        $customer = $customerModel->createEntity([
            'state' => $customerModel::STATE_ENABLED,
            'phone' => $validatedData['phone']['value'],
            'name' => $validatedData['name']['value'],
            'email' => $validatedData['email']['value'],
            'province' => $validatedData[$builder::SELECT_MARKER . $customerModel::PROVINCE_FIELD]['value'],
            'flags' => (1 << $customerModel::BITMASK_CREATED +
                0 << $customerModel::BITMASK_SAVED), // TODO: Add save checkbox.
            'ip' => $this->di['ip'],
            'device' => $browserId->OS,
            'browser' => $browserId->Browser,
        ])->save();

        // Handle Order
        $orderModel = $this->di['model.order'];
        $order = $orderModel->createEntity([
            'state' => $orderModel::STATE_PENDING,
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'file_id' => $fileId,
            'ip' => $this->di['ip'],
            'amount' => $validatedData['amount']['value'],
            'customer_note' => $validatedData['notes']['value'],
        ])->save();
        $order->addKeys($validatedData);

        // Fix File
        $fileModel = $this->di['model.file'];
        $fileEntity = $fileModel->getById($fileId);
        $fileEntity->entity_type = $fileModel::TYPE_ORDER;
        $fileEntity->entity_id = $order->id;
        $fileEntity->save();

        return $handler->respondWithJson(array(
            'location' => $handler->getHeaderUrl()
        ), $response);
    }
}
