<?php
declare(strict_types=1);

namespace App\Form;

class PageUpdateForm extends AbstractPageForm
{
    /**
     * @var array
     */
    protected $fields = [
        'dateValid' => false,
        'dateExpired' => false
    ];
    
    public function isValid()
    {
        // required fields
        foreach (['dateValid', 'dateExpired'] as $field) {
            if ($this->fields{$field} === false || strlen((string) $this->fields{$field}) === 0) {
                $this->addError($field, 'Field is required.');
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