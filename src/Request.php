<?php
declare(strict_types=1);

namespace App;

class Request
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    protected $urlElements = [
        'path' => null,
        'query' => null
    ];

    protected $postData = [];

    protected $headers = [];

    protected $envVars = [];

    /**
     * @var string
     */
    protected $method;

    public function __construct($method, $url)
    {
        $this->urlElements['path'] = parse_url($url, PHP_URL_PATH);
        $this->urlElements['query'] = parse_url($url, PHP_URL_QUERY);
        $this->method = $method;
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->urlElements['path'];
    }

    /**
     * @return null|[]
     */
    public function getQuery()
    {
        $output = null;
        if ($this->urlElements['query'] !== null) {
            parse_str($this->urlElements['query'], $output);
        }

        return $output;
    }

    public function setPostData(array $postData)
    {
        $this->postData = $postData;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $envVarName
     * @return mixed|null
     */
    public function getEnvVar($envVarName)
    {
        return array_key_exists($envVarName, $this->envVars) ? $this->envVars[$envVarName] : null;
    }

    /**
     * @param array $envVars
     */
    public function setEnvVars(array $envVars)
    {
        $this->envVars = $envVars;
    }

    /**
     * @param string $headerName
     * @return string|null
     */
    public function getHeaderValue($headerName)
    {
        return array_key_exists($headerName, $this->headers) ? $this->headers[$headerName] : null;
    }

    /**
     * @return array
     */
    public function getPostData()
    {
        return $this->postData;
    }

    public function isPost()
    {
        return $this->method === self::METHOD_POST;
    }

    public function isGet()
    {
        return $this->method === self::METHOD_GET;
    }

    public function isDelete()
    {
        return $this->method === self::METHOD_DELETE;
    }

    public function isPatch()
    {
        return $this->method === self::METHOD_PATCH;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getEnvVar('APP_HTTP_BASE_URL');
    }
}