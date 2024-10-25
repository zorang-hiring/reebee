<?php
declare(strict_types=1);

namespace App\Form;

use App\App;
use App\Entity\Flyer;
use App\Entity\Page;

abstract class AbstractPageForm extends AbstractForm
{
    /**
     * @var array
     */
    protected $fields = [];

    public function __construct(array $data)
    {
        foreach (array_keys($this->fields) as $fieldName) {
            if (array_key_exists($fieldName, $data)) {
                $this->fields[$fieldName] = $data[$fieldName];
            }
        }
    }

    public function fillPage(Page $page)
    {
        if (array_key_exists('flyerID', $this->fields) && $this->fields['flyerID'] !== false) {
            $page->setFlyer(App::getEm()->getReference(Flyer::class, $this->fields['flyerID']));
        }
        if ($this->fields['dateValid'] !== false) {
            $page->setDateValid(
                \DateTime::createFromFormat('Y-m-d H:i:s', $this->fields['dateValid'] . ' 00:00:00')
            );
        }
        if ($this->fields['dateExpired'] !== false) {
            $page->setDateExpired(
                \DateTime::createFromFormat('Y-m-d H:i:s', $this->fields['dateExpired'] . ' 00:00:00')
            );
        }
        if (array_key_exists('pageNumber', $this->fields) && $this->fields['pageNumber'] !== false) {
            $page->setPageNumber($this->fields['pageNumber']);
        }
        return $page;
    }
}