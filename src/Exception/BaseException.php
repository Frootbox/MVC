<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\Exception;

class BaseException extends \Exception
{
    protected bool $isViewContext = false;

    /**
     * @return bool
     */
    public function isViewContext(): bool
    {
        return $this->isViewContext;
    }
}
