<?php
/**
 *
 */

namespace Frootbox\MVC;

class Dispatcher
{
    protected $namespace;
    protected $cachepath;
    protected $baseDir = null;
    protected $container;

    /**
     *
     */
    public function __construct(\DI\Container $container, array $options = null)
    {
        if (!empty($options['namespace'])) {
            $this->namespace = $options['namespace'];
        }

        if (!empty($options['cachepath'])) {
            $this->cachepath = $options['cachepath'];
        }

        if (!empty($options['baseDir'])) {
            $this->baseDir = $options['baseDir'];
        }

        $this->container = $container;

        $path = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
        $path .= '://' . $_SERVER['SERVER_NAME'];

        $path .= ($_SERVER['SERVER_PORT'] == 80) ? '/' : ':' . $_SERVER['SERVER_PORT'];

        $path .= str_replace('/index.php', '/', $_SERVER['SCRIPT_NAME']);

        define('SERVER_PATH', $path);
    }

    /**
     *
     */
    protected function buildControllerCache(): void
    {

        function getDirContents($dir, &$results = array()) {
            $files = scandir($dir);

            foreach ($files as $key => $value) {

                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

                if (!is_dir($path)) {
                    if ($value == 'Controller.php') {
                        $results[] = $path;
                    }
                }
                else if ($value != "." && $value != "..") {
                    getDirContents($path, $results);
                }
            }

            return $results;
        }

        $controllerFiles = getDirContents(CORE_DIR . 'src/Controller');
        $config = [];

        foreach ($controllerFiles as $file) {

            $path = str_replace('/', '\\', substr(str_replace(CORE_DIR . 'src/Controller/', '', $file), 0, -15));

            $class = '\\' . $this->namespace . '\\Controller\\' . $path . '\\Controller';

            $controllerConfig = [
                'timestamp' => $_SERVER['REQUEST_TIME'],
                'access' => 'Private',
            ];

            $reflectionClass = new \ReflectionClass($class);
            $comment = $reflectionClass->getDocComment();

            if (preg_match('#@access (.*?)\n#is', $comment, $match)) {

                if ($match[1] == 'Public') {
                    $controllerConfig['access'] = 'Public';
                }
            }

            if (preg_match('#@userlevel (.*?)\n#is', $comment, $match)) {
                $controllerConfig['userlevel'] = $match[1];
            }

            foreach ($reflectionClass->getMethods() as $method) {

                if (substr($method->getName(), -6) != 'Action') {
                    continue;
                }

                $comment = $method->getDocComment();

                if (preg_match('#@access (.*?)\n#is', $comment, $match)) {

                    if ($match[1] == 'Public') {
                        $controllerConfig['methods'][$method->getName()]['access'] = 'Public';
                    }
                }

                if (preg_match_all('#@route (.*?)\n#is', $comment, $matches)) {

                    if (!isset($controllerConfig['methods'][$method->getName()]['routes'])) {
                        $controllerConfig['methods'][$method->getName()]['routes'] = [];
                    }

                    foreach ($matches[1] as $route) {

                        $xroute = [];

                        // Parse route
                        $xroute['regex'] = '#^' . preg_replace('#\{([a-z]+)\}#i', '(?<\\1>.*?)', $route) . '$#';

                        // Parse variables
                        preg_match_all('#\{([a-z]+)\}#i', $route, $match);
                        $xroute['variables'] = $match[1];

                        $controllerConfig['methods'][$method->getName()]['routes'][] = $xroute;
                    }
                }
            }

            $key = $reflectionClass->getName();

            $config[$key] = $controllerConfig;

        }

        // Prepare cache
        $cacheFilePath = $this->cachepath . 'system/';
        if (!file_exists($cacheFilePath)) {
            $oldUmask = umask(0);
            mkdir($cacheFilePath,0777, true);
            umask($oldUmask);
        }

        // Write cache file
        $source = "<?php\n/**\n * @generated\n */\n\nreturn " . var_export($config, true) . ";\n";
        $path = $cacheFilePath . 'controller.php';
        file_put_contents($path, $source);
    }

    /**
     *
     */
    public function getControllerCache(\Frootbox\MVC\AbstractController $controller): array
    {
        $path = $this->cachepath . 'system/controller.php';

        if (!file_exists($path)) {

            if (!file_exists(dirname($path))) {
                $oldumask = umask(0);
                mkdir(dirname($path),0777, true);
                umask($oldumask);
            }

            file_put_contents($path, '<?php return [];');
        }

        $config = require $path;
        $key = get_class($controller);

        $controllerFile = $controller->getPath() . 'Controller.php';

        if (!isset($config[$key]) or filemtime($controllerFile) > $config[$key]['timestamp']) {

            $controllerConfig = [
                'timestamp' => $_SERVER['REQUEST_TIME'],
                'access' => 'Private',
            ];

            $reflectionClass = new \ReflectionClass(get_class($controller));
            $comment = $reflectionClass->getDocComment();

            if (preg_match('#@access (.*?)\n#is', $comment, $match)) {

                if ($match[1] == 'Public') {
                    $controllerConfig['access'] = 'Public';
                }
            }

            if (preg_match('#@userlevel (.*?)\n#is', $comment, $match)) {
                $controllerConfig['userlevel'] = $match[1];
            }

            foreach ($reflectionClass->getMethods() as $method) {

                if (substr($method->getName(), -6) != 'Action') {
                    continue;
                }

                $comment = $method->getDocComment();

                if (preg_match('#@access (.*?)\n#is', $comment, $match)) {

                    if ($match[1] == 'Public') {
                        $controllerConfig['methods'][$method->getName()]['access'] = 'Public';
                    }
                }
            }

            $config[$key] = $controllerConfig;

            $source = "<?php\n/**\n * @generated\n */\n\nreturn " . var_export($config, true) . ";\n";

            file_put_contents($path, $source);
        }

        return $config[$key];
    }

    /**
     *
     */
    public function getControllerFromRequest(): AbstractController
    {
        // Extract request
        if ($_SERVER['SCRIPT_NAME'] != '/index.php') {
            $request = str_replace(substr($_SERVER['SCRIPT_NAME'], 0, -9), '', $_SERVER['REQUEST_URI']);

            if (!empty($this->baseDir)) {
                $request = str_replace('/' . trim($this->baseDir, '/') . '/', '', $request);
            }
        }
        else {
            $request = trim($_SERVER['REQUEST_URI'], '/');
        }

        define('ORIGINAL_REQUEST', $request);


        $request = explode('?', $request)[0];

        if (empty($request)) {
            $request = 'Session/login';
        }

        $segments = explode('/', $request);

        if (count($segments) == 1) {
            $segments[] = 'index';
        }

        $action = array_pop($segments);
        $segments = array_map('ucfirst', $segments);

        $controllerClass = '\\' . $this->namespace . '\\Controller\\' . implode('\\', $segments) . '\\Controller';

        $get = $this->container->get(\Frootbox\Http\Get::class);


        // Controller class miss
        if (!class_exists($controllerClass)) {

            $this->buildControllerCache();

            // Check for custom routes
            $config = require $this->cachepath . 'system/controller.php';

            $orgControllerClass = $controllerClass;
            $controllerClass = null;

            foreach ($config as $class => $controllerCfg) {

                if (empty($controllerCfg['methods'])) {
                    continue;
                }

                foreach ($controllerCfg['methods'] as $method => $params) {

                    if (empty($params['routes'])) {
                        continue;
                    }

                    foreach ($params['routes'] as $route) {

                        if (preg_match($route['regex'], $request, $match)) {

                            foreach ($route['variables'] as $var) {
                                $get->set($var, $match[$var] ?? null);
                            }

                            $action = substr($method, 0, -6);
                            $controllerClass = $class;

                            break 3;
                        }
                    }
                }
            }

            if (empty($controllerClass)) {
                throw new \Frootbox\Exceptions\RuntimeError('Missing controller ' . $orgControllerClass);
            }
        }

        // Build controller
        $controller = new $controllerClass;
        $controller->setContainer($this->container);

        // Check action
        if (!method_exists($controller, $action . 'Action')) {
            throw new \Frootbox\Exceptions\RuntimeError('Missing action ' . $action . 'Action');
        }

        $controller->setAction($action);

        // Check controller requirements
        $controllerCache = $this->getControllerCache($controller);

        if ($controllerCache['access'] == 'Private' or empty($controllerCache['methods'][$action . 'Action']['access']) or $controllerCache['methods'][$action . 'Action']['access'] != 'Public') {

            $session = $this->container->get(\Frootbox\MVC\Session::class);

            if (!$session->isLoggedIn()) {

                // If request is ajax return error
                if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    throw new \Frootbox\Exceptions\AccessDenied();
                }

                $controllerClass = '\\' . $this->namespace . '\\Controller\\Session\\Controller';

                $get->set('originalRequest', $request);

                // Build controller
                $controller = new $controllerClass;
                $controller->setContainer($this->container);
                $controller->setAction('login');

                return $controller;
            }
        }

        if (!empty($controllerCache['userlevel'])) {

            $user = $this->container->get(\Frootbox\MVC\Persistence\Entities\Interfaces\UserInterface::class);

            if (!preg_match('#' . $controllerCache['userlevel'] . '#', $user->getAccess())) {
                throw new \Frootbox\Exceptions\AccessDenied();
            }
        }

        return $controller;
    }
}
