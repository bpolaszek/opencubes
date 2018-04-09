<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\RangeFilter;
use PHPUnit\Framework\TestCase;

class RangeFilterTest extends TestCase
{

    public function testGetField()
    {
        $filter = new RangeFilter('foo', 0, 10);
        $this->assertEquals('foo', $filter->getField());
    }

    public function testGetLeft()
    {
        $filter = new RangeFilter('foo', 5, 10);
        $this->assertEquals(5, $filter->getLeft());
    }

    public function testGetRight()
    {

        $filter = new RangeFilter('foo', 5, 10);
        $this->assertEquals(10, $filter->getRight());
    }

    public function testIsInRange()
    {
        $filter = new RangeFilter('foo', 5, 10);
        $this->assertTrue($filter->isInRange(5));
        $this->assertTrue($filter->isInRange(7));
        $this->assertTrue($filter->isInRange(10));
        $this->assertFalse($filter->isInRange(4));
        $this->assertFalse($filter->isInRange(11));
    }

    public function testIsApplied()
    {
        $filter = new RangeFilter('foo', 5, 10);
        $this->assertTrue($filter->isApplied(5));
        $this->assertTrue($filter->isApplied(7));
        $this->assertTrue($filter->isApplied(10));
        $this->assertFalse($filter->isApplied(4));
        $this->assertFalse($filter->isApplied(11));
    }
}
