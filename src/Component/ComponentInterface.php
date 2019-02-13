<?php

namespace BenTools\OpenCubes\Component;

interface ComponentInterface extends \JsonSerializable
{

    /**
     * The component name.
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * The component JSON representation.
     *
     * @return array
     */
    public function jsonSerialize(): array;
}
