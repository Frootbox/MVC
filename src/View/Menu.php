<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View;

class Menu
{
    /**
     * @param array $sections
     */
    public function __construct(
        public array $sections,
    )
    { }

    /**
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }
}
