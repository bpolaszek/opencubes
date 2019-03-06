<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\FilterUriManager;
use BenTools\OpenCubes\Component\Filter\Model\CollectionFilter;
use BenTools\OpenCubes\Component\Filter\Model\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\Model\RangeFilter;
use BenTools\OpenCubes\Component\Filter\Model\SimpleFilter;
use PHPUnit\Framework\TestCase;
use function BenTools\OpenCubes\stringify_uri;
use function BenTools\UriFactory\Helper\uri;

class FilterUriManagerTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_the_raw_applied_filters()
    {
        $uriManager = new FilterUriManager();
        $this->assertEquals([], $uriManager->getAppliedFilters(uri('https://example.org/')));

        $expected = [
            'created_at' => '[2019-01-01 TO 2019-01-31]',
            'category'   => 'cats',
            'ids'        => [
                '1',
                '2',
            ],
            'name'       => [
                'NOT' => [
                    'STARTS_WITH' => 'Meow'
                ]
            ],
        ];

        $uri = uri('https://example.org/?filters[created_at]=[2019-01-01 TO 2019-01-31]&filters[category]=cats&filters[ids][]=1&filters[ids][]=2&filters[name][NOT][STARTS_WITH]=Meow');
        $this->assertEquals($expected, $uriManager->getAppliedFilters($uri));

        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/?f[created_at]=[2019-01-01 TO 2019-01-31]&f[category]=cats&f[ids][]=1&f[ids][]=2&f[name][NOT][STARTS_WITH]=Meow');
        $this->assertEquals($expected, $uriManager->getAppliedFilters($uri));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_simple_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = SimpleFilter::createFromValue('category', 'cats');
        $this->assertEquals('https://example.org/?f[category]=cats', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[category]=dogs', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, 'dogs')));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_negated_simple_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = SimpleFilter::createFromValue('category', 'cats');
        $filter = $filter->negate();
        $this->assertEquals('https://example.org/?f[category][NOT]=cats', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[category][NOT]=dogs', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, 'dogs')));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_collection_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = CollectionFilter::createFromValues('ids', [1, 2]);
        $this->assertEquals('https://example.org/?f[ids][]=1&f[ids][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][]=3&f[ids][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));

        $filter = CollectionFilter::createFromValues('ids', [1, 2], CollectionFilter::SATISFIED_BY_ALL);
        $this->assertEquals('https://example.org/?f[ids][ALL][]=1&f[ids][ALL][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][ALL][]=3&f[ids][ALL][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));

        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f', FilterUriManager::OPT_DEFAULT_SATISFIED_BY => CollectionFilter::SATISFIED_BY_ALL]);
        $filter = CollectionFilter::createFromValues('ids', [1, 2], CollectionFilter::SATISFIED_BY_ALL);
        $this->assertEquals('https://example.org/?f[ids][]=1&f[ids][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][]=3&f[ids][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));

        $filter = CollectionFilter::createFromValues('ids', [1, 2], CollectionFilter::SATISFIED_BY_ANY);
        $this->assertEquals('https://example.org/?f[ids][ANY][]=1&f[ids][ANY][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][ANY][]=3&f[ids][ANY][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_negated_collection_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = CollectionFilter::createFromValues('ids', [1, 2]);
        $filter = $filter->negate();
        $this->assertEquals('https://example.org/?f[ids][NOT][]=1&f[ids][NOT][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][NOT][]=3&f[ids][NOT][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));

        $filter = CollectionFilter::createFromValues('ids', [1, 2], CollectionFilter::SATISFIED_BY_ALL);
        $filter = $filter->negate();
        $this->assertEquals('https://example.org/?f[ids][NOT][ALL][]=1&f[ids][NOT][ALL][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][NOT][ALL][]=3&f[ids][NOT][ALL][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));

        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f', FilterUriManager::OPT_DEFAULT_SATISFIED_BY => CollectionFilter::SATISFIED_BY_ALL]);
        $filter = CollectionFilter::createFromValues('ids', [1, 2], CollectionFilter::SATISFIED_BY_ALL);
        $filter = $filter->negate();
        $this->assertEquals('https://example.org/?f[ids][NOT][]=1&f[ids][NOT][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][NOT][]=3&f[ids][NOT][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));

        $filter = CollectionFilter::createFromValues('ids', [1, 2], CollectionFilter::SATISFIED_BY_ANY);
        $filter = $filter->negate();
        $this->assertEquals('https://example.org/?f[ids][NOT][ANY][]=1&f[ids][NOT][ANY][]=2', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[ids][NOT][ANY][]=3&f[ids][NOT][ANY][]=4', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, [3, 4])));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_range_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = new RangeFilter('created_at', '2019-01-01', '2019-01-31');
        $this->assertEquals('https://example.org/?f[created_at]=[2019-01-01 TO 2019-01-31]', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[created_at]=[foo TO bar]', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, ['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_negated_range_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = new RangeFilter('created_at', '2019-01-01', '2019-01-31');
        $filter = $filter->negate();
        $this->assertEquals('https://example.org/?f[created_at][NOT]=[2019-01-01 TO 2019-01-31]', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
        $this->assertEquals('https://example.org/?f[created_at][NOT]=[foo TO bar]', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter, ['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_composite_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = new CompositeFilter('created_at', [
            new RangeFilter('created_at', '2019-01-01', '2019-01-31'),
            SimpleFilter::createFromValue('created_at', '2019-01-15')->negate()
        ]);
        $this->assertEquals('https://example.org/?f[created_at][ALL][]=[2019-01-01 TO 2019-01-31]&f[created_at][ALL][NOT]=2019-01-15', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
    }

    /**
     * @test
     */
    public function it_builds_the_correct_uri_with_a_negated_composite_filter()
    {
        $uriManager = new FilterUriManager([FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f']);
        $uri = uri('https://example.org/');
        $filter = (new CompositeFilter('created_at', [
            new RangeFilter('created_at', '2019-01-01', '2019-01-31'),
            SimpleFilter::createFromValue('created_at', '2019-01-15')->negate()
        ]))->negate();
        $this->assertEquals('https://example.org/?f[created_at][NOT][ALL][]=[2019-01-01 TO 2019-01-31]&f[created_at][NOT][ALL][NOT]=2019-01-15', stringify_uri($uriManager->buildApplyFilterUrl($uri, $filter)));
    }

}
