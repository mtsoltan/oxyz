<?php

namespace App\Model;

class Financial extends Model
{
    const STATE_PENDING   = 0;
    const STATE_CANCELLED = 1;
    const STATE_FINALIZED = 2;
    const STATE_ROLLED    = 3;

    public function getTableName() {
        return 'financials';
    }

    public function getSorted($data, $limit = 0, $offset = 0) {
        return $this->getByEntityData($data, $limit, $offset,
            ['create_timestamp', self::SORT_DESC]);
    }

    // TODO: Multiple creates (order transaction).
}
