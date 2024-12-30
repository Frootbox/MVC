<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class View
{
    protected \Twig\Environment $twig;

    /**
     * @param \DI\Container $container
     * @throws \Frootbox\Exceptions\RuntimeError
     */
    public function __construct(
        protected \DI\Container $container,
    )
    {
        $viewFolder = CORE_DIR . 'resources/private/views/';

        if (!file_exists($viewFolder)) {
            throw new \Frootbox\Exceptions\RuntimeError('View folder missing ' . $viewFolder);
        }

        $loader = new \Twig\Loader\FilesystemLoader([
            CORE_DIR, $viewFolder
        ]);

        $this->twig = new \Twig\Environment($loader, [
            // 'cache' => CORE_DIR . 'files/cache/view',
        ]);
    }

    /**
     * @param \Twig\TwigFilter $filter
     * @return void
     */
    public function addFilter(\Twig\TwigFilter $filter): void
    {
        $this->twig->addFilter($filter);
    }

    /**
     * @param $var
     * @param $value
     * @return void
     */
    public function assign($var, $value): void
    {
        $this->twig->addGlobal($var, $value);
    }

    /**
     * @return \DI\Container
     */
    public function getContainer(): \DI\Container
    {
        return $this->container;
    }

    /**
     * @param string $path
     * @return void
     */
    public function addPath(string $path): void
    {
        $loader = $this->twig->getLoader();
        $loader->addPath($path);
    }

    /**
     * @param string $partialClass
     * @param array $payload
     * @return View\AbstractPartial
     * @throws \Exception
     */
    public function getPartial(string $partialClass, array $payload = []): \Frootbox\MVC\View\AbstractPartial
    {
        // Check partial class
        if (!class_exists($partialClass)) {

            $segments = explode('/', $partialClass);
            $partialName = array_pop($segments);

            $partialClass = '\\' . implode('\\', $segments) . '\\' . $partialName . '\\Partial';

            if (!class_exists($partialClass)) {
                throw new \Exception('Partial ' . $partialClass . ' not loadable');
            }
        }

        // Build partial
        $partial = new $partialClass(payload: $payload);

        // Prime rendering
        if (method_exists($partial, 'onInit')) {
            $this->container->call([ $partial, 'onInit' ]);
        }

        return $partial;
    }

    /**
     * @param string $viewHelperClass
     * @return View\Viewhelper\AbstractViewhelper
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getViewhelper(string $viewHelperClass): \Frootbox\MVC\View\Viewhelper\AbstractViewhelper
    {
        $viewHelperClass = str_replace('/', '\\', $viewHelperClass);

        if (!class_exists($viewHelperClass)) {
            throw new \Exception('Viewhelper class ' . $viewHelperClass . ' missing.');
        }

        return $this->container->get($viewHelperClass);
    }

    /**
     * @param string $partialClass
     * @param array $payload
     * @return string
     */
    public function partial(string $partialClass, array $payload = []): string
    {
        try {
            // Obtain partial
            $partial = $this->getPartial($partialClass, $payload);
        }
        catch (\Exception $e) {
            return '<div class="message danger">Partial ' . $partialClass . ' konnte nicht geladen werden.</div>';
        }

        // Prime rendering
        if (method_exists($partial, 'onBeforeRendering')) {

            try {
                $xpayload = $this->container->call([ $partial, 'onBeforeRendering' ]);

                if ($xpayload === null) {
                    return (string) null;
                }

                $payload = array_merge($payload, $xpayload);
            }
            catch ( \Exception $e ) {
                return '<div class="message danger">' . $e->getMessage() . '</div>';
            }
        }

        // Render partial
        $payload['partial'] = $partial;
        $viewFile = $partial->getPath() . 'resources/private/views/Partial.html.twig';

        $source = $this->render($viewFile, $payload);

        return $source;
    }

    /**
     * @param string $viewfile
     * @param array|null $variables
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $viewfile, array $variables = null): string
    {
        $variables['view'] = $this;

        return $this->twig->render(str_replace(CORE_DIR, '', $viewfile), $variables);
    }
}
