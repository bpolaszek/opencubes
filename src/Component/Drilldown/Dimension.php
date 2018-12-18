<?php

namespace BenTools\OpenCubes\Component\Drilldown;

final class Dimension implements DimensionInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var bool
     */
    private $applied;

    /**
     * Dimension constructor.
     * @param string $field
     */
    public function __construct(string $field, bool $applied = false)
    {
        $this->field = $field;
        $this->applied = $applied;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }

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
}
