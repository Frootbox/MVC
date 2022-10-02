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
        if (!file_exists(CORE_DIR . 'resources/private/views/')) {
            throw new \Frootbox\Exceptions\RuntimeError('View folder missing ' . CORE_DIR . 'view/');
        }

        $loader = new \Twig\Loader\FilesystemLoader([
            CORE_DIR, CORE_DIR . 'resources/private/views/'
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
    public function getViewhelper(string $viewHelperClass): \Frootbox\MVC\View\Viewhelper\AbstractViewhelper
    {
        $viewHelperClass = str_replace('/', '\\', $viewHelperClass);

        return $this->container->get($viewHelperClass);
    }

    /**
     *
     */
    public function partial(string $partial, array $payload = []): string
    {
        // Build partial
        $segments = explode('/', $partial);
        $partialName = array_pop($segments);

        $partialClass = '\\' . implode('\\', $segments) . '\\' . $partialName . '\\Partial';

        if (!class_exists($partialClass)) {
            return '<div class="message danger">Partial ' . $partial . ' konnte nicht geladen werden.</div>';
        }

        $partial = new $partialClass(payload: $payload);

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
