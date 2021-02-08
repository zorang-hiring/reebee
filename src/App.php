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

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function dispatch(Request $request)
    {
        $dispatcher = new Dispatcher($request, $this->response);
        $dispatcher->dispatch();
    }

    public function print()
    {

    }
}