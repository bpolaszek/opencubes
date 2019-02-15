<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;
use Psr\Http\Message\UriInterface;

final class FilterValue implements \JsonSerializable
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $applied;

    /**
     * @var int
     */
    private $count;

    /**
     * @var UriInterface
     */
    private $toggleUri;

    /**
     * FilterValue constructor.
     */
    public function __construct(string $key, $value, bool $applied = true, ?int $count = null, ?UriInterface $toggleUri = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->applied = $applied;
        $this->count = $count;
        $this->toggleUri = $toggleUri;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
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
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @return UriInterface
     */
    public function getToggleUri(): ?UriInterface
    {
        return $this->toggleUri;
    }

    /**
     * @param bool $applied
     */
    public function setApplied(bool $applied): void
    {
        $this->applied = $applied;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @param UriInterface $toggleUri
     */
    public function setToggleUri(UriInterface $toggleUri): void
    {
        $this->toggleUri = $toggleUri;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $output = [
            'key'        => $this->getKey(),
            'value'      => $this->getValue(),
            'is_applied' => $this->isApplied(),
            'count'      => $this->getCount(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
