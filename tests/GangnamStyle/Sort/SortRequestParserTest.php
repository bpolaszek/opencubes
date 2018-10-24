<?php

namespace BenTools\OpenCubes\Tests\GangnamStyle\Sort;

use BenTools\OpenCubes\Component\Sort\SortComponent;
use BenTools\OpenCubes\GangnamStyle\Sort\SortRequestParser;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\uri_for;

class SortRequestParserTest extends TestCase
{

    public function testParseRequest()
    {
        $request = new Request('GET', uri_for('http://localhost/?foo=bar&o[foo]=asc&o[bar]=desc'));
        $parser = new SortRequestParser('o');

        $component = $parser->parseRequest($request);
        $this->assertCount(2, $component);
        $this->assertTrue($component->has('foo'));
        $this->assertTrue($component->has('bar'));
        $this->assertTrue($component->get('foo')->isAsc());
        $this->assertTrue($component->get('bar')->isDesc());
    }

    public function testSupportsComponent()
    {
        $parser = new SortRequestParser('o');
        $this->assertTrue($parser->supportsComponent(new SortComponent()));
    }
}
