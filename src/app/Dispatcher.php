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

        // define controller
        if ($this->request->getPath() === null || !$controller = $this->getController()) {
            return (new Response())->setStatus(404);
        }

        // define action
        $actionName = $this->getActionName();
        if (!$actionName || !method_exists($controller, $actionName)) {
            return (new Response())->setStatus(404);
        }

        // dispatch controller action
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
            // if path contains only one level
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

        // if second level is not empty value
        if (!empty($pathElements[1])) {

            // If 3rd path element is defined e.g. "/flyers/4/pages" (in this case pages),
            // then action will be "getPagesAction" or "postPagesAction"... of FlyersController.
            // If 3rd path element is NOT defined e.g. "/flyers" or "/flyers/4",
            // then action will be "getAction" or "postAction"... of FlyersController.

            // define suffix
            $actionSuffix = empty($pathElements[2])
                ? 'Action'
                : ucfirst(strtolower($pathElements[2])) . 'Action';

            // we consider that path is: "something/<ID>*" according to REST standards
            // so add ID param to the Request
            $this->request->setPathParam('id', $pathElements[1]);

            switch ($this->request->getMethod()) {
                case Request::METHOD_GET:
                    return 'get' . $actionSuffix;
                    break;
                case Request::METHOD_POST:
                    return 'post' . $actionSuffix;
                    break;
                case Request::METHOD_PATCH:
                    return 'patch' . $actionSuffix;
                    break;
                case Request::METHOD_DELETE:
                    return 'delete' . $actionSuffix;
                    break;
            }
        }

        return null;
    }
}