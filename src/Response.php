<?php
/**
 * @author Jan Habbo Brüning <jan.habbo.bruening@gmail.com>
 */

namespace Frootbox\MVC;

class Response implements ResponseInterface
{
    protected $body;
    protected $payload = [];

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
     *
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * 
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     *
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

}
