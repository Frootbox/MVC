<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View;

abstract class AbstractPartial implements \Frootbox\MVC\View\PartialInterface
{
    /**
     *
     */
    public function __construct(
        protected $payload = []
    )
    { }

    /**
     *
     */
    public function __toString(): string
    {
        return get_class($this);
    }

    /**
     *
     */
    protected function requireParameter(string $parameterName): mixed
    {
        if (!isset($this->payload[$parameterName])) {
            throw new \Frootbox\Exceptions\InputMissing(sprintf('Der Parameter <b>%s</b> fehlt.', $parameterName));
        }

        return $this->payload[$parameterName];
    }

}
