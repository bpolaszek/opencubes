<?php

namespace BenTools\OpenCubes\Component\BreakDown;

use BenTools\OpenCubes\Component\BreakDown\Model\Group;
use BenTools\OpenCubes\Component\ComponentInterface;

final class BreakDownComponent implements ComponentInterface, \IteratorAggregate, \Countable
{
    /**
     * @var Group[]
     */
    private $groups = [];

    /**
     * BreakDownComponent constructor.
     * @param Group[] $appliedGroups
     * @param Group[] $groups
     */
    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'breakdown';
    }

    /**
     * @param Group $group
     */
    public function add(Group $group): void
    {
        $this->groups[] = $group;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool
    {
        foreach ($this->groups as $group) {
            if ($group->getField() === $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $field
     * @return Group
     * @throws \InvalidArgumentException
     */
    public function get(string $field): Group
    {
        foreach ($this->groups as $group) {
            if ($group->getField() === $field) {
                return $group;
            }
        }

        throw new \InvalidArgumentException(sprintf('Group "%s" is not registered.', $field));
    }

    /**
     * @return Group[]
     */
    public function all(): array
    {
        return $this->groups;
    }

    /**
     * @return Group[]
     */
    public function getAppliedGroups(): array
    {
        return array_values(
            array_filter(
                $this->groups,
                function (Group $group) {
                    return $group->isApplied();
                }
            )
        );
    }

    /**
     * @return Group[]
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->groups);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->groups);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'groups' => $this->groups,
        ];
    }
}
