<?php
/**
 * Very small front controller / router for the MVC app.
 */
class App
{
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function run()
    {
        $url = $this->parseUrl();

        // Controller
        $controllerFile = __DIR__ . '/../controllers/' . ucfirst($url[0] ?? $this->controller) . '.php';
        $this->controller = $url[0] ?? $this->controller;
        array_shift($url);

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $this->controller = new $this->controller();
        } else {
            // default home
            require_once __DIR__ . '/../controllers/Home.php';
            $this->controller = new Home();
        }

        // Method
        $this->method = $url[0] ?? $this->method;
        if (!empty($url) && method_exists($this->controller, $this->method)) {
            array_shift($url);
        } else {
            $this->method = 'index';
        }

        // Params
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
