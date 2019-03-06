<?php

namespace BenTools\OpenCubes\Tests\Component\Pager;

use BenTools\OpenCubes\Component\Pager\PagerUriManager;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class PagerUrlManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_current_page_number()
    {
        $uriManager = new PagerUriManager();

        $uri = uri('https://example.org/');
        $this->assertEquals(null, $uriManager->getCurrentPageNumber($uri));

        $uri = uri('https://example.org/?page=3');
        $this->assertEquals(3, $uriManager->getCurrentPageNumber($uri));

        $uriManager = new PagerUriManager([PagerUriManager::OPT_PAGE_QUERY_PARAM => 'page_number']);
        $uri = uri('https://example.org/?page_number=3');
        $this->assertEquals(3, $uriManager->getCurrentPageNumber($uri));
    }

    /**
     * @test
     */
    public function it_returns_the_current_page_size()
    {
        $uriManager = new PagerUriManager();

        $uri = uri('https://example.org/');
        $this->assertEquals(null, $uriManager->getCurrentPageSize($uri));

        $uri = uri('https://example.org/?per_page=10');
        $this->assertEquals(10, $uriManager->getCurrentPageSize($uri));

        $uriManager = new PagerUriManager([PagerUriManager::OPT_PAGESIZE_QUERY_PARAM => 'limit']);
        $uri = uri('https://example.org/?limit=10');
        $this->assertEquals(10, $uriManager->getCurrentPageSize($uri));
    }

    /**
     * @test
     */
    public function it_builds_an_uri_with_the_correct_page_number()
    {
        $uriManager = new PagerUriManager();
        $uri = uri('https://example.org/');
        $this->assertEquals('https://example.org/?page=3', (string) $uriManager->buildPageUri($uri, 3));
        $this->assertEquals('https://example.org/', (string) $uriManager->buildPageUri($uri, 1));

        $uri = uri('https://example.org/?page=2');
        $this->assertEquals('https://example.org/?page=3', (string) $uriManager->buildPageUri($uri, 3));
        $this->assertEquals('https://example.org/', (string) $uriManager->buildPageUri($uri, 1));

        $uriManager = new PagerUriManager([PagerUriManager::OPT_PAGE_QUERY_PARAM => 'page_number']);
        $uri = uri('https://example.org/');
        $this->assertEquals('https://example.org/?page_number=3', (string) $uriManager->buildPageUri($uri, 3));
        $this->assertEquals('https://example.org/', (string) $uriManager->buildPageUri($uri, 1));

        $uri = uri('https://example.org/?page_number=2');
        $this->assertEquals('https://example.org/?page_number=3', (string) $uriManager->buildPageUri($uri, 3));
        $this->assertEquals('https://example.org/', (string) $uriManager->buildPageUri($uri, 1));
    }

    /**
     * @test
     */
    public function it_builds_an_uri_with_the_correct_page_size()
    {
        $uriManager = new PagerUriManager();
        $uri = uri('https://example.org/');
        $this->assertEquals('https://example.org/?per_page=10', (string) $uriManager->buildSizeUri($uri, 10));
        $this->assertEquals('https://example.org/?per_page=15', (string) $uriManager->buildSizeUri($uri, 15));

        $uri = uri('https://example.org/?per_page=5');
        $this->assertEquals('https://example.org/?per_page=10', (string) $uriManager->buildSizeUri($uri, 10));
        $this->assertEquals('https://example.org/?per_page=15', (string) $uriManager->buildSizeUri($uri, 15));

        $uriManager = new PagerUriManager([PagerUriManager::OPT_PAGESIZE_QUERY_PARAM => 'limit']);
        $uri = uri('https://example.org/');
        $this->assertEquals('https://example.org/?limit=10', (string) $uriManager->buildSizeUri($uri, 10));
        $this->assertEquals('https://example.org/?limit=15', (string) $uriManager->buildSizeUri($uri, 15));

        $uri = uri('https://example.org/?limit=5');
        $this->assertEquals('https://example.org/?limit=10', (string) $uriManager->buildSizeUri($uri, 10));
        $this->assertEquals('https://example.org/?limit=15', (string) $uriManager->buildSizeUri($uri, 15));
    }

}
