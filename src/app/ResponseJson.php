<?php
declare(strict_types=1);

namespace App;

class ResponseJson extends Response
{
    /**
     * @param mixed $body
     * @return self
     */
    public function setBody($body)
    {
        if ($body !== '' && $body !== null) {
            parent::setBody(json_encode($body));
        }
        return $this;
    }
}