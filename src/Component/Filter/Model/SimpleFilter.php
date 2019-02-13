<?php

namespace BenTools\OpenCubes\Component\Filter\Model;

use function BenTools\OpenCubes\stringify_uri;

final class SimpleFilter extends Filter
{
    /**
     * @var string
     */
    private $field;
    private $value;

    /**
     * SimpleFilter constructor.
     */
    public function __construct(string $field, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
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
