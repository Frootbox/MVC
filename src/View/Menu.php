<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View;

class Menu
{
    /**
     *
     */
    public function __construct(
        public array $sections,
    )
    {

    }

    /**
     *
     */
    public function getSections(): array
    {
        return $this->sections;
    }
}
