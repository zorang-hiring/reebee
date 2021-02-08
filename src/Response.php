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

    protected $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Cache-Control: No-Cache'
    ];

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

    public function buildHeaders()
    {
        $headers = $this->headers;
        switch ($this->getStatus()) {
            case 404:
                $headers[]= 'HTTP/1.0 404 Not Found';
                break;
        }

        return $headers;
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