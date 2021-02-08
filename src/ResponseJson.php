<?php
declare(strict_types=1);

namespace App;

class ResponseJson extends Response
{
    /**
     * @param mixed $body
     * @return $this
     */
    public function setBody($body)
    {
        parent::setBody(json_encode($body));
        return $this;
    }
}