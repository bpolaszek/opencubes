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
        $component->add(new Dimension('foo'));
        $this->assertCount(1, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertInstanceOf(DimensionInterface::class, $component->get('foo'));
        $this->assertFalse($component->has('bar'));
        $this->assertNull($component->get('bar'));
    }

    public function testAdd()
    {
        $component = new DrilldownComponent([new Dimension('bar')]);
        $component->add(new Dimension('foo'));
        $this->assertCount(2, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertTrue($component->has('bar'));
    }

    public function testWithoutDimension()
    {
        $component = new DrilldownComponent([
            new Dimension('foo'),
            new Dimension('bar')
        ]);
        $component->remove(new Dimension('foo'));
        $this->assertCount(1, $component);
        $this->assertFalse($component->has('foo'));
        $this->assertTrue($component->has('bar'));
    }

    public function testgets()
    {
        $dimensions = [
            new Dimension('foo'),
            new Dimension('bar')
        ];

        $component = new DrilldownComponent($dimensions);
        $this->assertEquals(array_combine(['foo', 'bar'], $dimensions), $component->all());
    }
    
}
