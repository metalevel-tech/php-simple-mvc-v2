<?php

/**
 * Class Router
 * 
 * @author  Spas Z. Spasov <spas.z.spasov@metalevel.tech>
 * @package app\core
 * 
 * PHP MVC Framework, based on https://github.com/thecodeholic/php-mvc-framework
 */

namespace app\core;
use app\core\exceptions\NotFoundException;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];

    /**
     * Summary of __construct
     * @param  \app\core\Request $request
     * @param  \app\core\Response $response
     * @return Router
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Summary of get
     * @param  string $path
     * @param  mixed $callback
     * @return void
     */
    public function get(string $path, mixed $callback): void
    {
        $this->routes["get"][$path] = $callback;
    }

    /**
     * Summary of post
     * @param  string $path
     * @param  mixed $callback
     * @return void
     */
    public function post(string $path, mixed $callback): void
    {
        $this->routes["post"][$path] = $callback;
    }

    /**
     * Summary of resolve
     * 
     * @var string $path
     * @var string $method
     * @var mixed $callback
     * 
     * @return string
     */
    public function resolve(): string
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            throw new NotFoundException();
        }

        if (is_string($callback)) {
            $this->response->setStatusCode(200);
            return Application::$app->view->renderView($callback);
        }

        if (is_array($callback)) {
            // We need this annotation, otherwise the IDE doesn't know 
            // instance of which class is the $controller var... 
            // see the foreach() below... https://youtu.be/BHuXI5JE9Qo?t=970

            /** @var \app\core\Controller $controller */
            $controller =  new $callback[0]();

            Application::$app->controller = $controller;
            $controller->action = $callback[1]; // See the second argument in index.php...
            $callback[0] = Application::$app->controller;

            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }

        // https://www.php.net/manual/en/function.call-user-func.php
        return call_user_func($callback, $this->request, $this->response);
    }
}
