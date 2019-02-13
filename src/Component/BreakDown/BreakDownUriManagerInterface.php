<?php

namespace BenTools\OpenCubes\Component\BreakDown;

use BenTools\OpenCubes\Component\BreakDown\Model\Group;
use Psr\Http\Message\UriInterface;

interface BreakDownUriManagerInterface
{

    /**
     * Return the applied groups, as strings, retrieved from the Uri.
     *
     * @param UriInterface $uri
     * @return array|string[]
     */
    public function getAppliedGroups(UriInterface $uri): array;

    /**
     * Hydrate an Uri with the given applied groups.
     *
     * @param UriInterface           $uri
     * @param array|string[]|Group[] $groups
     * @return UriInterface
     */
    public function buildGroupUri(UriInterface $uri, array $groups): UriInterface;
}
