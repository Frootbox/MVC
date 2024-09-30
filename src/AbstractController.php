<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

abstract class AbstractController
{
    protected ?\DI\Container $container = null;
    protected ?string $action;
    protected ?string $overrideViewFile = null;

    /**
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
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

        if ($response instanceof \Frootbox\MVC\Response\Redirect) {

            $target = $response->getTarget();

            if (substr($target, 0, 4) != 'http') {
                $target = SERVER_PATH . $target;
            }

            if (!empty($_SERVER['HTTP_ACCEPT']) and strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                die(json_encode([
                    'redirect' => $target,
                ]));
            }

            header('Location: ' . $target);
            exit;
        }

        // Switch response to json if requested
        if (!empty($_SERVER['HTTP_ACCEPT']) and str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {

            header('Content-Type: application/json; charset=utf-8');
            die(json_encode($response->getPayload()));
        }

        if (empty($response->getBody())) {

            $payload = $response->getPayload();

            // Render view file
            if ($this->overrideViewFile === null) {
                $viewFile = ucfirst(substr($action, 0, -6)) . '.html.twig';
            }
            else {
                $viewFile = ucfirst($this->overrideViewFile) . '.html.twig';
            }

            $viewFile = $this->getPath() . 'resources/private/views/' . $viewFile;

            if (!file_exists($viewFile)) {
                throw new \Frootbox\Exceptions\RuntimeError('View file missing ' . str_replace(CORE_DIR, '', $viewFile));
            }

            $html = $view->render($viewFile, $payload);

            $response->setBody($html);
        }

        return $response;
    }

    /**
     * Get controllers current action
     *
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @param array|null $payload
     * @return string
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
     * @return string
     */
    public function getControllerName(): string
    {
        preg_match('#\\\\Controller\\\\(.*?)\\\\Controller$#', get_class($this), $match);

        return str_replace('\\', '/', $match[1]);
    }

    /**
     * @return string
     */
    abstract public function getPath(): string;

    /**
     * @param string $controller
     * @param string $action
     * @param array|null $payload
     * @return View\Uri
     */
    public function getSubUri(string $controller, string $action = 'index', array $payload = null): View\Uri
    {
        $segments = explode('\\', get_class($this));
        array_shift($segments);
        array_shift($segments);
        array_pop($segments);

        $uri = SERVER_PATH . implode('/', $segments) . '/' . $controller . '/' . $action;

        $uri = View\Uri::fromUrl($uri);

        if (!empty($payload)) {
            $uri->setQuery($payload);
        }

        return $uri;
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array|null $payload
     * @return string
     */
    public function getUri(string $controller, string $action = 'index', array $payload = null): View\Uri
    {
        $uri = View\Uri::fromUrl(SERVER_PATH . $controller . '/' . $action);

        if (!empty($payload)) {
            $uri->setQuery($payload);
        }

        return $uri;
    }

    /**
     * @param array $payload
     * @return string
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Frootbox\Exceptions\RuntimeError
     */
    public function render(array $payload = []): string
    {
        // Render view file
        if ($this->overrideViewFile === null) {
            $viewFile = ucfirst($this->action) . '.html.twig';
        }
        else {
            $viewFile = ucfirst($this->overrideViewFile) . '.html.twig';
        }

        $viewFile = $this->getPath() . 'resources/private/views/' . $viewFile;

        if (!file_exists($viewFile)) {
            throw new \Frootbox\Exceptions\RuntimeError('View file missing ' . str_replace(CORE_DIR, '', $viewFile));
        }

        $view = $this->container->get(\Frootbox\MVC\View::class);
        return $view->render($viewFile, $payload);
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;        
    }

    /**
     * @param \DI\Container $container
     * @return void
     */
    public function setContainer(\DI\Container $container): void
    {
        $this->container = $container;
    }
}
