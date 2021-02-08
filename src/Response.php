<?php
declare(strict_types=1);

namespace App;

class Response
{

    protected $status;

    /**
     * @var string
     */
    protected $body = '';

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getHeaders()
    {
        return [];
    }

    /**
     * @param string $body
     * @return self
     */
    public function setBody(string $body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }
}