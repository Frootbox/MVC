<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class Response implements ResponseInterface
{
    protected ?string $body = null;
    protected array $payload = [];

    /**
     *
     */
    public function __construct(array $payload = null)
    {
        if (!empty($payload)) {
            $this->payload = $payload;
        }
    }

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
