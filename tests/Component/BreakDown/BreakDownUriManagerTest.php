<?php

namespace BenTools\OpenCubes\Tests\Component\BreakDown;

use BenTools\OpenCubes\Component\BreakDown\BreakDownUriManager;
use PHPUnit\Framework\TestCase;
use function BenTools\OpenCubes\stringify_uri;
use function BenTools\UriFactory\Helper\uri;

class BreakDownUriManagerTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_the_applied_groups()
    {
        $uriManager = new BreakDownUriManager();
        $this->assertEquals([], $uriManager->getAppliedGroups(uri('https://example.org/')));
        $this->assertEquals(['foo', 'bar'], $uriManager->getAppliedGroups(uri('https://example.org/?breakdown[]=foo&breakdown[]=bar')));

        $uriManager = new BreakDownUriManager([BreakDownUriManager::OPT_BREAKDOWN_QUERY_PARAM => 'group_by']);
        $this->assertEquals(['foo', 'bar'], $uriManager->getAppliedGroups(uri('https://example.org/?group_by[]=foo&group_by[]=bar')));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri()
    {
        $uriManager = new BreakDownUriManager();
        $uri = uri('https://example.org/');
        $this->assertEquals('https://example.org/?breakdown[]=foo&breakdown[]=bar', stringify_uri($uriManager->buildGroupUri($uri, ['foo', 'bar'])));

        $uriManager = new BreakDownUriManager([BreakDownUriManager::OPT_BREAKDOWN_QUERY_PARAM => 'group_by']);
        $this->assertEquals('https://example.org/?group_by[]=foo&group_by[]=bar', stringify_uri($uriManager->buildGroupUri($uri, ['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function it_resets_the_page_number()
    {
        $uriManager = new BreakDownUriManager();
        $uri = uri('https://example.org/?page=3&foo=bar');
        $this->assertEquals('https://example.org/?foo=bar&breakdown[]=foo&breakdown[]=bar', stringify_uri($uriManager->buildGroupUri($uri, ['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function it_resets_the_applied_sort()
    {
        $uriManager = new BreakDownUriManager();
        $uri = uri('https://example.org/?page=3&foo=bar&sort[foo]=bar');
        $this->assertEquals('https://example.org/?foo=bar&breakdown[]=foo&breakdown[]=bar', stringify_uri($uriManager->buildGroupUri($uri, ['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function it_does_not_resets_the_applied_sort($whenRemoveSort = false)
    {
        $uriManager = new BreakDownUriManager([BreakDownUriManager::OPT_REMOVE_SORT => false]);
        $uri = uri('https://example.org/?page=3&foo=bar&sort[foo]=bar');
        $this->assertEquals('https://example.org/?foo=bar&sort[foo]=bar&breakdown[]=foo&breakdown[]=bar', stringify_uri($uriManager->buildGroupUri($uri, ['foo', 'bar'])));
    }

}
