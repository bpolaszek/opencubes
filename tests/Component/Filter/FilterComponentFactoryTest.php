<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\FilterComponentFactory;
use BenTools\OpenCubes\Component\Filter\FilterUriManager;
use BenTools\OpenCubes\Component\Filter\Model\CollectionFilter;
use BenTools\OpenCubes\Component\Filter\Model\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\Model\RangeFilter;
use BenTools\OpenCubes\Component\Filter\Model\SimpleFilter;
use BenTools\OpenCubes\Component\Filter\Model\StringMatchFilter;
use PHPUnit\Framework\TestCase;
use function BenTools\UriFactory\Helper\uri;

class FilterComponentFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function it_creates_a_simple_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo]=bar');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var SimpleFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(SimpleFilter::class, $filter);

        $this->assertEquals('bar', $filter->getValue());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(false, $filter->isNegated());
    }

    /**
     * @test
     */
    public function it_creates_a_negated_simple_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo][NOT]=bar');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var SimpleFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(SimpleFilter::class, $filter);

        $this->assertEquals('bar', $filter->getValue());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(true, $filter->isNegated());
    }

    /**
     * @test
     */
    public function it_creates_a_collection_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo][]=bar&f[foo][]=baz');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var CollectionFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(CollectionFilter::class, $filter);

        $this->assertEquals(['bar', 'baz'], $filter->getValues());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(false, $filter->isNegated());
        $this->assertEquals(CollectionFilter::SATISFIED_BY_ANY, $filter->getSatisfiedBy());
    }

    /**
     * @test
     */
    public function it_creates_a_negated_collection_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM              => 'f',
            FilterUriManager::OPT_DEFAULT_COLLECTION_SATISFIED_BY => CollectionFilter::SATISFIED_BY_ALL,
        ]));

        $uri = uri('/?f[foo][NOT][]=bar&f[foo][NOT][]=baz');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var CollectionFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(CollectionFilter::class, $filter);

        $this->assertEquals(['bar', 'baz'], $filter->getValues());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(true, $filter->isNegated());
        $this->assertEquals(CollectionFilter::SATISFIED_BY_ALL, $filter->getSatisfiedBy());
    }

    /**
     * @test
     */
    public function it_creates_a_range_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo]=[bar TO baz]');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var RangeFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(RangeFilter::class, $filter);

        $this->assertEquals('bar', $filter->getLeft());
        $this->assertEquals('baz', $filter->getRight());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(false, $filter->isNegated());

        $uri = uri('/?f[foo]=[bar TO *]');
        $component = $factory->createComponent($uri);
        $filter = $component->get('foo');
        $this->assertEquals('bar', $filter->getLeft());
        $this->assertEquals(null, $filter->getRight());

        $uri = uri('/?f[foo]=[* TO baz]');
        $component = $factory->createComponent($uri);
        $filter = $component->get('foo');
        $this->assertEquals(null, $filter->getLeft());
        $this->assertEquals('baz', $filter->getRight());
    }

    /**
     * @test
     */
    public function it_creates_a_negated_range_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo][NOT]=[bar TO baz]');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var RangeFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(RangeFilter::class, $filter);

        $this->assertEquals('bar', $filter->getLeft());
        $this->assertEquals('baz', $filter->getRight());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(true, $filter->isNegated());
    }

    /**
     * @test
     */
    public function it_creates_a_string_match_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo][STARTS_WITH]=bar');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var StringMatchFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(StringMatchFilter::class, $filter);

        $this->assertEquals('bar', $filter->getValue());
        $this->assertEquals(StringMatchFilter::STARTS_WITH, $filter->getOperator());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(false, $filter->isNegated());
    }

    /**
     * @test
     */
    public function it_creates_a_negated_string_match_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo][NOT][LIKE]=bar');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var StringMatchFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(StringMatchFilter::class, $filter);

        $this->assertEquals('bar', $filter->getValue());
        $this->assertEquals(StringMatchFilter::LIKE, $filter->getOperator());
        $this->assertEquals(true, $filter->isApplied());
        $this->assertEquals(true, $filter->isNegated());
    }

    /**
     * @test
     */
    public function it_creates_a_composite_filter()
    {
        $factory = new FilterComponentFactory([], new FilterUriManager([
            FilterUriManager::OPT_FILTER_QUERY_PARAM => 'f',
        ]));

        $uri = uri('/?f[foo][]=bar&f[foo][]=baz&f[foo][NOT][STARTS_WITH]=z');

        /** @var FilterComponent $component */
        $component = $factory->createComponent($uri);

        $this->assertTrue($component->has('foo'));

        /** @var CompositeFilter $filter */
        $filter = $component->get('foo');
        $this->assertInstanceOf(CompositeFilter::class, $filter);
        $this->assertEquals(CompositeFilter::SATISFIED_BY_ALL, $filter->getSatisfiedBy());

        $subFilters = $filter->getFilters();
        $this->assertCount(2, $subFilters);

        $this->assertInstanceOf(CollectionFilter::class, $subFilters[0]);
        $this->assertEquals(CollectionFilter::SATISFIED_BY_ANY, $subFilters[0]->getSatisfiedBy());
        $this->assertInstanceOf(StringMatchFilter::class, $subFilters[1]);

    }
}
