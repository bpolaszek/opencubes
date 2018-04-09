<?php

namespace BenTools\OpenCubes\Tests\Component\Drilldown;

use BenTools\OpenCubes\Component\Drilldown\Dimension;
use PHPUnit\Framework\TestCase;

class DimensionTest extends TestCase
{

    public function testGetField()
    {
        $dimension = new Dimension('foo');
        $this->assertEquals('foo', $dimension->getField());
    }
}
