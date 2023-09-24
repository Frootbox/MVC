<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\View;

class Front
{
    /**
     * @return array
     */
    public function getClearMessages(): array
    {
        $messages = $this->getMessages();

        unset($_SESSION['view']['front']['messages']);

        return $messages;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        if (empty($_SESSION['view']['front']['messages'])) {
            return [];
        }

        return $_SESSION['view']['front']['messages'];
    }

    /**
     * @return bool
     */
    public function hasMessages(): bool
    {
        if (empty($_SESSION['view']['front']['messages'])) {
            return false;
        }

        return count($_SESSION['view']['front']['messages']) > 0;
    }

    /**
     * @param string $message
     * @return void
     */
    public static function error(string $message): void
    {
        if (empty($_SESSION['view']['front']['messages'])) {
            $_SESSION['view']['front']['messages'] = [];
        }

        $_SESSION['view']['front']['messages'][] = [
            'message' => $message,
            'type' => 'error',
        ];
    }

    /**
     * @param string $message
     * @return void
     */
    public static function success(string $message): void
    {
        if (empty($_SESSION['view']['front']['messages'])) {
            $_SESSION['view']['front']['messages'] = [];
        }

        $_SESSION['view']['front']['messages'][] = [
            'message' => $message,
            'type' => 'success',
        ];
    }
}
