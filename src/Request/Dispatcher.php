<?php
declare(strict_types=1);

namespace App\Request;

use App\FlyerController;
use App\Request;
use App\Response;

class Dispatcher
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function dispatch()
    {
        $controllerName = null;

        if ($this->request->getPath() === null) {
            $this->response->setStatus(404);
            return;
        }

        $controller = $this->getController();
        if (!$controller) {
            $this->response->setStatus(404);
            return;
        }

        $actionName = $this->getActionName();
        if (!$actionName) {
            $this->response->setStatus(404);
            return;
        }

        if (!method_exists($controller, $actionName)) {
            $this->response->setStatus(404);
            return;
        }

        $controller->{$actionName}($this->request, $this->response);
    }

    protected function getController()
    {
        $pathElements = explode(',', trim($this->request->getPath(), '/'));
        if (count($pathElements) === 0) {
            return null;
        }

        $controllerName =
            '\\App\\Controller\\'
            . str_replace(
            '-',
            '',
                ucwords(strtolower($pathElements[0]), '-')
            )
            . 'Controller';

        if (!class_exists($controllerName)) {
            return null;
        }

        return new $controllerName();
    }

    protected function getActionName()
    {
        $pathElements = explode(',', trim($this->request->getPath(), '/'));
        if (count($pathElements) <= 1) {
            return 'indexAction';
        }

        $action = str_replace(
            '-',
            '',
            ucwords(strtolower($pathElements[1]), '-')
        );

        if ($action === '') {
            return 'indexAction';
        }

        return $action . 'Action';
    }
}