<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class StringMatchFilter extends Filter
{
    const LIKE = 'LIKE';
    const STARTS_WITH = 'STARTS_WITH';
    const ENDS_WITH = 'ENDS_WITH';
    const REGEXP = 'REGEXP';

    const OPERATORS = [
        self::LIKE,
        self::STARTS_WITH,
        self::ENDS_WITH,
        self::REGEXP,
    ];

    /**
     * @var string
     */
    private $field;

    /**
     * @var FilterValue
     */
    private $value;

    /**
     * @var string
     */
    private $operator;

    /**
     * StringMatchFilter constructor.
     * @param string           $field
     * @param FilterValue|null $value
     * @param string           $operator
     */
    public function __construct(string $field, FilterValue $value = null, string $operator = self::LIKE)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $operator
     * @return StringMatchFilter
     */
    public static function createFromValue(string $field, string $value, string $operator = self::LIKE)
    {
        return new self(
            $field,
            new FilterValue($value),
            $operator
        );
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
    public function getValue()
    {
        return $this->value->getValue();
    }

    /**
     * @return FilterValue|null
     */
    public function getFilterValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getOperator(): ?string
    {
        return $this->operator;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'string_match';
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $output = [
            'type'       => $this->getType(),
            'field'      => $this->getField(),
            'operator'   => $this->getOperator(),
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
