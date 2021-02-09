<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Flyer;
use App\Repository\DbConnectorInterface;
use App\Request;

class FlyerSaveForm extends AbstractForm
{
    protected $name = false;
    protected $storeName = false;
    protected $dateValid = false;
    protected $dateExpired = false;
    protected $pageCount = false;

    protected $fields = [
        'name' => false,
        'storeName' => false,
        'dateValid' => false,
        'dateExpired' => false,
        'pageCount' => false
    ];
    
    public function __construct(Request $request)
    {
        // fill form with request
        $requestData = $request->getPostData();
        foreach (array_keys($this->fields) as $fieldName) {
            if (array_key_exists($fieldName, $requestData)) {
                $this->fields[$fieldName] = $requestData[$fieldName];
            }
        }
    }

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

    public function fillFlyer(Flyer $flyer)
    {
        if ($this->fields['name'] !== false) {
            $flyer->setName($this->fields['name']);
        }
        if ($this->fields['storeName'] !== false) {
            $flyer->setStoreName($this->fields['storeName']);
        }
        if ($this->fields['dateValid'] !== false) {
          $flyer->setDateValid(\DateTime::createFromFormat('Y-m-d', $this->fields['dateValid']));
        }
        if ($this->fields['dateExpired'] !== false) {
         $flyer->setDateExpired(\DateTime::createFromFormat('Y-m-d', $this->fields['dateExpired']));
        }
        if ($this->fields['pageCount'] !== false) {
          $flyer->setPageCount($this->fields['pageCount']);
        }
        return $flyer;
    }

    public function fillForm(Flyer $flyer)
    {
        $this->fields['name'] = $flyer->getName();
        $this->fields['storeName'] = $flyer->getStoreName();
        $this->fields['dateValid'] = $flyer->getDateValid()->format('Y-m-d');
        $this->fields['dateExpired'] = $flyer->getDateExpired()->format('Y-m-d');
        $this->fields['pageCount'] = $flyer->getPageCount();
    }
}