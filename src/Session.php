<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class Session
{
    /**
     *
     */
    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['userId']);
    }

    /**
     *
     */
    public function login(\Frootbox\MVC\Persistence\Entities\Interfaces\UserInterface $user): void
    {
        $_SESSION['userId'] = $user->getId();
    }

    /**
     *
     */
    public function logout(): void
    {
        $_SESSION['userId'] = null;

        session_destroy();
    }
}
