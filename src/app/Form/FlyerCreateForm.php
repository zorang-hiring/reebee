<?php
declare(strict_types=1);

namespace App\Form;

class FlyerCreateForm extends AbstractFlyerForm
{
    public function isValid()
    {
        // required fields
        foreach (['name', 'storeName', 'dateValid', 'dateExpired', 'pageCount'] as $field) {
            if ($this->fields{$field} === false || strlen((string) $this->fields{$field}) === 0) {
                $this->addError($field, 'Field is required.');
            }
        }

        // check integers
        foreach (['pageCount'] as $field) {
            if ($this->fields{$field} !== false && !is_numeric($this->fields{$field})) {
                $this->addError($field, 'Has to be integer.');
            }
        }

        // check dates
        foreach (['dateValid', 'dateExpired'] as $field) {
            if (\DateTime::createFromFormat('Y-m-d', (string) $this->fields{$field}) === false) {
                $this->addError($field, 'Has to be date in the form YYYY-MM-DD.');

            }
        }

        return empty($this->getErrors());
    }
}