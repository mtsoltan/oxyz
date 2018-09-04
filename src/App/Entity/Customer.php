<?php
namespace App\Entity;

class Customer extends Entity
{
    protected $ini = [];

    public function addKey($key, $label, $description, $value) {
        $keyModel = $this->di['model.keystore'];
        $keyEntity = $keyModel->createEntity([
            'state' => $keyModel::STATE_ENABLED,
            'key' => $key,
            'label' => $label,
            'description' => $description,
            'value' => $value,
            'entity_type' => $keyModel::TYPE_CUSTOMER,
            'entity_id' => $this->id,
            'on_form' => 0,
            'on_dashboard' => 0,
        ])->save();
        if (!is_null($this->privateKeys)) {
            $this->privateKeys[] = $keyEntity;
        }
        return $this;
    }

    public function getOrders() {
        return $this->di['model.order']->getByEntityData(['customer_id' => $this->id]);
    }

    public function disable() {
        $model = $this->model;
        if ($this->state == $model::STATE_ENABLED) {
            $this->state = $model::STATE_DISABLED;
            return $this->save();
        }
        return $this;
    }
    public function  blacklist() {
        // Cancel all orders by IP (in blacklist entity).
        // Disable all customers by IP (in blacklist entity).
        // Blacklist logic.
        return $this;
    }

    public function verify() {
        // Customer verify logic.
        return $this;
    }
}