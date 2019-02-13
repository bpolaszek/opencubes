<?php

namespace BenTools\OpenCubes\Component\Pager\Model;

use Psr\Http\Message\UriInterface;
use function BenTools\OpenCubes\stringify_uri;

final class PageSize implements \JsonSerializable
{
    /**
     * @var int
     */
    private $value;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var bool
     */
    private $applied;

    /**
     * PageSize constructor.
     */
    public function __construct(int $value, bool $applied, UriInterface $uri)
    {
        $this->value = $value;
        $this->applied = $applied;
        $this->uri = $uri;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isApplied(): bool
    {
        return $this->applied;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'size' => $this->value,
            'is_applied' => $this->isApplied(),
            'link' => stringify_uri($this->uri),
        ];
    }
}
