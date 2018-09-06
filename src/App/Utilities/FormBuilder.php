<?php

namespace App\Utilities;

class FormBuilder
{
    const FORM_TRIGGER = '__form::';
    const SELECT_MARKER = 'select';
    private $formText;
    private $di;
    private $inputsArray = [];
    private $fieldsArray = [];
    private $patterns = [
        'datettime-local' => '\d{4}.\d{2}.\d{2}.\d{2}(.\d{2})?',
        'date' => '\d{4}.\d{2}.\d{2}',
        'time' => '\d{2}.\d{2}(.\d{2})?',
        'tel' => '(\+(2|٢))?(01|٠١)(\d|[٠١٢٣٤٥٦٧٨٩]){9}?',
        'number' => '(\d|[٠١٢٣٤٥٦٧٨٩])+(\.(\d|[٠١٢٣٤٥٦٧٨٩])+)?',
        'email' => '([a-zA-Z0-9_\-.]+)@([a-zA-Z0-9_\-.]+)\.([a-zA-Z]{2,11})?',
    ];

    public function __construct($di, &$store = null) {
        $this->di = $di;
    }

    /**
     * Populates the $inputsArray with data from the provided product.
     * @param \App\Entity\Keystore[] $keys
     * @return boolean true if any sort of population did occur.
     */
    private function populateInputs($keys, $separator = '') {
        $somethingHappened = false;
        $i = 0;
        foreach ($keys as $key) {
            if ($key->state == \App\Model\Keystore::STATE_DISABLED) continue;
            if (!$key->on_form) continue;
            if (!is_array($key->value)) continue;
            // Selects are arrays by nature.
            // Normal elements are arrays because INI parse doesn't fail on them.

            $somethingHappened = true;
            $viewArray = [
                'attributes' => $key->value,
                'label' => $key->label,
                'description' => $key->description,
                'separator' => (++$i == count($keys) ? $separator : ''),
            ];

            if (strpos($key->getSaveableData()['value'], self::FORM_TRIGGER) === 0) { // A normal element.
                if ($viewArray['attributes']['type'] == 'textarea') {
                    $inputParagraph = $this->di['view']->fetch('@layout/form_builder/textarea.twig', $viewArray);
                } else {
                    if (isset($this->patterns[$viewArray['attributes']['type']]) && !isset($viewArray['attributes']['pattern'])) {
                        $viewArray['attributes']['pattern'] = $this->patterns[$viewArray['attributes']['type']];
                    }
                    $inputParagraph = $this->di['view']->fetch('@layout/form_builder/input.twig', $viewArray);
                }
            } else { // A select. $key->value are options.
                $viewArray['key'] = self::SELECT_MARKER . $key->key;
                $inputParagraph = $this->di['view']->fetch('@layout/form_builder/select.twig', $viewArray);
            }
            $this->inputsArray[] = $inputParagraph;
        }
        return $somethingHappened;
    }

    private function populateFields($keys) {
        array_push($this->fieldsArray, ...$keys);
    }

    /**
     * Validates an input value based on an attribute of the form input.
     * @param string $input_value
     * @param string $attribute
     * @param string $attribute_value
     * @return number|boolean
     */
    private function validateAttribute($input_value, $attribute, $attribute_value) {
        switch ($attribute) {
            case 'required' : return strlen($input_value);
            case 'minlength': return strlen($input_value) >= intval($attribute_value);
            case 'maxlength': return strlen($input_value) <= intval($attribute_value);
            case 'pattern'  : return !strlen($input_value) || preg_match('%^' . $attribute_value . '$%', trim($input_value));
            case 'min'      : return !strlen($input_value) || floatval($input_value) >= $attribute_value;
            case 'max'      : return !strlen($input_value) || floatval($input_value) <= $attribute_value;
            default         : return true;
        }
    }

    /**
     * Builds a form and returns it as an HTML string.
     * @param \App\Entity\Product $product The service to build form for.
     * @return string
     * @throws \App\Exception\NoSuchXException on being unable to populate the form.
     *
     */
    public function buildForm($product, $includeCustomer = true) {
        if ($includeCustomer) {
            $this->populateInputs($this->di['model.keystore']->getCustomerFields(), '<hr><hr>');
        }
        $this->populateInputs($this->di['model.keystore']->getAmountField());
        $this->populateInputs($product->getKeys());
        if (!count($this->inputsArray)) {
            throw new \App\Exception\NoSuchXException('form');
        }

        return implode('', $this->inputsArray);
    }

    /**
     * Validates the input against a set of rules.
     * Fills the error array accordingly.
     * @param string[] $data The post data.
     * @param \Slim\Http\UploadedFile[] $files The file array.
     * @param \App\Entity\Product $product The product to validate against.
     * @param string[] $errors
     * @return array|boolean Returns validated $data or false if a validation error occurs.
     */
    public function validateInput($data, $files, $product, $validateCustomer = true, &$errors) {
        $this->populateFields($product->getKeys());
        $this->populateFields($this->di['model.keystore']->getAmountField());
        if ($validateCustomer) {
            $this->populateFields($this->di['model.keystore']->getCustomerFields());
        }
        $strings = $this->di['strings::forms'];
        $final = [];
        foreach ($this->fieldsArray as $key) {
            if ($key->state == \App\Model\Keystore::STATE_DISABLED) continue;
            if (!$key->on_form) continue;
            if (!is_array($key->value)) continue;
            // Selects are arrays by nature.
            // Normal elements are arrays because INI parse doesn't fail on them.

            // Setting basic variables that we will use.
            $a = $key->value;
            $label = $key->label;

            // Normalizing data.
            if (strpos($key->getSaveableData()['value'], self::FORM_TRIGGER) === 0) { // A normal element.
                $current = isset($data[$a['name']]) ? $data[$a['name']] : '';

                // Do attribute based validation.
                foreach ($a as $attribute => $attribute_value) {
                    if ($a['type'] !== 'file') {
                        if (!$this->validateAttribute($current, $attribute, $attribute_value)) {
                            $errors[] = sprintf($strings[$attribute], $label, $attribute_value);
                        }
                    }
                }

                // Do type based validation.
                switch ($a['type']) { // Fixing values in final data.
                    case 'file':
                        if (isset($a['required'])) { // Check file presence.
                            if (!$files) {
                                $errors[] = sprintf($strings['required.file']);
                            }
                            foreach ($files as $file) {
                                if ($file->file) continue;
                                $errors[] = sprintf($strings['required.file']);
                            }
                        }
                        if (!isset($a['multiple'])) { // Check file count.
                            if (count($files) > 1) {
                                $errors[] = sprintf($strings['multiple.file']);
                            }
                        }
                        foreach ($files as $file) { // Check file extension.
                            $name = $file->getClientFilename();
                            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                            if (!$ext || !strlen($ext) || !in_array($ext, $product->getAllowedExtensions(), true)) {
                                $errors[] = sprintf($strings['pattern.file'], $name);
                            }
                        }
                        break;
                    case 'datettime-local': // yyyy-MM-ddThh:mm => timestamp
                        if (!$this->validateAttribute($current, 'pattern', $this->patterns['datetime-local'])) {
                            $errors[] = sprintf($strings['pattern.datetime'], $label);
                            break;
                        }
                        $current = \DateTime::createFromFormat('Y m d H i s', preg_replace('\D', ' ', $current))->getTimestamp();
                        break;
                    case 'date': // Fix data to be timestamp
                        if (!$this->validateAttribute($current, 'pattern', $this->patterns['date'])) {
                            $errors[] = sprintf($strings['pattern.date'], $label);
                            break;
                        }
                        // TODO: Format split to generate timestamp at end.
                        break;
                    case 'time': // Fix data to be timestamp
                        if (!$this->validateAttribute($current, 'pattern', $this->patterns['time'])) {
                            $errors[] = sprintf($strings['pattern.time'], $label);
                            break;
                        }
                        // TODO: Format split to generate timestamp at end.
                        break;
                    case 'tel': // Fix data to remove +201
                        if (!$this->validateAttribute($current, 'pattern', $this->patterns['tel'])) {
                            $errors[] = sprintf($strings['pattern.tel'], $label);
                            break;
                        }
                        $current = preg_replace('%^(\+2)?01%', '', $current);
                        break;
                    case 'number':
                        if (!$this->validateAttribute($current, 'pattern', $this->patterns['number'])) {
                            $errors[] = sprintf($strings['pattern.number'], $label);
                            break;
                        }
                        break;
                    case 'email':
                        if (!$this->validateAttribute($current, 'pattern', $this->patterns['email'])) {
                            $errors[] = sprintf($strings['pattern.email'], $label);
                            break;
                        }
                        break;
                    default:
                        $current = htmlspecialchars($current);
                        break;
                }

                $final[$a['name']] = ['key' => $key, 'value' => $current];
            } else { // A select. $key->value are options. Selects which aren't required should be required and have an empty option for validation.
                $name = self::SELECT_MARKER . $key->key;
                $current = isset($data[$name]) ? $data[$name] : '='; // Equal signs cannot exist as a key in INI.
                $exists = in_array(strval($current), array_map('strval', array_keys($key->value))); // Convert all keys to strings to prevent comparison drama.
                if (!$exists) {
                    $errors[] = sprintf($strings['select'], $label);
                    $current = '';
                }
                $final[$name] = ['key' => $key, 'value' => $current];
            }
        }

        $final = array_filter($final, function ($o) {
            return $o !== '';
        });

        if ($errors) return false;
        return $final;
    }
}
