<?php
declare(strict_types=1);

namespace App;

use App\Request\Dispatcher;

class App
{
    protected static $envVariables;

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
     * @param Response $response
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

    public function output()
    {
        // todo
    }
}