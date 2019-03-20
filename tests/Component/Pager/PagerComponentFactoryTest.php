<?php

namespace BenTools\OpenCubes\Tests\Component\Pager;

use BenTools\OpenCubes\Component\Pager\Model\Page;
use BenTools\OpenCubes\Component\Pager\PagerComponent;
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use function BenTools\UriFactory\Helper\uri;
use PHPUnit\Framework\TestCase;

class PagerComponentFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_pager_component()
    {
        $factory = new PagerComponentFactory();
        $this->assertTrue($factory->supports(PagerComponent::getName()));
    }

    /**
     * @test
     */
    public function it_generates_at_least_one_page()
    {
        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_DEFAULT_PAGESIZE => 50,
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri);
        $this->assertEquals(0, $component->getNbItems());
        $this->assertCount(1, $component);
    }

    /**
     * @test
     */
    public function it_generates_the_correct_number_of_pages()
    {
        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_DEFAULT_PAGESIZE => 50,
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri, [PagerComponentFactory::OPT_TOTAL_ITEMS => 160]);
        $this->assertEquals(160, $component->getNbItems());
        $this->assertCount(4, $component);

        $uri = uri('https://example.org/?per_page=10');
        $component = $factory->createComponent($uri, [PagerComponentFactory::OPT_TOTAL_ITEMS => 160]);
        $this->assertEquals(160, $component->getNbItems());
        $this->assertCount(16, $component);
        $this->assertCount(16, iterable_to_array($component->getPages()));
    }

    /**
     * @test
     */
    public function it_generates_only_one_page_when_pager_is_disabled()
    {
        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_ENABLED => false,
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri, [PagerComponentFactory::OPT_TOTAL_ITEMS => 160]);
        $this->assertEquals(160, $component->getNbItems());
        $this->assertCount(1, $component);

        $uri = uri('https://example.org/?per_page=15&page=3');
        $component = $factory->createComponent($uri, [PagerComponentFactory::OPT_TOTAL_ITEMS => 160]);
        $this->assertEquals(160, $component->getNbItems());
        $this->assertCount(1, $component);
        $this->assertEquals(1, $component->getCurrentPage());
    }

    /**
     * @test
     */
    public function it_applies_a_delta()
    {
        $containsPageNumber = function (iterable $pages, int $pageNumber): bool {
            /** @var Page[] $pages */
            foreach ($pages as $page) {
                if ($page->getPageNumber() === $pageNumber) {
                    return true;
                }
            }

            return false;
        };

        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_DELTA => 2,
            PagerComponentFactory::OPT_DEFAULT_PAGESIZE => 10,
            PagerComponentFactory::OPT_TOTAL_ITEMS => 200,
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri);
        $this->assertEquals(200, $component->getNbItems());
        $this->assertCount(20, $component);

        $pages = $component->getPages();
        $this->assertEquals(true, $containsPageNumber($pages, 1));
        $this->assertEquals(true, $containsPageNumber($pages, 2));
        $this->assertEquals(true, $containsPageNumber($pages, 3));
        $this->assertEquals(false, $containsPageNumber($pages, 4));
        $this->assertEquals(false, $containsPageNumber($pages, 20));

        $uri = uri('https://example.org/?page=20');
        $component = $factory->createComponent($uri);
        $this->assertEquals(200, $component->getNbItems());
        $this->assertCount(20, $component);

        $pages = $component->getPages();
        $this->assertEquals(false, $containsPageNumber($pages, 1));
        $this->assertEquals(true, $containsPageNumber($pages, 20));
        $this->assertEquals(true, $containsPageNumber($pages, 19));
        $this->assertEquals(true, $containsPageNumber($pages, 18));
        $this->assertEquals(false, $containsPageNumber($pages, 17));

        $uri = uri('https://example.org/?page=10');
        $component = $factory->createComponent($uri);
        $this->assertEquals(200, $component->getNbItems());
        $this->assertCount(20, $component);

        $pages = $component->getPages();
        $this->assertEquals(false, $containsPageNumber($pages, 1));
        $this->assertEquals(false, $containsPageNumber($pages, 20));
        $this->assertEquals(false, $containsPageNumber($pages, 7));
        $this->assertEquals(true, $containsPageNumber($pages, 8));
        $this->assertEquals(true, $containsPageNumber($pages, 9));
        $this->assertEquals(true, $containsPageNumber($pages, 10));
        $this->assertEquals(true, $containsPageNumber($pages, 11));
        $this->assertEquals(true, $containsPageNumber($pages, 12));
        $this->assertEquals(false, $containsPageNumber($pages, 13));

    }


    /**
     * @test
     */
    public function it_generates_the_pagesizes()
    {
        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_AVAILABLE_PAGESIZES => [10, 20, 50],
            PagerComponentFactory::OPT_TOTAL_ITEMS => 200,
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri);
        $pageSizes = $component->getPageSizes();
        $this->assertCount(3, $pageSizes);

        $uri = uri('https://example.org/?per_page=10');
        $component = $factory->createComponent($uri);
        $pageSizes = $component->getPageSizes();
        $this->assertCount(3, $pageSizes);

        $uri = uri('https://example.org/?per_page=40');
        $component = $factory->createComponent($uri);
        $pageSizes = $component->getPageSizes();
        $this->assertCount(4, $pageSizes);

    }


    /**
     * @test
     */
    public function it_successfully_disables_the_pager()
    {
        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_AVAILABLE_PAGESIZES => [10, 20, 50],
            PagerComponentFactory::OPT_ENABLED => false,
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri);
        $pageSizes = $component->getPageSizes();
        $this->assertCount(3, $pageSizes);

        $uri = uri('https://example.org/?per_page=10');
        $component = $factory->createComponent($uri);
        $pageSizes = $component->getPageSizes();
        $this->assertCount(3, $pageSizes);

        $uri = uri('https://example.org/?per_page=40');
        $component = $factory->createComponent($uri);
        $pageSizes = $component->getPageSizes();
        $this->assertCount(3, $pageSizes);

        $this->assertFalse($component->isEnabled());
        $this->assertCount(1, $component);
        $this->assertEquals($component->getNbItems(), $component->getPerPage());
    }

}
