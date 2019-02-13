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
    private $value;
    /**
     * @var string
     */
    private $operator;

    /**
     * SimpleFilter constructor.
     */
    public function __construct(string $field, $value = null, string $operator)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
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
            'operator'   => $this->getOperator(),
            'value'      => $this->getValue(),
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
