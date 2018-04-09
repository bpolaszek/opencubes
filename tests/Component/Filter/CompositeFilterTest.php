<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\CompositeFilter;
use BenTools\OpenCubes\Component\Filter\CompositeFilterInterface;
use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use PHPUnit\Framework\TestCase;

class CompositeFilterTest extends TestCase
{

    public function testGetField()
    {
        $filter = new CompositeFilter('foo', []);
        $this->assertEquals('foo', $filter->getField());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFilters()
    {
        new CompositeFilter('foo', [
            new SimpleFilter('foo', 'bar'),
            new SimpleFilter('baz', 'bar'),
        ]);
    }

    public function testGetFilters()
    {
        $filters = [
            new SimpleFilter('foo', 'bar'),
            new SimpleFilter('foo', 'baz'),
        ];
        $filter = new CompositeFilter('foo', $filters);
        $this->assertEquals($filters, $filter->getFilters());
    }

    public function testIsApplied()
    {
        $filters = [
            new SimpleFilter('foo', 'bar'),
            new SimpleFilter('foo', 'baz'),
        ];
        $filter = new CompositeFilter('foo', $filters);
        $this->assertEquals($filters, $filter->getFilters());
    }

    public function testCount()
    {
        $filters = [
            new SimpleFilter('foo', 'bar'),
            new SimpleFilter('foo', 'baz'),
        ];
        $filter = new CompositeFilter('foo', $filters);
        $this->assertCount(2, $filter);
    }

    public function testGetOperator()
    {
        $filter = new CompositeFilter('foo', [], 'OR');
        $this->assertEquals(CompositeFilterInterface::OR, $filter->getOperator());
    }
}
