<?php
/**
 *
 */

namespace Frootbox\MVC;

class Dispatcher
{
    protected $namespace;
    protected $cachepath;
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

            $config = require $path;
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

        if (!class_exists($controllerClass)) {
            throw new \Frootbox\Exceptions\RuntimeError('Missing controller ' . $controllerClass);
        }

        // Build controller
        $controller = new $controllerClass;
        $controller->setContainer($this->container);
        $controller->setAction($action);

        // Check controller requirements
        $controllerCache = $this->getControllerCache($controller);

        if ($controllerCache['access'] == 'Private' or empty($controllerCache['methods'][$action . 'Action']['access']) or $controllerCache['methods'][$action . 'Action']['access'] != 'Public') {

            $session = $this->container->get(\Frootbox\MVC\Session::class);

            if (!$session->isLoggedIn()) {

                $controllerClass = '\\' . $this->namespace . '\\Controller\\Session\\Controller';

                $get = $this->container->get(\Frootbox\Http\Get::class);
                $get->set('originalRequest', $request);

                // Build controller
                $controller = new $controllerClass;
                $controller->setContainer($this->container);
                $controller->setAction('login');
            }
        }

        return $controller;
    }

}
