<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\Exception\ClassNotFound;

class PartialClass extends \Frootbox\MVC\Exception\ClassNotFound
{
    protected bool $isViewContext = true;
}
