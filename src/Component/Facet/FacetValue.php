<?php

namespace BenTools\OpenCubes\Component\Facet;

final class FacetValue implements FacetValueInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var null|string
     */
    private $value;

    /**
     * @var int|null
     */
    private $numFound;

    /**
     * FacetValue constructor.
     */
    public function __construct(string $identifier, ?string $value = null, ?int $numFound = null)
    {
        $this->identifier = $identifier;
        $this->value = $value;
        $this->numFound = $numFound;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getNumFound(): ?int
    {
        return $this->numFound;
    }
}
