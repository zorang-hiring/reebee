<?php
declare(strict_types=1);

namespace App\Form;

class PageCreateForm extends AbstractPageForm
{
    protected $flyerService;

    public function __construct(array $data, \App\Service\Flyer $flyerService)
    {
        parent::__construct($data);
        

        $this->flyerService = $flyerService;
    }
    
    /**
     * @var array
     */
    protected $fields = [
        'flyerID' => false,
        'dateValid' => false,
        'dateExpired' => false,
        'pageNumber' => false
    ];

    public function isValid()
    {
        // required fields
        foreach (['flyerID', 'dateValid', 'dateExpired'] as $field) {
            if ($this->fields[$field] === false || strlen((string) $this->fields[$field]) === 0) {
                $this->addError($field, 'Field is required.');
            }
        }

        // check dates
        foreach (['dateValid', 'dateExpired'] as $field) {
            if (\DateTime::createFromFormat('Y-m-d', (string) $this->fields[$field]) === false) {
                $this->addError($field, 'Has to be date in the form YYYY-MM-DD.');

            }
        }
        
        // validate that flyer exists 
        if (!empty($this->fields['flyerID']) && !$this->flyerService->find($this->fields['flyerID'])) {
            $this->addError('flyerID', sprintf('Flyer "%s" does not exist.', $this->fields['flyerID']));
        }

        if ($this->fields['pageNumber'] !== false && !is_numeric($this->fields['pageNumber'])) {
            $this->addError('pageNumber', 'Has to be empty or integer.');
        }

        return empty($this->getErrors());
    }
}