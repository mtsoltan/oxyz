<?php

namespace App\Exception;

class DatabaseException extends \Exception
{
    public function __construct($sql) {
        parent::__construct('Error in SQL ' . $sql);
    }
}
