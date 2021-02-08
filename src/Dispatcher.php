<?php
declare(strict_types=1);

namespace App;

use App\Controller\AbstractController;

class Dispatcher
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ServiceContainer
     */
    protected $serviceContainer;

    public function __construct(Request $request, ServiceContainer $serviceContainer)
    {
        $this->request = $request;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return Response
     */
    public function dispatch()
    {
        $controllerName = null;

        if ($this->request->getPath() === null) {
            return (new Response())->setStatus(404);
        }

        $controller = $this->getController();
        if (!$controller) {
            return (new Response())->setStatus(404);
        }

        $actionName = $this->getActionName();
        if (!$actionName) {
            return (new Response())->setStatus(404);
        }

        if (!method_exists($controller, $actionName)) {
            return (new Response())->setStatus(404);
        }

        $response = $controller->{$actionName}($this->request);
        if (!$response instanceof Response) {
            throw new \RuntimeException('Controller action should return Response');
        }
        return $response;
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

        /** @var AbstractController $controller */
        $controller = new $controllerName();
        $controller->setServiceContainer($this->serviceContainer);

        return $controller;
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