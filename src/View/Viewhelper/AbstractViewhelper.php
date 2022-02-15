<?php
/**
 *
 */

namespace Frootbox\MVC\View\Viewhelper;

abstract class AbstractViewhelper
{
    /**
     *
     */
    public function __construct(
        protected \Frootbox\MVC\View $view,
    )
    { }
}
