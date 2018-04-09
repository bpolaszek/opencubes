<?php

namespace BenTools\OpenCubes\Request;

use BenTools\OpenCubes\Component\ComponentInterface;
use Psr\Http\Message\RequestInterface;

interface RequestBuilderInterface
{

    /**
     * @param ComponentInterface    $component
     * @param RequestInterface|null $request
     * @return RequestInterface
     */
    public function buildRequest(ComponentInterface $component, RequestInterface $request = null): RequestInterface;

    /**
     * @param ComponentInterface $component
     * @return bool
     */
    public function supportsComponent(ComponentInterface $component): bool;
}
