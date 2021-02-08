<?php
declare(strict_types=1);

namespace App;

class Request
{
    protected $urlElements = [
        'path' => null,
        'query' => null
    ];

    public function __construct($url)
    {
        $this->urlElements['path'] = parse_url($url, PHP_URL_PATH);
        $this->urlElements['query'] = parse_url($url, PHP_URL_QUERY);

    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->urlElements['path'];
    }

    public function getQuery()
    {
        $output = null;
        if ($this->urlElements['query'] !== null) {
            parse_str($this->urlElements['query'], $output);
        }

        return $output;
    }
}