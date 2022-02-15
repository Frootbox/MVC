<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class ResponseRedirect implements ResponseInterface
{
    /**
     *
     */
    public function __construct(
        protected string $target,
    )
    {}

    /**
     *
     */
    public function getTarget(): string
    {
        return $this->target;
    }
}
