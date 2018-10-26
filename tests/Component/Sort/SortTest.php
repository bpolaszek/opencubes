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
}
