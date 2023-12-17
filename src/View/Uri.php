<?php
/**
 *
 */

namespace Frootbox\MVC\View;

class Uri
{
    /**
     * @param string $scheme
     * @param string|null $path
     * @param array|null $query
     * @param string|null $fragment
     */
    public function __construct(
        protected string $scheme = 'https',
        protected ?string $host = null,
        protected ?int $port = null,
        protected ?string $path = null,
        protected ?array $query = [],
        protected ?string $fragment = null,
    )
    { }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = $this->scheme . '://' . $this->host;

        if ($this->port) {
            $string .= ':' . $this->port;
        }

        if ($this->path) {
            $string .= '/' . $this->path;
        }

        if (!empty($this->query)) {
            $payload = array_filter($this->query);

            $string .= '?' . http_build_query($this->query);
        }

        return $string;
    }

    /**
     * @param string $parameter
     * @param string|int $value
     * @return void
     */
    public function setParameter(string $parameter, string|int $value): void
    {
        $this->query[$parameter] = $value;
    }

    /**
     * @param array $query
     * @return void
     */
    public function setQuery(array $query): void
    {
        $this->query = $query;
    }

    /**
     * @param string $url
     * @return self
     */
    public static function fromUrl(string $url): self
    {
        if (preg_match('#^(http|https)://(.*?)(?::([0-9]+))?\/(.*?)(?:\?(.*?))?$#', $url, $match)) {

            if (!empty($match[5])) {
                parse_str($match[5], $query);
            }

            return new self(
                scheme: $match[1],
                host: $match[2],
                port: !empty($match[3]) ? (int) $match[3] : null,
                path: $match[4],
                query: $query ?? [],
            );
        }
    }
}