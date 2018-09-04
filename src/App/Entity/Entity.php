<?php
namespace App\Entity;

class Entity
{
    protected $data = [];
    protected $privateKeys = null;
    /** @var \App\Model\Model $model */
    protected $model;
    /** @var bool $new */
    protected $new = false;
    /** @var bool $changed */
    protected $changed = false;
    /** @var \Slim\Container */
    protected $di;
    /** @var array Entity data keys that have ini language strings in them. */
    protected $ini = [];

    public function __construct($data, $model, $di) {
        $this->data = $data;
        $this->model = $model;
        $this->di = $di;
    }

    /**
     * ABSTRACT: Entities that inherit this and need a key type should override this.
     * @return integer
     */
    public function getKeyType() {
        return 0xff;
    }

    public function __isset($el) {
        return array_key_exists($el, $this->getData());
    }

    public function __get($el) {
        if ($el == 'keys') { // Only call keys if it's ever needed.
            if (is_null($this->privateKeys)) { // Memoize keys to save expensive database calls.
                $keyModel = $this->di['model.keystore'];
                $privateKeys = $keyModel->getEnabledByEntityTypeAndId($this->getKeyType(), $this->id);
                foreach ($privateKeys as $privateKey) {
                    $this->privateKeys[$privateKey->key] = $privateKey;
                }
            }
        }

        // Look for INI language strings inside database fields.
        if (in_array($el, $this->ini)) {
            $processed = $this->getData()[$el];
            $parsed = parse_ini_string($processed, true, INI_SCANNER_RAW);
            if (!count($parsed)) return $processed;
            if (isset($parsed[$this->di['language']])) return $parsed[$this->di['language']];
            return $parsed;
        }

        return $this->getData()[$el];
    }

    public function getData() {
        $data = $this->data;
        $data['keys'] = $this->privateKeys;
        return $data;
    }

    public function getKeys() {
        return $this->keys;
    }

    public function getKey($key) {
        return $this->keys[$key];
    }

    public function getSaveableData() {
        return $this->data;
    }

    public function delete() {
        return $this->model->deleteById($this->data['id']);
    }

    public function save() {
        bdump($this, 'Entity Getting Saved');
        return $this->model->save($this);
    }

    public function setNew() {
        $this->new = true;
        return $this;
    }

    public function isNew() {
        return $this->new;
    }

    public function __set($el, $val) {
        if ($el == 'id') {
            throw new \InvalidArgumentException("Unable to set primary key on entity.");
        }

        if (is_object($val)) {
            throw new \InvalidArgumentException('The value of a property of an entity cannot be an object.');
        }

        if (isset($this->data[$el])) {
            $this->changed[$el] = $val;
            $this->data[$el] = $val;
        }
        return $this;
    }

    public function getChangedValues() {
        return $this->changed;
    }

    public function hasChanged() {
        return (count($this->changed) !== 0);
    }
}

