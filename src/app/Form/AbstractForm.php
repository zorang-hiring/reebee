<?php
declare(strict_types=1);

namespace App\Form;

use App\Request;

abstract class AbstractForm
{
    private $errors = [];

    /**
     * @return bool
     */
    abstract public function isValid();

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function addError($field, $message)
    {
        if (!array_key_exists($field, $this->errors)) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
}