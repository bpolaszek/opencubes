<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class SimpleFilter extends Filter
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var FilterValue|null
     */
    private $value;

    /**
     * SimpleFilter constructor.
     * @param string           $field
     * @param FilterValue|null $value
     */
    public function __construct(string $field, FilterValue $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @param string $field
     * @param        $value
     * @return SimpleFilter
     */
    public static function createFromValue(string $field, $value): self
    {
        return new self($field, new FilterValue($value));
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value->getValue();
    }

    /**
     * @return FilterValue|null
     */
    public function getFilterValue(): ?FilterValue
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'simple';
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $output = [
            'type'       => $this->getType(),
            'field'      => $this->getField(),
            'value'      => $this->getFilterValue(),
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
