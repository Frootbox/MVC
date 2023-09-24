<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\Ids;

class Scanner
{
    /**
     * @param string $uri
     * @return bool
     */
    public function containsMaliciousLoginUri(string $uri): bool
    {
        $uri = trim($uri, '/');
        $checks = [
            '^Login\.php',
        ];

        foreach ($checks as $checkRegex) {

            if (preg_match('#' .$checkRegex . '#i', $uri, $match)) {
                return true;
            }
        }

        return false;
    }
}
