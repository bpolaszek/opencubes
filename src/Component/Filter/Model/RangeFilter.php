<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class RangeFilter extends Filter
{
    /**
     * @var string
     */
    private $field;
    private $left;
    private $right;

    /**
     * RangeFilter constructor.
     * @param string $field
     * @param        $left
     * @param        $right
     */
    public function __construct(string $field, $left = null, $right = null)
    {
        $this->field = $field;
        $this->left = $left;
        $this->right = $right;
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
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @inheritDoc
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @inheritDoc
     */
    public function isInRange($value): bool
    {
        return $value >= $this->left
            && $value <= $this->right;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'range';
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $output = [
            'type'       => $this->getType(),
            'field'      => $this->getField(),
            'left'       => $this->getLeft(),
            'right'      => $this->getRight(),
            'is_applied' => $this->isApplied(),
            'is_negated' => $this->isNegated(),
        ];

        if ($this->isApplied()) {
            $output['unset_link'] = stringify_uri($this->getToggleUri());
        } else {
            $output['link'] = stringify_uri($this->getToggleUri());
        }

        return $output;
    }
}
