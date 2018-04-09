<?php

namespace BenTools\OpenCubes\Request;

use BenTools\OpenCubes\Component\ComponentInterface;
use Psr\Http\Message\RequestInterface;

interface RequestParserInterface
{

    /**
     * @param RequestInterface        $request
     * @param ComponentInterface|null $component
     * @return ComponentInterface
     */
    public function parseRequest(RequestInterface $request, ComponentInterface $component = null): ComponentInterface;

    /**
     * @param ComponentInterface $component
     * @return bool
     */
    public function supportsComponent(ComponentInterface $component): bool;
}
