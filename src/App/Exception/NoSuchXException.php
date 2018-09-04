<?php

namespace App\Exception;

class NoSuchXException extends NotFound
{
    protected $what;

    public function __construct($what) {
        $this->what = $what;
        parent::__construct("No such {$what}");
    }

    public function getWhat() {
        return $this->what;
    }
}
