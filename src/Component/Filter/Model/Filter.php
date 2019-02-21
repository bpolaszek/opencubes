<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use Psr\Http\Message\UriInterface;

abstract class Filter implements \JsonSerializable
{
    public const SATISFIED_BY_ANY = 'ANY';
    public const SATISFIED_BY_ALL = 'ALL';

    /**
     * @var bool
     */
    private $applied = true;

    /**
     * @var bool
     */
    private $negated = false;

    /**
     * @var UriInterface
     */
    private $toggleUri;

    /**
     * @return bool
     */
    public function isApplied(): bool
    {
        return $this->applied;
    }

    /**
     * @param bool $applied
     */
    public function setApplied(bool $applied): void
    {
        $this->applied = $applied;
    }

    /**
     * @return bool
     */
    public function isNegated(): bool
    {
        return $this->negated;
    }

    /**
     * @return Filter
     */
    public function negate(): Filter
    {
        $clone = clone $this;
        $clone->negated = !$this->negated;
        return $clone;
    }

    /**
     * @return UriInterface
     */
    public function getToggleUri(): ?UriInterface
    {
        return $this->toggleUri;
    }

    /**
     * @param UriInterface $toggleUri
     */
    public function setToggleUri(UriInterface $toggleUri): void
    {
        $this->toggleUri = $toggleUri;
    }

    /**
     * @return string
     */
    abstract public function getField(): string;

    /**
     * @return string
     */
    abstract public function getType(): string;

    /**
     * @inheritDoc
     */
    abstract public function jsonSerialize(): array;
}
