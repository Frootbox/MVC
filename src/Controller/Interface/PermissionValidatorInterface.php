<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC\Controller\Interface;

interface PermissionValidatorInterface
{
    /**
     * @param string $accessKey
     * @return bool
     */
    public function validateAccessKey(string $accessKey): bool;
}
