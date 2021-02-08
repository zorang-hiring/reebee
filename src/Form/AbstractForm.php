<?php
declare(strict_types=1);

namespace App\Form;

use App\Request;

abstract class AbstractForm
{
    protected $errors = [];

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
}