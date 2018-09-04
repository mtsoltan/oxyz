<?php

namespace App\Model;

class Customer extends Model
{
    const CUSTOMER_FIELDS = 1;
    const AMOUNT_FIELD    = 2;
    const PROVINCE_FIELD  = 254;

    const BITMASK_CREATED = 0;
    const BITMASK_SAVED   = 1;
    const BITMASK_ACKED   = 2;

    public function getTableName() {
        return 'customers';
    }
}
