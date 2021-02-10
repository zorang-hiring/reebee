<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Page;

abstract class AbstractPageForm extends AbstractForm
{
    protected $fields = [
        'flyerID' => false,
        'dateValid' => false,
        'dateExpired' => false
    ];

    public function __construct(array $data)
    {
        foreach (array_keys($this->fields) as $fieldName) {
            if (array_key_exists($fieldName, $data)) {
                $this->fields[$fieldName] = $data[$fieldName];
            }
        }
    }

//    public function fillFlyer(Flyer $flyer)
//    {
//        if ($this->fields['name'] !== false) {
//            $flyer->setName($this->fields['name']);
//        }
//        if ($this->fields['storeName'] !== false) {
//            $flyer->setStoreName($this->fields['storeName']);
//        }
//        if ($this->fields['dateValid'] !== false) {
//          $flyer->setDateValid(
//              \DateTime::createFromFormat('Y-m-d H:i:s', $this->fields['dateValid'] . ' 00:00:00')
//          );
//        }
//        if ($this->fields['dateExpired'] !== false) {
//         $flyer->setDateExpired(
//             \DateTime::createFromFormat('Y-m-d H:i:s', $this->fields['dateExpired'] . ' 00:00:00')
//         );
//        }
//        return $flyer;
//    }
}