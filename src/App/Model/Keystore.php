<?php

namespace App\Model;

class Keystore extends Model
{
    const TYPE_SITE     = 0;
    const TYPE_ORDER    = 1;
    const TYPE_CUSTOMER = 2;
    const TYPE_PRODUCT  = 3;
    const TYPE_OTHER    = 4;

    const CUSTOMER_FIELDS = 1;
    const AMOUNT_FIELD    = 2;

    public function getTableName() {
        return 'keystore';
    }

    public function getCustomerFields() {
        return $this->getEnabledByEntityTypeAndId(self::TYPE_SITE, self::CUSTOMER_FIELDS);
    }

    public function getAmountField() {
        return $this->getEnabledByEntityTypeAndId(self::TYPE_SITE, self::AMOUNT_FIELD);
    }

    public function getEnabledByEntityTypeAndId($type, $id) {
        return $this->getByEntityData(
            ['entity_type' => $type, 'entity_id' => $id, 'state' => self::STATE_ENABLED], 0, 0,
            ['key', self::SORT_ASC]);
    }
}
