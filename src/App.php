<?php
declare(strict_types=1);

namespace App;

use App\Request\Dispatcher;

class App
{
    /**
     * @var Response
     */
    protected $response;

    protected static $envVariables;

    /**
     * @var ServiceContainer
     */
    protected $serviceContainer;

    public function __construct(Response $response, ServiceContainer $serviceContainer, $envVariables = [])
    {
        $this->response = $response;
        $this->serviceContainer = $serviceContainer;
        self::$envVariables = $envVariables;
    }

    public function dispatch(Request $request)
    {
        $dispatcher = new Dispatcher($request, $this->response, $this->serviceContainer);
        $dispatcher->dispatch();
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

    public function output()
    {
        // todo
    }
}