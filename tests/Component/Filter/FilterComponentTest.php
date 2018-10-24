<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\FilterComponent;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use PHPUnit\Framework\TestCase;

class FilterComponentTest extends TestCase
{

    public function testConstruct()
    {
        $component = new FilterComponent([new SimpleFilter('foo', 'bar')]);
        $this->assertCount(1, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertFalse($component->has('bar'));
        $this->assertInstanceOf(SimpleFilter::class, $component->get('foo'));
        $this->assertNull($component->get('bar'));
    }

    public function testaddFilter()
    {
        $component = new FilterComponent([new SimpleFilter('foo', 'bar')]);
        $component->add(new SimpleFilter('bar', 'foo'));
        $this->assertCount(2, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertTrue($component->has('bar'));
    }

    public function testWithoutFilter()
    {
        $component = new FilterComponent([
            new SimpleFilter('foo', 'bar'),
            new SimpleFilter('bar', 'foo'),
        ]);
        $component->remove(new SimpleFilter('bar', 'foo'));
        $this->assertCount(1, $component);
        $this->assertFalse($component->has('bar'));
        $this->assertTrue($component->has('foo'));
    }

    public function testAll()
    {
        $filters = [
            new SimpleFilter('color', 'red'),
            new SimpleFilter('shape', 'round'),
        ];
        $component = new FilterComponent($filters);
        $expected = array_combine(['color', 'shape'], $filters);
        $this->assertEquals($expected, $component->all());
    }

    public function testget()
    {
        $filter = new SimpleFilter('foo', 'bar');
        $component = new FilterComponent([$filter]);
        $this->assertEquals($filter, $component->get('foo'));
    }

    public function testhas()
    {
        $component = new FilterComponent([new SimpleFilter('foo', 'bar')]);
        $this->assertTrue($component->has('foo'));
    }
}
