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
        $clone = $component->withSort(new Sort('foo'));
        $this->assertNotSame($component, $clone);
        $this->assertCount(1, $clone);
        $this->assertTrue($clone->hasSort('foo'));
        $this->assertInstanceOf(SortInterface::class, $clone->getSort('foo'));
        $this->assertFalse($clone->hasSort('bar'));
        $this->assertNull($clone->getSort('bar'));
    }

    public function testWithAddedSort()
    {
        $component = new SortComponent([new Sort('bar')]);
        $clone = $component->withAddedSort(new Sort('foo'));
        $this->assertNotSame($component, $clone);
        $this->assertCount(2, $clone);
        $this->assertTrue($clone->hasSort('foo'));
        $this->assertTrue($clone->hasSort('bar'));
    }

    public function testWithoutSort()
    {
        $component = new SortComponent([new Sort('foo'), new Sort('bar')]);
        $clone = $component->withoutSort(new Sort('foo'));
        $this->assertNotSame($component, $clone);
        $this->assertCount(1, $clone);
        $this->assertFalse($clone->hasSort('foo'));
        $this->assertTrue($clone->hasSort('bar'));
    }

    public function testGetSorts()
    {
        $sorts = [
            new Sort('foo'),
            new Sort('bar')
        ];

        $component = new SortComponent($sorts);
        $this->assertEquals(array_combine(['foo', 'bar'], $sorts), $component->getSorts());
    }

}
