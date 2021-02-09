<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Flyer;

abstract class AbstractFlyerForm extends AbstractForm
{
    protected $fields = [
        'name' => false,
        'storeName' => false,
        'dateValid' => false,
        'dateExpired' => false,
        'pageCount' => false
    ];

    public function fillFlyer(Flyer $flyer)
    {
        if ($this->fields['name'] !== false) {
            $flyer->setName($this->fields['name']);
        }
        if ($this->fields['storeName'] !== false) {
            $flyer->setStoreName($this->fields['storeName']);
        }
        if ($this->fields['dateValid'] !== false) {
          $flyer->setDateValid(
              \DateTime::createFromFormat('Y-m-d H:i:s', $this->fields['dateValid'] . ' 00:00:00')
          );
        }
        if ($this->fields['dateExpired'] !== false) {
         $flyer->setDateExpired(
             \DateTime::createFromFormat('Y-m-d H:i:s', $this->fields['dateExpired'] . ' 00:00:00')
         );
        }
        if ($this->fields['pageCount'] !== false) {
          $flyer->setPageCount($this->fields['pageCount']);
        }
        return $flyer;
    }

    public function fillForm(array $data)
    {
        foreach (array_keys($this->fields) as $fieldName) {
            if (array_key_exists($fieldName, $data)) {
                $this->fields[$fieldName] = $data[$fieldName];
            }
        }
    }
}