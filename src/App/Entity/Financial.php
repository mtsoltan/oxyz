<?php
namespace App\Entity;

class Financial extends Entity
{
    protected $ini = [];

    protected $reservedOrders = 8; // 1 to 8.

    public function getKeyType() {
        $keystoreModel = $this->di['model.keystore'];
        return $keystoreModel::TYPE_OTHER;
    }

    /**
     * @return \App\Entity\Customer
     */
    public function getCustomer() {

    }

    /**
     * @return \App\Entity\Order
     */
    public function getOrder() {

    }

    public function cancel() {

    }

    public function finalize() {

    }

    public function roll() {

    }

    // TODO: Complex financial logic here.
}
