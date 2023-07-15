<?php
/**
 *
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
