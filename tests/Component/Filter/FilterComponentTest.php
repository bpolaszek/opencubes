<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use PHPUnit\Framework\TestCase;

class FilterComponentTest extends TestCase
{

    public function testWithFilter()
    {
        $component = new FilterComponent(new SimpleFilter('foo', 'bar'));
        $clone = $component->withFilter(new SimpleFilter('bar', 'foo'));
        $this->assertNotSame($clone, $component);
        $this->assertCount(1, $clone);
        $this->assertFalse($clone->hasFilter('foo'));
        $this->assertTrue($clone->hasFilter('bar'));
        $this->assertInstanceOf(SimpleFilter::class, $clone->getFilter('bar'));
    }

    public function testWithAddedFilter()
    {
        $component = new FilterComponent(new SimpleFilter('foo', 'bar'));
        $clone = $component->withAddedFilter(new SimpleFilter('bar', 'foo'));
        $this->assertNotSame($clone, $component);
        $this->assertCount(2, $clone);
        $this->assertTrue($clone->hasFilter('foo'));
        $this->assertTrue($clone->hasFilter('bar'));
    }

    public function testWithoutFilter()
    {
        $component = new FilterComponent(new SimpleFilter('foo', 'bar'), new SimpleFilter('bar', 'foo'));
        $clone = $component->withoutFilter(new SimpleFilter('bar', 'foo'));
        $this->assertNotSame($clone, $component);
        $this->assertCount(1, $clone);
        $this->assertFalse($clone->hasFilter('bar'));
        $this->assertTrue($clone->hasFilter('foo'));
    }

    public function testGetFilters()
    {
        $filters = [
            new SimpleFilter('color', 'red'),
            new SimpleFilter('shape', 'round'),
        ];
        $component = new FilterComponent(...$filters);
        $expected = array_combine(['color', 'shape'], $filters);
        $this->assertEquals($expected, $component->getFilters());
    }

    public function testGetFilter()
    {
        $filter = new SimpleFilter('foo', 'bar');
        $component = new FilterComponent($filter);
        $this->assertEquals($filter, $component->getFilter('foo'));
    }

    public function testHasFilter()
    {
        $component = new FilterComponent(new SimpleFilter('foo', 'bar'));
        $this->assertTrue($component->hasFilter('foo'));
    }

    public function testIsFilterApplied()
    {
        $component = new FilterComponent(new SimpleFilter('foo', 'bar'));
        $this->assertTrue($component->isFilterApplied('foo'));
        $this->assertFalse($component->isFilterApplied('bar'));
        $this->assertTrue($component->isFilterApplied('foo', 'bar'));
        $this->assertFalse($component->isFilterApplied('foo', 'baz'));
        $this->assertFalse($component->isFilterApplied('foo', null));
    }
}
