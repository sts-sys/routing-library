<?php
namespace sts\routes;

class Router
{
    protected array $routes = []; 
    protected array $cache = [];
    protected array $middleware = [];
    protected string $routeGroup = '';
    protected array $namedRoutes = [];
    protected string $routePrefix = '';
    protected array $middlewareGroups = [];
    protected bool $debug = false;

    public function __construct()
    {
        $this->loadCache();
        $this->initializeTries();
    }

    public function add($methods, $uri, $action)
    {
        $methods = (array) $methods;
        $uri = $this->routePrefix . $this->routeGroup . $uri;

        foreach ($methods as $method) {
            $currentNode = $this->routes[$method];
            $segments = explode('/', trim($uri, '/'));

            foreach ($segments as $segment) {
                if (!isset($currentNode->children[$segment])) {
                    $currentNode->children[$segment] = new TrieNode();
                }
                $currentNode = $currentNode->children[$segment];
            }

            $currentNode->isEndOfRoute = true;
            $currentNode->routeData = [
                'action' => $action,
                'middleware' => $this->middleware
            ];
        }

        $this->middleware = []; // Reset middleware after adding
        return $this; // For chaining
    }

    public function get($uri, $action)
    {
        return $this->add('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        return $this->add('POST', $uri, $action);
    }

    public function put($uri, $action)
    {
        return $this->add('PUT', $uri, $action);
    }

    public function delete($uri, $action)
    {
        return $this->add('DELETE', $uri, $action);
    }

    public function middleware($middleware)
    {
        $this->middleware[] = $middleware;
        return $this; // Permite chaining-ul
    }

    public function name($name)
    {
        $lastRouteKey = array_key_last($this->routes);
        $this->namedRoutes[$name] = $lastRouteKey;
        return $this; // Permite chaining-ul
    }

    public function routeExists($name)
    {
        return isset($this->namedRoutes[$name]);
    }

    public function getRouteByName($name)
    {
        if ($this->routeExists($name)) {
            $routeKey = $this->namedRoutes[$name];
            foreach ($this->routes as $method => $routeTree) {
                if (isset($routeTree[$routeKey])) {
                    return $routeTree[$routeKey];
                }
            }
        }
        return null;
    }

    public function routeUrl($name, $params = [])
    {
        if (!$this->routeExists($name)) {
            throw new \Exception("Route name '$name' not defined.");
        }

        $routeKey = $this->namedRoutes[$name];
        $uri = '';

        foreach ($this->routes as $method => $routeTree) {
            if (isset($routeTree[$routeKey])) {
                $uri = $routeTree[$routeKey]->uri;
                break;
            }
        }

        foreach ($params as $key => $value) {
            $uri = str_replace("{" . $key . "}", $value, $uri);
        }

        return $uri;
    }

    public function group($prefix, $callback)
    {
        $previousGroup = $this->routeGroup;
        $this->routeGroup .= $prefix;
        $callback($this);
        $this->routeGroup = $previousGroup; // Restore previous group
    }

    public function defineMiddlewareGroup($name, $middlewares)
    {
        $this->middlewareGroups[$name] = $middlewares;
    }

    public function middlewareGroup($group)
    {
        if (isset($this->middlewareGroups[$group])) {
            foreach ($this->middlewareGroups[$group] as $middleware) {
                $this->middleware($middleware);
            }
        }
        return $this;
    }

    public function enableDebug()
    {
        $this->debug = true;
    }

    public function log($message)
    {
        if ($this->debug) {
            error_log($message);
        }
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $routeMatch = $this->match($method, $uri);
        if (!$routeMatch) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        $routeData = $routeMatch['route'];
        $params = $routeMatch['params'];

        foreach ($routeData['middleware'] as $middleware) {
            $middlewareInstance = new $middleware();
            if (!$middlewareInstance->handle()) {
                return; // Dacă un middleware oprește execuția
            }
        }

        $action = $routeData['action'];

        if (is_callable($action)) {
            call_user_func_array($action, $params);
        } elseif (is_string($action)) {
            $this->executeControllerAction($action, $params);
        }
    }

    protected function executeControllerAction($action, $params)
    {
        list($controller, $method) = explode('@', $action);
        $controllerInstance = new $controller;
        call_user_func_array([$controllerInstance, $method], $params);
    }

    protected function initializeTries()
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            $this->routes[$method] = new TrieNode();
        }
    }

    protected function match($method, $uri)
    {
        $segments = explode('/', trim($uri, '/'));
        $currentNode = $this->routes[$method];

        $params = [];

        foreach ($segments as $segment) {
            if (isset($currentNode->children[$segment])) {
                $currentNode = $currentNode->children[$segment];
            } elseif ($parameterizedChild = $this->findParameterizedChild($currentNode->children)) {
                $currentNode = $parameterizedChild;
                $params[] = $segment;
            } else {
                return null;
            }
        }

        if ($currentNode->isEndOfRoute) {
            $this->cacheRoute($method, $uri, $currentNode->routeData, $params);
            return ['route' => $currentNode->routeData, 'params' => $params];
        }

        return null;
    }

    protected function findParameterizedChild($children)
    {
        foreach ($children as $key => $child) {
            if (strpos($key, '{') === 0 && strpos($key, '}') === strlen($key) - 1) {
                return $child;
            }
        }
        return null;
    }

    private function cacheRoute($method, $uri, $routeData, $params)
    {
        $cacheKey = $method . $uri;
        $this->cache[$cacheKey] = ['route' => $routeData, 'params' => $params];
        $this->saveCache();
    }

    protected function loadCache()
    {
        if (file_exists('cache/routes.php')) {
            $this->cache = include 'cache/routes.php';
        }
    }

    protected function saveCache()
    {
        file_put_contents('cache/routes.php', '<?php return ' . var_export($this->cache, true) . ';');
    }
}
