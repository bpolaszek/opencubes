<?php

namespace BenTools\OpenCubes\Component\Sort;

final class Sort implements SortInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var int
     */
    private $direction;

    /**
     * Sorting constructor.
     * @param string $field
     * @param int    $direction
     */
    public function __construct(string $field, int $direction = self::SORT_ASC)
    {

        if (!in_array($direction, [self::SORT_ASC, self::SORT_DESC])) {
            throw new \InvalidArgumentException(sprintf('Direction must be SORT_ASC or SORT_DESC constants.'));
        }

        $this->field = $field;
        $this->direction = $direction;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @inheritDoc
     */
    public function isAsc(): bool
    {
        return self::SORT_ASC === $this->direction;
    }

    /**
     * @inheritDoc
     */
    public function isDesc(): bool
    {
        return self::SORT_DESC === $this->direction;
    }

    /**
     * @return Sort
     */
    public function invert(): self
    {
        return new self($this->field, $this->isAsc() ? self::SORT_DESC : self::SORT_ASC);
    }
}
