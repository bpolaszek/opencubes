<?php

namespace BenTools\OpenCubes\Component\Sort;

use BenTools\OpenCubes\Component\ComponentInterface;
use BenTools\OpenCubes\Component\Sort\Model\Sort;

final class SortComponent implements ComponentInterface, \IteratorAggregate, \Countable
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
