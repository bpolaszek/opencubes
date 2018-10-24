<?php

namespace BenTools\OpenCubes\Component\Drilldown;

use ArrayIterator;

final class DrilldownComponent implements DrilldownComponentInterface
{
    /**
     * @var DimensionInterface[]
     */
    private $dimensions = [];

    /**
     * DrilldownComponent constructor.
     * @param DimensionInterface[] $dimensions
     */
    public function __construct(array $dimensions = [])
    {
        foreach ($dimensions as $dimension) {
            $this->add($dimension);
        }
    }

    public function clear(): void
    {
        $this->dimensions = [];
    }

    /**
     * @inheritDoc
     */
    public function add(DimensionInterface $dimension): void
    {
        $this->dimensions[$dimension->getField()] = $dimension;
    }

    /**
     * @inheritDoc
     */
    public function remove(DimensionInterface $dimension): void
    {
        unset($this->dimensions[$dimension->getField()]);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->dimensions;
    }

    /**
     * @inheritDoc
     */
    public function get(string $field): ?DimensionInterface
    {
        return $this->dimensions[$field] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $field): bool
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

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->dimensions);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'drilldown';
    }
}
