<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View\Viewhelper;

abstract class AbstractViewhelper
{
    /**
     * @param \Frootbox\MVC\View $view
     */
    public function __construct(
        protected \Frootbox\MVC\View $view,
    )
    { }
}
