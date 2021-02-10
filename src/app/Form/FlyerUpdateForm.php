<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Flyer;
use App\Request;

class FlyerUpdateForm extends AbstractFlyerForm
{
    public function isValid()
    {         
        foreach (['name', 'storeName', 'dateValid', 'dateExpired'] as $field) {
            if (
                $this->fields{$field} !== false
                && strlen((string) $this->fields{$field}) === 0
            ) {
                $this->addError($field, 'Field can not be empty.');
            }
        }
        
        // check dates
        foreach (['dateValid', 'dateExpired'] as $field) {
            if (
                $this->fields{$field} !== false
                && \DateTime::createFromFormat('Y-m-d', (string) $this->fields{$field}) === false
            ) {
                $this->addError($field, 'Has to be date in the form YYYY-MM-DD.');

            }
        }

        return empty($this->getErrors());
    }
}