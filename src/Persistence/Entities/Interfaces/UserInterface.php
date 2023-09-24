<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\Persistence\Entities\Interfaces;

interface UserInterface
{
    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void;
}
