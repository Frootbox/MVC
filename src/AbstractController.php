<?php
/**
 *
 */

namespace Frootbox\MVC;

abstract class AbstractController
{
    protected $action;

    /**
     * @return ResponseInterface
     * @throws \Frootbox\Exceptions\RuntimeError
     */
    public function execute(): ResponseInterface
    {
        $action = $this->action . 'Action';

        if (!is_callable([ $this, $action ])) {
            throw new \Frootbox\Exceptions\RuntimeError('Missing controller action ' . $action);
        }

        $configuration = $this->container->get(\Frootbox\Config\Config::class);

        // Prime view layer
        $view = $this->container->get(\Frootbox\MVC\View::class);
        $view->assign('get', $this->container->get(\Frootbox\Http\Get::class));
        $view->assign('controller', $this);
        $view->assign('configuration', $configuration);

        // Perform controller action
        $response = $this->container->call([ $this, $action ]);

        if ($response instanceof \Frootbox\MVC\ResponseRedirect) {

            $target = $response->getTarget();

            if (substr($target, 0, 4) != 'http') {
                $target = SERVER_PATH . $target;
            }

            if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                die(json_encode([
                    'redirect' => $target,
                ]));
            }

            header('Location: ' . $target);
            exit;
        }

        if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {

            header('Content-Type: application/json; charset=utf-8');
            die(json_encode($response->getPayload()));
        }

        if (empty($response->getBody())) {

            $payload = $response->getPayload();

            // Render view file
            $viewFile = $this->getPath() . 'resources/private/views/' . ucfirst(substr($action, 0, -6)) . '.html.twig';

            if (!file_exists($viewFile)) {
                throw new \Frootbox\Exceptions\RuntimeError('View file missing ' . str_replace(CORE_DIR, '', $viewFile));
            }

            $html = $view->render($viewFile, $payload);

            $response->setBody($html);
        }

        return $response;
    }

    /**
     *
     */
    public function getActionUri(string $action, array $payload = null): string
    {
        $segments = explode('\\', get_class($this));
        array_shift($segments);
        array_shift($segments);
        array_pop($segments);

        $uri = SERVER_PATH . implode('/', $segments) . '/' . $action;

        if (!empty($payload)) {
            $uri .= '?' . http_build_query($payload);
        }

        return $uri;
    }

    /**
     *
     */
    abstract public function getPath(): string;

    /**
     *
     */
    public function getUri(string $controller, string $action, array $payload = null): string
    {
        $uri = SERVER_PATH . $controller . '/' . $action;

        if (!empty($payload)) {
            $uri .= '?' . http_build_query($payload);
        }

        return $uri;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;        
    }

    public function setContainer(\DI\Container $container): void
    {
        $this->container = $container;
    }

        

}
