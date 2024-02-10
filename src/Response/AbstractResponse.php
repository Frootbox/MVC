<?php
/**
 *
 */

namespace Frootbox\MVC\Response;

abstract class AbstractResponse implements ResponseInterface, \Frootbox\MVC\ResponseInterface
{
    /**
     * @param array|null $payload
     * @param string|null $body
     */
    public function __construct(
        protected ?array $payload = [],
        protected ?string $body = null)
    { }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
