<?php

namespace BenTools\OpenCubes\Component\Sort;

final class Sort implements SortInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $availableDirections;

    /**
     * @var int
     */
    private $appliedDirection;

    /**
     * Sorting constructor.
     * @param string $field
     * @param array  $availableDirections
     */
    public function __construct(string $field, array $availableDirections = [self::SORT_ASC, self::SORT_DESC])
    {
        foreach ($availableDirections as $availableDirection) {
            if (!in_array($availableDirection, [self::SORT_ASC, self::SORT_DESC])) {
                throw new \InvalidArgumentException('Invalid direction');
            }
        }
        $this->field = $field;
        $this->availableDirections = $availableDirections;
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
    public function isApplied(int $direction = null): bool
    {
        if (null === $direction) {
            return null !== $this->appliedDirection;
        }
        return $direction === $this->appliedDirection;
    }

    /**
     * @param bool $applied
     */
    public function setAppliedDirection(?int $direction): void
    {
        if (!in_array($direction, [null, self::SORT_ASC, self::SORT_DESC])) {
            throw new \InvalidArgumentException('Invalid direction');
        }
        $this->appliedDirection = $direction;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDirections(): array
    {
        return $this->availableDirections;
    }
}
