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

    protected $headers = [];

    protected $pathParams = [];

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Request constructor.
     * @param string $method GET, POST, PUT, DELETE ...
     * @param string $uri E.g.: '/a/b/?c=d'
     */
    public function __construct($method, $uri)
    {
        $this->urlElements['path'] = parse_url($uri, PHP_URL_PATH);
        $this->urlElements['query'] = parse_url($uri, PHP_URL_QUERY);
        $this->method = $method;
    }

    public function setContent($content)
    {
        $this->content =  $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isJson()
    {
        $headerV = $this->getHeaderValue('Content-Type');
        if ($headerV && strtolower($headerV) === 'application/json') {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->isJson()) {
            $data = json_decode($this->getContent(), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
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

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $headerName
     * @return string|null
     */
    public function getHeaderValue($headerName)
    {
        return array_key_exists($headerName, $this->headers) ? $this->headers[$headerName] : null;
    }

    public function getMethod()
    {
        return $this->method;
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

    public function setPathParam($name, $value)
    {
        $this->pathParams[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return self
     */
    public function getPathParam($name)
    {
        if (array_key_exists($name, $this->pathParams)) {
            return $this->pathParams[$name];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getEnvVar('APP_HTTP_BASE_URL');
    }
}