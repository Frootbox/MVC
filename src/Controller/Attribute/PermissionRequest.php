<?php
/**
 *
 */

namespace Frootbox\Mvc\Controller\Attribute;

#[\Attribute]
class PermissionRequest
{
    public function __construct(
        protected ?string $accessKey = null,
    )
    { }

    public function getAccessKey(): ?string
    {
        return $this->accessKey;
    }

    public function hasAccessKey(): bool
    {
        return $this->accessKey !== null;
    }
}