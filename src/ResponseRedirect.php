<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class ResponseRedirect implements ResponseInterface
{
    /**
     * @param string $target
     */
    public function __construct(
        protected string $target,
    )
    {}

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }
}
