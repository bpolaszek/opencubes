<?php

namespace BenTools\OpenCubes\Tests\Component\Sort;

use BenTools\OpenCubes\Component\Sort\SortUriManager;
use PHPUnit\Framework\TestCase;
use function BenTools\OpenCubes\stringify_uri;
use function BenTools\UriFactory\Helper\uri;

class SortUriManagerTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_the_applied_sorts()
    {
        $uriManager = new SortUriManager();
        $this->assertEquals([], $uriManager->getAppliedSorts(uri('https://example.org/')));
        $this->assertEquals(['foo' => 'asc', 'bar' => 'desc'], $uriManager->getAppliedSorts(uri('https://example.org/?sort[foo]=asc&sort[bar]=desc')));

        $uriManager = new SortUriManager([SortUriManager::OPT_SORT_QUERY_PARAM => 'order_by']);
        $this->assertEquals(['foo' => 'asc', 'bar' => 'desc'], $uriManager->getAppliedSorts(uri('https://example.org/?order_by[foo]=asc&order_by[bar]=desc')));
    }

    /**
     * @test
     */
    public function it_builds_a_correct_uri()
    {
        $uriManager = new SortUriManager();
        $uri = uri('https://example.org/');
        $this->assertEquals('https://example.org/?sort[foo]=asc&sort[bar]=desc', stringify_uri($uriManager->buildSortUri($uri, ['foo' => 'asc', 'bar' => 'desc'])));

        $uriManager = new SortUriManager([SortUriManager::OPT_SORT_QUERY_PARAM => 'order_by']);
        $this->assertEquals('https://example.org/?order_by[foo]=asc&order_by[bar]=desc', stringify_uri($uriManager->buildSortUri($uri, ['foo' => 'asc', 'bar' => 'desc'])));

    }

    /**
     * @test
     */
    public function it_resets_the_page_number()
    {
        $uriManager = new SortUriManager();
        $uri = uri('https://example.org/?page=3&foo=bar');
        $this->assertEquals('https://example.org/?foo=bar&sort[foo]=asc&sort[bar]=desc', stringify_uri($uriManager->buildSortUri($uri, ['foo' => 'asc', 'bar' => 'desc'])));
    }

}
