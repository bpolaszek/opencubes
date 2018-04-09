<?php

namespace BenTools\OpenCubes\Tests\Component\Drilldown;

use BenTools\OpenCubes\Component\Drilldown\Dimension;
use BenTools\OpenCubes\Component\Drilldown\DimensionInterface;
use BenTools\OpenCubes\Component\Drilldown\DrilldownComponent;
use PHPUnit\Framework\TestCase;

class DrilldownComponentTest extends TestCase
{

    public function testWithDimension()
    {
        $component = new DrilldownComponent();
        $clone = $component->withDimension(new Dimension('foo'));
        $this->assertNotSame($component, $clone);
        $this->assertCount(1, $clone);
        $this->assertTrue($clone->hasDimension('foo'));
        $this->assertInstanceOf(DimensionInterface::class, $clone->getDimension('foo'));
        $this->assertFalse($clone->hasDimension('bar'));
        $this->assertNull($clone->getDimension('bar'));
    }

    public function testWithAddedDimension()
    {
        $component = new DrilldownComponent([new Dimension('bar')]);
        $clone = $component->withAddedDimension(new Dimension('foo'));
        $this->assertNotSame($component, $clone);
        $this->assertCount(2, $clone);
        $this->assertTrue($clone->hasDimension('foo'));
        $this->assertTrue($clone->hasDimension('bar'));
    }

    public function testWithoutDimension()
    {
        $component = new DrilldownComponent([
            new Dimension('foo'),
            new Dimension('bar')
        ]);
        $clone = $component->withoutDimension(new Dimension('foo'));
        $this->assertNotSame($component, $clone);
        $this->assertCount(1, $clone);
        $this->assertFalse($clone->hasDimension('foo'));
        $this->assertTrue($clone->hasDimension('bar'));
    }

    public function testGetDimensions()
    {
        $dimensions = [
            new Dimension('foo'),
            new Dimension('bar')
        ];

        $component = new DrilldownComponent($dimensions);
        $this->assertEquals(array_combine(['foo', 'bar'], $dimensions), $component->getDimensions());
    }
    
}
