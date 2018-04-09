<?php

namespace BenTools\OpenCubes\Component\Drilldown;

final class DrilldownComponent implements DrilldownComponentInterface
{
    /**
     * @var DimensionInterface[]
     */
    private $dimensions = [];

    /**
     * DrilldownComponent constructor.
     * @param array $dimensions
     */
    public function __construct(array $dimensions = [])
    {
        $dimensions = (function (DimensionInterface ...$dimensions) {
            return $dimensions;
        })(...$dimensions);
        foreach ($dimensions as $dimension) {
            $this->dimensions[$dimension->getField()] = $dimension;
        }
    }

    /**
     * @inheritDoc
     */
    public function withDimension(DimensionInterface ...$dimensions): DrilldownComponentInterface
    {
        return new self($dimensions);
    }

    /**
     * @inheritDoc
     */
    public function withAddedDimension(DimensionInterface ...$dimensions): DrilldownComponentInterface
    {
        $clone = clone $this;
        foreach ($dimensions as $dimension) {
            $clone->dimensions[$dimension->getField()] = $dimension;
        }
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutDimension(DimensionInterface ...$dimensions): DrilldownComponentInterface
    {
        $clone = clone $this;
        foreach ($dimensions as $dimension) {
            unset($clone->dimensions[$dimension->getField()]);
        }
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    /**
     * @inheritDoc
     */
    public function getDimension(string $field): ?DimensionInterface
    {
        return $this->dimensions[$field] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasDimension(string $field): bool
    {
        return isset($this->dimensions[$field]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->dimensions);
    }
}
