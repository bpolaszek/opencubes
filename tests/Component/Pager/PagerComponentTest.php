<?php

namespace BenTools\OpenCubes\Tests\Component\Pager;

use BenTools\OpenCubes\Component\Pager\PagerComponent;
use BenTools\OpenCubes\Component\Pager\PagerComponentFactory;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class PagerComponentTest extends TestCase
{

    public function countDataProvider()
    {
        yield [1, 4];
        yield [2, 4];
        yield [3, 4];
        yield [10, 4];
    }

    /**
     * @dataProvider countDataProvider
     */
    public function testCount(int $currentPage, int $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertCount($expected, $component);
    }

    public function firstPageDataProvider()
    {
        yield [1, 1];
        yield [12, 1];
    }

    /**
     * @dataProvider firstPageDataProvider
     */
    public function testGetFirstPage(int $pageNumber, int $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $pageNumber);
        $this->assertEquals($expected, $component->getFirstPage());
    }

    public function previousPageDataProvider()
    {
        yield [1, 1];
        yield [2, 1];
        yield [3, 2];
        yield [10, 3];
    }

    /**
     * @dataProvider previousPageDataProvider
     */
    public function testGetPreviousPage(int $pageNumber, int $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $pageNumber);
        $this->assertEquals($expected, $component->getPreviousPage());

        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals($expected, $component->getPreviousPage($pageNumber));

        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 4);
        $this->assertEquals($expected, $component->getPreviousPage($pageNumber));

        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 10);
        $this->assertEquals($expected, $component->getPreviousPage($pageNumber));
    }


    public function nextPageDataProvider()
    {
        yield [1, 2];
        yield [2, 3];
        yield [3, 4];
        yield [4, 4];
        yield [10, 4];
    }

    /**
     * @dataProvider nextPageDataProvider
     */
    public function testGetNextPage(int $pageNumber, int $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $pageNumber);
        $this->assertEquals($expected, $component->getNextPage());

        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals($expected, $component->getNextPage($pageNumber));

        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 4);
        $this->assertEquals($expected, $component->getNextPage($pageNumber));

        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 10);
        $this->assertEquals($expected, $component->getNextPage($pageNumber));
    }

    public function lastPageDataProvider()
    {
        yield [1, 4];
        yield [2, 4];
        yield [3, 4];
        yield [10, 4];
    }

    /**
     * @dataProvider lastPageDataProvider
     */
    public function testGetLastPage(int $currentPage, int $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->getLastPage());
    }

    public function isFirstPageDataProvider()
    {
        yield [1, true];
        yield [2, false];
        yield [3, false];
        yield [4, false];
        yield [10, false];
    }

    /**
     * @dataProvider isFirstPageDataProvider
     */
    public function testIsFirstPage(int $currentPage, bool $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->isFirstPage());
    }

    public function isPreviousPageDataProvider()
    {
        yield [2, 1, true];
        yield [2, 2, false];
        yield [2, 3, false];
    }

    /**
     * @dataProvider isPreviousPageDataProvider
     */
    public function testIsPreviousPage(int $currentPage, int $previousPage, bool $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->isPreviousPage($previousPage));
    }

    public function isCurrentPageDataProvider()
    {
        yield [2, 2, true];
        yield [2, 1, false];
        yield [2, 3, false];
    }

    /**
     * @dataProvider isCurrentPageDataProvider
     */
    public function testIsCurrentPage(int $currentPage, int $comparedPage, bool $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->isCurrentPage($comparedPage));
    }

    public function isNextPageDataProvider()
    {
        yield [2, 3, true];
        yield [2, 4, false];
        yield [2, 5, false];
    }

    /**
     * @dataProvider isNextPageDataProvider
     */
    public function testIsNextPage(int $currentPage, int $nextPage, bool $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->isNextPage($nextPage));
    }

    public function isLastPageDataProvider()
    {
        yield [1, false];
        yield [2, false];
        yield [3, false];
        yield [4, true];
        yield [10, true];
    }

    /**
     * @dataProvider isLastPageDataProvider
     */
    public function testIsLastPage(int $currentPage, bool $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->isLastPage());
    }

    public function currentPageNumberDataProvider()
    {
        yield [1, 1];
        yield [2, 2];
        yield [3, 3];
        yield [4, 4];
        yield [10, 4];
    }

    /**
     * @dataProvider currentPageNumberDataProvider
     */
    public function testGetCurrentPageNumber(int $currentPage, bool $expected)
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, $currentPage);
        $this->assertEquals($expected, $component->getCurrentPage());
    }

    public function testGetPerPage()
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals(50, $component->getPerPage());
        $component = new PagerComponent(uri('https://foo.bar/'), 160, null, 1);
        $this->assertEquals(160, $component->getPerPage());
    }

    public function testGetNbItems()
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals(160, $component->getNbItems());
        $component = new PagerComponent(uri('https://foo.bar/'), 160, null, 1);
        $this->assertEquals(160, $component->getNbItems());
    }

    public function testGetCurrentOffset()
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals(0, $component->getCurrentOffset());
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 2);
        $this->assertEquals(50, $component->getCurrentOffset());
        $component = new PagerComponent(uri('https://foo.bar/'), 160, null, 2);
        $this->assertEquals(0, $component->getCurrentOffset());
    }

    public function testGetPageOffset()
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals(0, $component->getPageOffset(1));
        $this->assertEquals(50, $component->getPageOffset(2));
        $this->assertEquals(100, $component->getPageOffset(3));
        $this->assertEquals(150, $component->getPageOffset(4));
        $this->assertEquals(150, $component->getPageOffset(5));

        $component = new PagerComponent(uri('https://foo.bar/'), 160, null, 1);
        $this->assertEquals(0, $component->getPageOffset(1));
        $this->assertEquals(0, $component->getPageOffset(2));
        $this->assertEquals(0, $component->getPageOffset(3));
        $this->assertEquals(0, $component->getPageOffset(4));
        $this->assertEquals(0, $component->getPageOffset(5));
    }

    public function testGetPageCount()
    {
        $component = new PagerComponent(uri('https://foo.bar/'), 160, 50, 1);
        $this->assertEquals(50, $component->getPageCount(1));
        $this->assertEquals(50, $component->getPageCount(2));
        $this->assertEquals(50, $component->getPageCount(3));
        $this->assertEquals(10, $component->getPageCount(4));
        $this->assertEquals(10, $component->getPageCount(5));

        $component = new PagerComponent(uri('https://foo.bar/'), 160, null, 1);
        $this->assertEquals(160, $component->getPageCount(1));
        $this->assertEquals(160, $component->getPageCount(2));
        $this->assertEquals(160, $component->getPageCount(3));
        $this->assertEquals(160, $component->getPageCount(4));
        $this->assertEquals(160, $component->getPageCount(5));
    }

    public function testJsonSerialize()
    {
        $uri = uri('https://foo.bar/?page=3');
        $factory = new PagerComponentFactory([
            PagerComponentFactory::OPT_TOTAL_ITEMS => 160,
            PagerComponentFactory::OPT_DEFAULT_PAGESIZE => 50,
            PagerComponentFactory::OPT_AVAILABLE_PAGESIZES => [10, 100],
        ]);
        $component = $factory->createComponent($uri);
        $json = json_decode(json_encode($component), true);
        $expected = [
            'is_enabled'    => true,
            'per_page'   => 50,
            'nb_items'   => 160,
            'count'      => 4,
            'first'      => [
                'number'      => 1,
                'nb_items'    => 50,
                'offset'      => 0,
                'is_first'    => true,
                'is_previous' => false,
                'is_current'  => false,
                'is_next'     => false,
                'is_last'     => false,
                'link'        => 'https://foo.bar/',
            ],
            'previous'   => [
                'number'      => 2,
                'nb_items'    => 50,
                'offset'      => 50,
                'is_first'    => false,
                'is_previous' => true,
                'is_current'  => false,
                'is_next'     => false,
                'is_last'     => false,
                'link'        => 'https://foo.bar/?page=2',
            ],
            'current'    => [
                'number'      => 3,
                'nb_items'    => 50,
                'offset'      => 100,
                'is_first'    => false,
                'is_previous' => false,
                'is_current'  => true,
                'is_next'     => false,
                'is_last'     => false,
                'link'        => 'https://foo.bar/?page=3',
            ],
            'next'       => [
                'number'      => 4,
                'nb_items'    => 10,
                'offset'      => 150,
                'is_first'    => false,
                'is_previous' => false,
                'is_current'  => false,
                'is_next'     => true,
                'is_last'     => true,
                'link'        => 'https://foo.bar/?page=4',
            ],
            'last'       => [
                'number'      => 4,
                'nb_items'    => 10,
                'offset'      => 150,
                'is_first'    => false,
                'is_previous' => false,
                'is_current'  => false,
                'is_next'     => true,
                'is_last'     => true,
                'link'        => 'https://foo.bar/?page=4',
            ],
            'pages'      => [
                [
                    'number'      => 1,
                    'nb_items'    => 50,
                    'offset'      => 0,
                    'is_first'    => true,
                    'is_previous' => false,
                    'is_current'  => false,
                    'is_next'     => false,
                    'is_last'     => false,
                    'link'        => 'https://foo.bar/',
                ],
                [
                    'number'      => 2,
                    'nb_items'    => 50,
                    'offset'      => 50,
                    'is_first'    => false,
                    'is_previous' => true,
                    'is_current'  => false,
                    'is_next'     => false,
                    'is_last'     => false,
                    'link'        => 'https://foo.bar/?page=2',
                ],
                [
                    'number'      => 3,
                    'nb_items'    => 50,
                    'offset'      => 100,
                    'is_first'    => false,
                    'is_previous' => false,
                    'is_current'  => true,
                    'is_next'     => false,
                    'is_last'     => false,
                    'link'        => 'https://foo.bar/?page=3',
                ],
                [
                    'number'      => 4,
                    'nb_items'    => 10,
                    'offset'      => 150,
                    'is_first'    => false,
                    'is_previous' => false,
                    'is_current'  => false,
                    'is_next'     => true,
                    'is_last'     => true,
                    'link'        => 'https://foo.bar/?page=4',
                ],
            ],
            'page_sizes' => [
                [
                    'size'         => 10,
                    'is_applied' => false,
                    'link'       => 'https://foo.bar/?per_page=10',
                ],
                [
                    'size'         => 50,
                    'is_applied' => true,
                    'link'       => 'https://foo.bar/',
                ],
                [
                    'size'         => 100,
                    'is_applied' => false,
                    'link'       => 'https://foo.bar/?per_page=100',
                ],
            ]
        ];

        $this->assertEquals($expected, $json);
    }
}
