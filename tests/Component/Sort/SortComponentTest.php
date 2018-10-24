<?php

namespace BenTools\OpenCubes\Tests\Component\Sort;

use BenTools\OpenCubes\Component\Sort\Sort;
use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\Component\Sort\SortInterface;
use PHPUnit\Framework\TestCase;

class SortComponentTest extends TestCase
{

    public function testWithSort()
    {
        $component = new SortComponent();
        $component->add(new Sort('foo'));
        $this->assertCount(1, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertInstanceOf(SortInterface::class, $component->get('foo'));
        $this->assertFalse($component->has('bar'));
        $this->assertNull($component->get('bar'));
    }

    public function testWithAddedSort()
    {
        $component = new SortComponent([new Sort('bar')]);
        $component->add(new Sort('foo'));
        $this->assertCount(2, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertTrue($component->has('bar'));
    }

    public function testWithoutSort()
    {
        $component = new SortComponent([new Sort('foo'), new Sort('bar')]);
        $component->remove(new Sort('foo'));
        $this->assertCount(1, $component);
        $this->assertFalse($component->has('foo'));
        $this->assertTrue($component->has('bar'));
    }

    public function testgets()
    {
        $sorts = [
            new Sort('foo'),
            new Sort('bar')
        ];

        $component = new SortComponent($sorts);
        $this->assertEquals(array_combine(['foo', 'bar'], $sorts), $component->all());
    }

}
