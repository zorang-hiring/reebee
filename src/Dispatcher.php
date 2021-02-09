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
        $pathElements = explode('/', trim($this->request->getPath(), '/'));
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

    /**
     * This app is completely REST it dispatches REST actions only
     *
     * @return null|string
     */
    protected function getActionName()
    {
        $pathElements = explode('/', trim($this->request->getPath(), '/'));
        if (count($pathElements) <= 1) {
            // standard reset actions
            switch ($this->request->getMethod()) {
                case Request::METHOD_GET:
                    return 'indexAction';
                    break;
                case Request::METHOD_POST:
                    return 'postAction';
                    break;
            }
            return null;
        }

        if (is_numeric($pathElements[1])) {
            // we consider that url path is: "something/<number>"
            $this->request->setPathParam('id', $pathElements[1]);
            // standard reset actions
            switch ($this->request->getMethod()) {
                case Request::METHOD_GET:
                    return 'getAction';
                    break;
                case Request::METHOD_POST:
                    return 'postAction';
                    break;
                case Request::METHOD_PATCH:
                    return 'patchAction';
                    break;
                case Request::METHOD_DELETE:
                    return 'deleteAction';
                    break;
            }
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