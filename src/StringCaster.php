<?php

namespace BenTools\OpenCubes;

final class StringCaster
{
    private $value;

    /**
     * UriParamNormalizer constructor.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function asInt(): int
    {
        return (int) $this->value;
    }

    /**
     * @return int|null
     */
    public function asIntOrNull(): ?int
    {
        if (0 === strlen($this->value)) {
            return null;
        }

        return (int) $this->value;
    }

    /**
     * @return float
     */
    public function asFloat(): float
    {
        return (float) $this->value;
    }

    /**
     * @return float|null
     */
    public function asFloatOrNull(): ?float
    {
        if (0 === strlen($this->value)) {
            return null;
        }

        return (float) $this->value;
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return (string) $this->value;
    }

    /**
     * @return string|null
     */
    public function asStringOrNull(): ?string
    {
        if (0 === strlen($this->value)) {
            return null;
        }

        return (string) $this->value;
    }

    /**
     * @return bool
     */
    public function asBool(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool|null
     */
    public function asBoolOrNull(): ?bool
    {
        if (0 === strlen($this->value)) {
            return null;
        }

        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
    }
}
