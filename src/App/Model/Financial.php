<?php

namespace App\Model;

class Financial extends Model
{
    public function getTableName() {
        return 'financials';
    }

    public function getSorted($data, $limit = 0, $offset = 0) {
        return $this->getByEntityData($data, $limit, $offset,
            ['create_timestamp', self::SORT_DESC]);
    }

    // TODO: Multiple creates (order transaction).
}
