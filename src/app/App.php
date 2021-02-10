<?php
declare(strict_types=1);

namespace App;

use Doctrine\ORM\EntityManagerInterface;

class App
{
    protected static $envVariables;

    /**
     * @var EntityManagerInterface
     */
    protected static $em;

    /**
     * @var ServiceContainer
     */
    protected $serviceContainer;

    public function __construct(ServiceContainer $serviceContainer, $envVariables = [])
    {
        $this->serviceContainer = $serviceContainer;
        self::$envVariables = $envVariables;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request)
    {
        $dispatcher = new Dispatcher($request, $this->serviceContainer);
        return $dispatcher->dispatch();
    }

    /**
     * @param string $envVarName
     * @return mixed|null
     */
    public static function getEnv($envVarName)
    {
        $envVars = self::$envVariables;
        return array_key_exists($envVarName, $envVars) ? $envVars[$envVarName] : null;
    }

    public function output(Request $request)
    {
        $response = $this->dispatch($request);

        // remove any previously printed string which can break json etc.
        if (ob_get_contents()) {
            ob_end_clean();
        }

        // clean eventual previously added headers
        header_remove();

        // send headers
        header("Access-Control-Allow-Origin: *");
        header("Cache-Control: No-Cache");
        if ($response instanceof ResponseJson) {
            header("Content-Type: application/json; charset=utf-8");
            header("Accept: application/json");
        } else {
            header("Content-Type: text/html; charset=utf-8");
            header("Accept: text/html");
        }
        http_response_code($response->getStatus());

        echo $response->getBody();

        // will assure nothing is added after
        exit();
    }

    /**
     * @return EntityManagerInterface
     */
    public static function getEm()
    {
        return self::$em;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public static function setEm(EntityManagerInterface $em)
    {
        self::$em = $em;
    }
}