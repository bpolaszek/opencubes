<?php

namespace BenTools\OpenCubes\Component;

use Psr\Http\Message\UriInterface;

interface ComponentFactoryInterface
{

    /**
     * Return whether or not this factory supports this type of component.
     *
     * @param string $name
     * @return bool
     */
    public function supports(string $name): bool;

    /**
     * Instanciate a component from an URI.
     *
     * @param UriInterface $uri
     * @param array        $options
     * @return ComponentInterface
     */
    public function createComponent(UriInterface $uri, array $options = []): ComponentInterface;
}
