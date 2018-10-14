<?php
namespace App\Entity;

class Order extends Entity
{
    protected $ini = [];

    public function getKeyType() {
        $keystoreModel = $this->di['model.keystore'];
        return $keystoreModel::TYPE_ORDER;
    }

    /**
     * Data is an array of ['key' => $key, 'value' => $value] arrays.
     * @param array[] $data
     */
    public function addKeys($data) {
        $keyModel = $this->di['model.keystore'];
        foreach ($data as $name => $datum) {
            if ($datum['key']->on_dashboard && $datum['key']->entity_type == $keyModel::TYPE_PRODUCT) {
                $keyEntity = $keyModel->createEntity([
                    'state' => $keyModel::STATE_ENABLED,
                    'key' => $datum['key']->key,
                    'label' => $datum['key']->label,
                    'description' => $datum['key']->id,
                    'value' => $datum['value'],
                    'entity_type' => $keyModel::TYPE_ORDER,
                    'entity_id' => $this->id,
                    'on_form' => 0,
                    'on_dashboard' => 0,
                ])->save();
                if (!is_null($this->privateKeys)) {
                    $this->privateKeys[] = $keyEntity;
                }
            }
        }
        return $this;
    }

    /**
     * @return \App\Entity\File
     */
    public function getFile() {
        return $this->di['model.file']->getById($this->file_id);
    }

    /**
     * @return \App\Entity\Product
     */
    public function getProduct() {
        return $this->di['model.product']->getById($this->product_id);
    }

    /**
     * @return \App\Entity\Financial
     */
    public function getFinancials() {
        return $this->di['model.financial']->getByEntityData(['order_id' => $this->id]);
    }

    /**
     * @return \App\Entity\Customer
     */
    public function getCustomer() {
        return $this->di['model.customer']->getById($this->customer_id);
    }

    public function  finalize() {
        $model = $this->model;
        if ($this->state == $model::STATE_PENDING) {
            $this->state = $model::STATE_FINALIZED;
            $rv = $this->save();
            if ($rv) {
                // TODO: Transaction logic.
                return $rv;
            }
        }
        throw new \LogicException('Cannot finalize an order that is not pending.');
    }

    public function  rollback() {
        $model = $this->model;
        if ($this->state == $model::STATE_FINALIZED) {
            $this->state = $model::STATE_ROLLED;
            $rv = $this->save();
            if ($rv) {
                // TODO: Transaction logic.
                return $rv;
            }
        }
        throw new \LogicException('Cannot roll back an order that is not finalized.');
    }

    public function  cancel() {
        $model = $this->model;
        if ($this->state == $model::STATE_PENDING || $model::STATE_ROLLED) {
            $this->state = $model::STATE_CANCELLED;
            $rv = $this->save();
            if ($rv) {
                // TODO: Transaction logic.
                return $rv;
            }
        }
        throw new \LogicException('Cannot cancel an order that is finalized. Please roll it back first.');
    }

    public function  blacklist() {
        // TODO: Cancel all orders by IP (in blacklist entity).
        // Disable all customers by IP (in blacklist entity).
        // Blacklist logic.
        return $this;
    }

    public function verify() {
        $this->getCustomer()->verify();
        return $this;
    }

    /**
     * @param float $tx
     * @param string $note
     */
    public function createFinancial($tx, $note = '') {
        // Create a new Financial.
        /** @var \App\Model\Financial $model */
        $model = $this->di['model.financial'];
        $model->createEntity([
            'state' => $model::STATE_PENDING,
            'customer_id' => $this->customer_id,
            'order_id' => $this->id,
            'item' => $this->getProduct()->getWithLanguage('name', 'en'),
            'item_amount' => $this->amount,
            'transaction' => $tx,
            'note' => $note,
        ]);
    }
}
