<?php
namespace App\Entity;

class Financial extends Entity
{
    protected $ini = [];

    public function getKeyType() {
        $keystoreModel = $this->di['model.keystore'];
        return $keystoreModel::TYPE_OTHER;
    }

    // TODO: Complex financial logic and getters here.
}
