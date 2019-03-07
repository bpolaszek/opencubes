<?php

namespace BenTools\OpenCubes\Tests\Component\Sort;

use BenTools\OpenCubes\Component\Sort\Model\Sort;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortComponentFactory;
use function BenTools\OpenCubes\first_of;
use function BenTools\OpenCubes\stringify_uri;
use function BenTools\UriFactory\Helper\uri;
use PHPUnit\Framework\TestCase;

class SortComponentFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_sort_component()
    {
        $factory = new SortComponentFactory();
        $this->assertTrue($factory->supports(SortComponent::getName()));
    }

    /**
     * @test
     */
    public function we_can_define_available_sorts()
    {
        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_AVAILABLE_SORTS => [
                'hits' => ['asc', 'desc'],
                'relevance' => ['desc'],
            ],
        ]);

        $component = $factory->createComponent(uri('https://example.org/'));

        $this->assertEquals(true, $component->has('hits'));
        $this->assertEquals(true, $component->has('hits', 'asc'));
        $this->assertEquals(true, $component->has('hits', 'desc'));
        $this->assertEquals(false, $component->has('hits', 'dummy'));

        $this->assertEquals(true, $component->has('relevance'));
        $this->assertEquals(false, $component->has('relevance', 'asc'));
        $this->assertEquals(true, $component->has('relevance', 'desc'));

        $this->assertEquals(false, $component->has('dummy'));

        $this->assertInstanceOf(Sort::class, first_of($component->get('hits')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('hits', 'asc')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('hits', 'desc')));
        $this->assertNull(first_of($component->get('hits', 'dummy')));
        $this->assertNull(first_of($component->get('dummy')));

        $this->assertCount(2, iterable_to_array($component->get('hits')));

        /** @var Sort $sort */
        $sort = first_of($component->get('hits', 'desc'));
        $this->assertEquals('hits', $sort->getField());
        $this->assertEquals('desc', $sort->getDirection());
        $this->assertFalse($sort->isApplied());
        $this->assertEquals('https://example.org/?sort[hits]=desc', stringify_uri($sort->getToggleUri()));
    }

    /**
     * @test
     */
    public function it_applies_an_available_sort()
    {

        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_AVAILABLE_SORTS => [
                'hits' => ['asc', 'desc'],
            ],
        ]);

        $uri = uri('https://example.org/?sort[hits]=desc');
        $component = $factory->createComponent($uri);

        $this->assertEquals(true, $component->has('hits'));
        $this->assertEquals(true, $component->has('hits', 'asc'));
        $this->assertEquals(true, $component->has('hits', 'desc'));

        $this->assertInstanceOf(Sort::class, first_of($component->get('hits')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('hits', 'asc')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('hits', 'desc')));

        $this->assertCount(2, iterable_to_array($component->get('hits')));

        /** @var Sort $sort */
        $sort = first_of($component->get('hits', 'desc'));
        $this->assertEquals('hits', $sort->getField());
        $this->assertEquals('desc', $sort->getDirection());
        $this->assertTrue($sort->isApplied());
        $this->assertEquals('https://example.org/', stringify_uri($sort->getToggleUri()));
    }

    /**
     * @test
     */
    public function it_applies_an_undefined_sort()
    {

        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_AVAILABLE_SORTS => [
                'hits' => ['asc', 'desc'],
            ],
        ]);

        $uri = uri('https://example.org/?sort[clicks]=desc');
        $component = $factory->createComponent($uri);

        $this->assertEquals(true, $component->has('hits'));
        $this->assertEquals(true, $component->has('hits', 'asc'));
        $this->assertEquals(true, $component->has('hits', 'desc'));

        $this->assertInstanceOf(Sort::class, first_of($component->get('hits')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('hits', 'asc')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('hits', 'desc')));

        $this->assertCount(2, iterable_to_array($component->get('hits')));
        $this->assertCount(1, iterable_to_array($component->get('clicks')));

        /** @var Sort $sort */
        $sort = first_of($component->get('hits', 'desc'));
        $this->assertEquals('hits', $sort->getField());
        $this->assertEquals('desc', $sort->getDirection());
        $this->assertFalse($sort->isApplied());
        $this->assertEquals('https://example.org/?sort[hits]=desc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('clicks', 'desc'));
        $this->assertInstanceOf(Sort::class, $sort);
        $this->assertEquals('clicks', $sort->getField());
        $this->assertEquals('desc', $sort->getDirection());
        $this->assertTrue($sort->isApplied());
        $this->assertEquals('https://example.org/', stringify_uri($sort->getToggleUri()));
    }

    /**
     * @test
     */
    public function it_applies_an_default_sort()
    {
        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_DEFAULT_SORTS => [
                'date' => 'asc',
            ],
        ]);

        $uri = uri('https://example.org/');
        $component = $factory->createComponent($uri);

        $this->assertEquals(true, $component->has('date'));
        $this->assertEquals(true, $component->has('date', 'asc'));
        $this->assertEquals(false, $component->has('date', 'desc'));

        $this->assertInstanceOf(Sort::class, first_of($component->get('date')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('date', 'asc')));

        $sort = first_of($component->getAppliedSorts());
        $this->assertInstanceOf(Sort::class, $sort);
        $this->assertEquals('date', $sort->getField());
        $this->assertEquals('asc', $sort->getDirection());
        $this->assertTrue($sort->isApplied());
        $this->assertEquals('https://example.org/', stringify_uri($sort->getToggleUri()));

    }

    /**
     * @test
     */
    public function uri_replaces_the_default_sort()
    {
        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_DEFAULT_SORTS => [
                'date' => 'asc',
            ],
        ]);

        $uri = uri('https://example.org/?sort[clicks]=desc');
        $component = $factory->createComponent($uri);

        $this->assertEquals(false, $component->has('date'));
        $this->assertEquals(false, $component->has('date', 'asc'));
        $this->assertEquals(false, $component->has('date', 'desc'));

        $this->assertEquals(true, $component->has('clicks'));
        $this->assertEquals(false, $component->has('clicks', 'asc'));
        $this->assertEquals(true, $component->has('clicks', 'desc'));

        $this->assertNull(first_of($component->get('date')));
        $this->assertNull(first_of($component->get('date', 'asc')));

        $this->assertInstanceOf(Sort::class, first_of($component->get('clicks')));
        $this->assertInstanceOf(Sort::class, first_of($component->get('clicks', 'desc')));

        $sort = first_of($component->getAppliedSorts());
        $this->assertInstanceOf(Sort::class, $sort);
        $this->assertEquals('clicks', $sort->getField());
        $this->assertEquals('desc', $sort->getDirection());
        $this->assertTrue($sort->isApplied());
        $this->assertEquals('https://example.org/', stringify_uri($sort->getToggleUri()));
    }

    /**
     * @test
     */
    public function sorts_cannot_be_combined_by_default()
    {
        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_AVAILABLE_SORTS => [
                'hits' => ['asc', 'desc'],
                'clicks' => ['asc', 'desc'],
            ],
        ]);

        $uri = uri('https://example.org/?sort[clicks]=desc');
        $component = $factory->createComponent($uri);

        /** @var Sort $sort */
        $sort = first_of($component->get('hits', 'asc'));
        $this->assertEquals(false, $sort->isApplied());
        $this->assertEquals('https://example.org/?sort[hits]=asc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('hits', 'desc'));
        $this->assertEquals(false, $sort->isApplied());
        $this->assertEquals('https://example.org/?sort[hits]=desc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('clicks', 'asc'));
        $this->assertEquals(false, $sort->isApplied());
        $this->assertEquals('https://example.org/?sort[clicks]=asc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('clicks', 'desc'));
        $this->assertEquals(true, $sort->isApplied());
        $this->assertEquals('https://example.org/', stringify_uri($sort->getToggleUri()));
    }

    /**
     * @test
     */
    public function sorts_can_be_combined_when_multisort_is_enabled()
    {
        $factory = new SortComponentFactory([
            SortComponentFactory::OPT_AVAILABLE_SORTS => [
                'hits' => ['asc', 'desc'],
                'clicks' => ['asc', 'desc'],
            ],
            SortComponentFactory::OPT_ENABLE_MULTISORT => true,
        ]);

        $uri = uri('https://example.org/?sort[clicks]=desc');
        $component = $factory->createComponent($uri);

        /** @var Sort $sort */
        $sort = first_of($component->get('hits', 'asc'));
        $this->assertEquals(false, $sort->isApplied());
        $this->assertEquals('https://example.org/?sort[clicks]=desc&sort[hits]=asc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('hits', 'desc'));
        $this->assertEquals(false, $sort->isApplied());
        $this->assertEquals('https://example.org/?sort[clicks]=desc&sort[hits]=desc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('clicks', 'asc'));
        $this->assertEquals(false, $sort->isApplied());
        $this->assertEquals('https://example.org/?sort[clicks]=asc', stringify_uri($sort->getToggleUri()));

        $sort = first_of($component->get('clicks', 'desc'));
        $this->assertEquals(true, $sort->isApplied());
        $this->assertEquals('https://example.org/', stringify_uri($sort->getToggleUri()));
    }

}
