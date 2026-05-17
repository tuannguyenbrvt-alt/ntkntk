<?php
class Router {
    private $routes = [];

    public function get($url, $action) {
        $this->addRoute('GET', $url, $action);
    }

    public function post($url, $action) {
        $this->addRoute('POST', $url, $action);
    }

    private function addRoute($method, $url, $action) {
        $this->routes[] = [
            'method' => $method,
            'url' => $url,
            'action' => $action
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'];
        
        // Loại bỏ subfolder nếu chạy ở localhost/ntkntk
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && $scriptName !== '\\') {
            $path = str_replace($scriptName, '', $path);
        }
        
        if (empty($path)) {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['url'] === $path && $route['method'] === $requestMethod) {
                $actionParts = explode('@', $route['action']);
                $controllerName = $actionParts[0];
                $methodName = $actionParts[1];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        return $controller->$methodName();
                    }
                }
                break;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 - Trang không tồn tại.";
    }
}
