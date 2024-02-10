<?php
/**
 *
 */

namespace Frootbox\MVC\Response;

class Redirect
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
