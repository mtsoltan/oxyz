<?php
namespace App\Entity;

class Keystore extends Entity
{
    protected $ini = ['label', 'description', 'value'];

    /* Keystore value formats for forms:
     * If the form element is a select with options, an array is provided.
     * If a string is provided and it starts with FormBuilder::FORM_TRIGGER, a form element with the
     * ini properties in the rest of the string is created.
     *
     * \App\Utilities\FormBuilder is responsible for handling all of this logic.
     * Please do not handle it elsewhere.
     */

    public function getEntity() {
        $keystoreModel = $this->model;
        if ($this->entity_type == $keystoreModel::TYPE_ORDER) {
            $entityModel = $this->di['model.order'];
        }
        if ($this->entity_type == $keystoreModel::TYPE_CUSTOMER) {
            $entityModel = $this->di['model.customer'];
        }
        if ($this->entity_type == $keystoreModel::TYPE_PRODUCT) {
            $entityModel = $this->di['model.product'];
        }
        if (!$entityModel) { // entity_type wasn't one of the types that have models.
            return false;
        }

        return $entityModel->getById($this->entity_id);
    }
}