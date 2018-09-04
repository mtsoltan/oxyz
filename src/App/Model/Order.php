<?php

namespace App\Model;

class Order extends Model
{
    const STATE_PENDING   = 0;
    const STATE_CANCELLED = 1;
    const STATE_FINALIZED = 2;
    const STATE_ROLLED    = 3;

    public function getTableName() {
        return 'orders';
    }

    public function getPending() {
        return $this->getByEntityData(
            ['state' => self::STATE_PENDING, 'state' => self::STATE_ENABLED], 0, 0,
            ['create_timestamp', self::SORT_DESC]);
    }

    // getFinalizedByProductId
}
