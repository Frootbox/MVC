<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class Session
{
    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['userId']);
    }

    /**
     * @param Persistence\Entities\Interfaces\UserInterface $user
     * @return void
     */
    public function login(\Frootbox\MVC\Persistence\Entities\Interfaces\UserInterface $user): void
    {
        // Set session id
        $_SESSION['userId'] = $user->getId();

        // Set last login
        if ($user->hasColumn('lastLogin')) {
            $user->setLastLogin(date('Y-m-d H:i:s'));
            $user->save();
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $_SESSION['userId'] = null;

        session_destroy();
    }
}
