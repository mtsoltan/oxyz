<?php

namespace App\Model;

class Product extends Model
{
    const TYPE_PRODUCT = 0;
    const TYPE_SERVICE = 1;

    public function getTableName() {
        return 'products';
    }

    public function createService() {
        // TODO: When doing admin, create service here.
    }

    public function getEnabledServices() {
        return $this->getByEntityData(['type' => self::TYPE_SERVICE, 'state' => self::STATE_ENABLED]);
    }
}
