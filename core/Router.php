<?php

/**
 * Class Router
 * 
 * @author  Spas Z. Spasov
 * @package PHP MVC Framework, based https://github.com/thecodeholic/php-mvc-framework
 */

namespace app\core;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];
    /**
     *  protected array $routes = [
     *      'get'  => [ '/' => 'callBack', '/contacts' => 'callBack' ],
     *      'post' => [ '/posts' => 'callBack', ... ]
     *  ];
     */


    /**
     * __construct
     *
     * @param  \app\core\Request $request
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * get
     *
     * @param  string $path
     * @param  function $callback
     * @return void
     */
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }
        
    /**
     * post
     *
     * @param  string $path
     * @param  function $callback
     * @return void
     */
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * resolve
     *
     * @return void
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            $this->response->setStatusCode(404);
            return $this->renderView("_HTTP404");
        }
        
        if (is_string($callback)) {
            $this->response->setStatusCode(200);
            return $this->renderView($callback);
        }

        return call_user_func($callback);
    }

    /**
     * renderView
     *
     * @param  string $view
     * @return string
     */
    public function renderView(string $view)
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
        
    /**
     * renderContent
     * 
     * It is like renderView, but instead of view,
     * it renders (HTML text) $viewContent directly.
     *
     * @param  string $viewContent
     * @return string
     */
    public function renderContent(string $viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * layoutContent
     *
     * @param  string $layout
     * @return string
     */
    protected function layoutContent(string $layout = "main")
    {
        ob_start();             // Start caching buffer, so nothing will be output to the browser
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();  // Stop caching buffer, and return the cached content
    }

    /**
     * renderOnlyView
     *
     * @param  string $view
     * @return string
     */
    protected function renderOnlyView(string $view = "home")
    {
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}
