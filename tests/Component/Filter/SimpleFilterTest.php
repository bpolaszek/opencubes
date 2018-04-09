<?php

namespace BenTools\OpenCubes\Tests\Component\Filter;

use BenTools\OpenCubes\Component\Filter\SimpleFilter;
use PHPUnit\Framework\TestCase;

class SimpleFilterTest extends TestCase
{

    public function testGetField()
    {
        $filter = new SimpleFilter('foo', null);
        $this->assertEquals('foo', $filter->getField());
    }

    public function testGetValue()
    {
        $filter = new SimpleFilter('foo', 'bar');
        $this->assertEquals('bar', $filter->getValue());

        $filter = new SimpleFilter('foo', null);
        $this->assertNull($filter->getValue());

    }

    public function testIsApplied()
    {
        $filter = new SimpleFilter('foo', 'bar');
        $this->assertTrue($filter->isApplied('bar'));
        $this->assertFalse($filter->isApplied('foo'));

        $filter = new SimpleFilter('foo', null);
        $this->assertTrue($filter->isApplied(null));
        $this->assertFalse($filter->isApplied('foo'));
    }
}
