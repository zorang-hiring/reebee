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

    /**
     * @var ServiceContainer
     */
    protected $serviceContainer;

    public function __construct(Response $response, ServiceContainer $serviceContainer)
    {
        $this->response = $response;
        $this->serviceContainer = $serviceContainer;
    }

    public function dispatch(Request $request)
    {
        $dispatcher = new Dispatcher($request, $this->response, $this->serviceContainer);
        $dispatcher->dispatch();
    }

    public function print()
    {

    }
}