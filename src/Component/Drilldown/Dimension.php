<?php

namespace BenTools\OpenCubes\Component\Drilldown;

final class Dimension implements DimensionInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * Dimension constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }
}
