<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Sort\Model\Sort;
use Countable;
use IteratorAggregate;
use JsonSerializable;

final class SortComponent implements ComponentInterface, IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @var Sort[]
     */
    private $sorts = [];

    /**
     * SortComponent constructor.
     * @param array $sorts
     */
    public function __construct(array $sorts)
    {
        foreach ($sorts as $sort) {
            $this->add($sort);
        }
    }

    /**
     * @param Sort $sort
     */
    public function add(Sort $sort): void
    {
        $this->sorts[] = $sort;
    }

    /**
     * @param string $field
     * @param null   $direction
     * @return bool
     */
    public function has(string $field, $direction = null): bool
    {
        if (1 === func_num_args()) {
            foreach ($this->sorts as $sort) {
                if ($sort->getField() === $field) {
                    return true;
                }
            }

            return false;
        }

        foreach ($this->sorts as $sort) {
            if ($sort->getField() === $field && $sort->getDirection() === $direction) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $field
     * @param null   $direction
     * @return Sort[]
     */
    public function get(string $field, $direction = null): iterable
    {
        if (1 === func_num_args()) {
            foreach ($this->sorts as $sort) {
                if ($sort->getField() === $field) {
                    yield $sort;
                }
            }

            return;
        }

        foreach ($this->sorts as $sort) {
            if ($sort->getField() === $field && $sort->getDirection() === $direction) {
                yield $sort;
            }
        }
    }

    /**
     * @return Sort[]
     */
    public function all(): array
    {
        return $this->sorts;
    }

    /**
     * @return Sort[]
     */
    public function getAppliedSorts(): array
    {
        return array_values(
            array_filter(
                $this->sorts,
                function (Sort $sort) {
                    return $sort->isApplied();
                }
            )
        );
    }

    /**
     * @return Sort[]
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->sorts);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->sorts);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'sort';
    }

    /**
     * @param string      $field
     * @param string|null $direction
     * @return bool
     */
    public function isApplied(string $field, ?string $direction = null): bool
    {
        if (1 === func_num_args()) {
            foreach ($this->sorts as $sort) {
                if ($sort->getField() === $field && $sort->isApplied()) {
                    return true;
                }
            }

            return false;
        }

        foreach ($this->sorts as $sort) {
            if ($sort->getField() === $field && $sort->isApplied() && $sort->getDirection() === $direction) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $output = [];

        // Group by field
        $fields = array_values(
            array_unique(
                array_map(
                    function (Sort $sort): string {
                        return $sort->getField();
                    },
                    $this->sorts
                )
            )
        );

        foreach ($fields as $field) {
            $output[] = [
                'field'      => $field,
                'is_applied' => $this->isApplied($field),
                'directions' => array_values(
                    array_filter(
                        $this->sorts,
                        function (Sort $sort) use ($field): bool {
                            return $sort->getField() === $field;
                        }
                    )
                ),
            ];
        }

        return [
            'sorts' => $output,
        ];
    }
}
