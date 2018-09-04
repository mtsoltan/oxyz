<?php
namespace App\Entity;

class Product extends Entity
{
    public function getKeyType() {
        $keystoreModel = $this->di['model.keystore'];
        return $keystoreModel::TYPE_PRODUCT;
    }

    const KEY_NOTES        = 127;
    const KEY_ALLOWED_EXTS = 126; // TODO: Set customer 254 to province

    protected $ini = ['name', 'description'];

    public function getData() {
        $data = parent::getData();
        $check = strpos($data['image'], $this->di['config']['site.site_url']) === 0 ||
                 strpos($data['image'], '/') === 0;
        if (!$check) $data['image'] = '/static/images/crossorigin.png';
        return $data;
    }

    /**
     * Checks the keystore for a key with the key number being self::KEY_ALLOWED_EXTS
     * Returns the value of that key (an array) mapped to strtolower
     * Please make sure you check this with lowercase extensions using strict comparison.
     * @return array
     */
    public function getAllowedExtensions() {
        return array_map('strtolower', $this->getKey(self::KEY_ALLOWED_EXTS)->value);
    }
}