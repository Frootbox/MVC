<?php
/**
 *
 */

namespace Frootbox\MVC;

class View
{
    protected $twig;

    /**
     *
     */
    public function __construct(
        protected \DI\Container $container,
    )
    {
        $viewfolder = CORE_DIR . 'resources/private/views/';

        if (!file_exists($viewfolder)) {
            throw new \Frootbox\Exceptions\RuntimeError('View folder missing ' . $viewfolder);
        }

        $loader = new \Twig\Loader\FilesystemLoader([
            CORE_DIR, $viewfolder
        ]);

        $this->twig = new \Twig\Environment($loader, [
            // 'cache' => CORE_DIR . 'files/cache/view',
        ]);
    }

    /**
     * 
     */
    public function addFilter(\Twig\TwigFilter $filter): void
    {
        $this->twig->addFilter($filter);
    }
    
    /**
     *
     */
    public function assign($var, $value): void
    {
        $this->twig->addGlobal($var, $value);
    }

    /**
     *
     */
    public function getContainer(): \DI\Container
    {
        return $this->container;
    }

    /**
     *
     */
    public function addPath(string $path): void
    {
        $loader = $this->twig->getLoader();
        $loader->addPath($path);
    }

    /**
     *
     */
    public function getPartial(string $partialClass, array $payload = []): \Frootbox\MVC\View\AbstractPartial
    {
        // Build partial
        $segments = explode('/', $partialClass);
        $partialName = array_pop($segments);

        $partialClass = '\\' . implode('\\', $segments) . '\\' . $partialName . '\\Partial';

        if (!class_exists($partialClass)) {
            throw new \Exception('Partial ' . $partialClass . ' not loadable');
        }

        $partial = new $partialClass(payload: $payload);

        // Prime rendering
        if (method_exists($partial, 'onInit')) {
            $this->container->call([ $partial, 'onInit' ]);
        }

        return $partial;
    }

    /**
     *
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
     *
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
     *
     */
    public function render(string $viewfile, array $variables = null): string
    {
        $variables['view'] = $this;

        return $this->twig->render(str_replace(CORE_DIR, '', $viewfile), $variables);
    }
}
