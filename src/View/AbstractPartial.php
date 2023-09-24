<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View;

abstract class AbstractPartial implements \Frootbox\MVC\View\PartialInterface
{
    /**
     * @param $payload
     */
    public function __construct(
        protected $payload = []
    )
    { }
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return get_class($this);
    }

    /**
     * @param string $parameterName
     * @return bool
     */
    protected function hasParameter(string $parameterName): bool
    {
        return isset($this->payload[$parameterName]);
    }

    /**
     * @param string $parameterName
     * @return mixed
     * @throws \Frootbox\Exceptions\InputMissing
     */
    protected function requireParameter(string $parameterName): mixed
    {
        if (!isset($this->payload[$parameterName])) {
            throw new \Frootbox\Exceptions\InputMissing(sprintf('Der Parameter <b>%s</b> fehlt.', $parameterName));
        }

        return $this->payload[$parameterName];
    }
}
