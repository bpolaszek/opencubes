<?php

namespace BenTools\OpenCubes\Tests\Component\Sort;

use BenTools\OpenCubes\Component\Sort\Sort;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{

    public function testGetField()
    {
        $sorting = new Sort('foo');
        $this->assertEquals('foo', $sorting->getField());
    }

    public function testIsAsc()
    {
        $sorting = new Sort('foo');
        $this->assertTrue($sorting->isAsc());
        $this->assertFalse($sorting->isDesc());

        $sorting = new Sort('foo', Sort::SORT_ASC);
        $this->assertTrue($sorting->isAsc());
        $this->assertFalse($sorting->isDesc());
    }

    public function testIsDesc()
    {
        $sorting = new Sort('foo', Sort::SORT_DESC);
        $this->assertFalse($sorting->isAsc());
        $this->assertTrue($sorting->isDesc());
    }

    public function testInvert()
    {
        $sorting = new Sort('foo', Sort::SORT_ASC);
        $invert = $sorting->invert();
        $this->assertNotSame($sorting, $invert);
        $this->assertFalse($invert->isAsc());
        $this->assertTrue($invert->isDesc());
    }
}
