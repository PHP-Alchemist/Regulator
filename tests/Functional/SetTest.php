<?php

namespace Ruler\Test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ruler\Context;
use Ruler\RuleBuilder;

class SetTest extends TestCase
{
    public function testComplicated()
    {
        $rb = new RuleBuilder();
        $context = new Context([
            'expected' => 'a',
            'foo'      => ['a', 'z'],
            'bar'      => ['z', 'b'],
            'baz'      => ['a', 'z', 'b', 'q'],
            'bob'      => ['a', 'd'],
        ]);

        $this->assertTrue(
            $rb->create(
                $rb['foo']->intersect(
                    $rb['bar']->symmetricDifference($rb['baz'])
                )->setContains($rb['expected'])
            )->evaluate($context)
        );

        $this->assertTrue(
            $rb->create(
                $rb['bar']->union(
                    $rb['bob']
                )->containsSubset($rb['foo'])
            )->evaluate($context)
        );
    }

    public static function setUnion()
    {
        return [
            [
                ['a', 'b', 'c'],
                [],
                ['a', 'b', 'c'],
            ],
            [
                [],
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
            ],
            [
                [],
                [],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['d', 'e', 'f'],
                ['a', 'b', 'c', 'd', 'e', 'f'],
            ],
            [
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
            ],
            [
                ['a', 'b', 'c'],
                ['b', 'c'],
                ['a', 'b', 'c'],
            ],
            [
                ['b', 'c'],
                ['b', 'd'],
                ['b', 'c', 'd'],
            ],
        ];
    }

    #[DataProvider('setUnion')]
    public function testUnion($a, $b, $expected)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('a', 'b', 'expected'));
        $this->assertTrue(
            $rb->create(
                $rb['expected']->equalTo(
                    $rb['a']->union($rb['b'])
                )
            )->evaluate($context)
        );
    }

    public static function setIntersect()
    {
        return [
            [
                ['a', 'b', 'c'],
                [],
                [],
            ],
            [
                [],
                ['a', 'b', 'c'],
                [],
            ],
            [
                [],
                [],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['d', 'e', 'f'],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
            ],
            [
                ['a', 'b', 'c'],
                ['b', 'c'],
                ['b', 'c'],
            ],
            [
                ['b', 'c'],
                ['b', 'd'],
                ['b'],
            ],
        ];
    }

    #[DataProvider('setIntersect')]
    public function testIntersect($a, $b, $expected)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('a', 'b', 'expected'));
        $this->assertTrue(
            $rb->create(
                $rb['expected']->equalTo(
                    $rb['a']->intersect($rb['b'])
                )
            )->evaluate($context)
        );
    }

    public static function setComplement()
    {
        return [
            [
                ['a', 'b', 'c'],
                [],
                ['a', 'b', 'c'],
            ],
            [
                [],
                ['a', 'b', 'c'],
                [],
            ],
            [
                [],
                [],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['d', 'e', 'f'],
                ['a', 'b', 'c'],
            ],
            [
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['b', 'c'],
                ['a'],
            ],
            [
                ['b', 'c'],
                ['b', 'd'],
                ['c'],
            ],
        ];
    }

    #[DataProvider('setComplement')]
    public function testComplement($a, $b, $expected)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('a', 'b', 'expected'));
        $this->assertTrue(
            $rb->create(
                $rb['expected']->equalTo(
                    $rb['a']->complement($rb['b'])
                )
            )->evaluate($context)
        );
    }

    public static function setSymmetricDifference()
    {
        return [
            [
                ['a', 'b', 'c'],
                [],
                ['a', 'b', 'c'],
            ],
            [
                [],
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
            ],
            [
                [],
                [],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['d', 'e', 'f'],
                ['a', 'b', 'c', 'd', 'e', 'f'],
            ],
            [
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [],
            ],
            [
                ['a', 'b', 'c'],
                ['b', 'c'],
                ['a'],
            ],
            [
                ['b', 'c'],
                ['b', 'd'],
                ['c', 'd'],
            ],
        ];
    }

    #[DataProvider('setSymmetricDifference')]
    public function testSymmetricDifference($a, $b, $expected)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('a', 'b', 'expected'));
        $this->assertTrue(
            $rb->create(
                $rb['expected']->equalTo(
                    $rb['a']->symmetricDifference($rb['b'])
                )
            )->evaluate($context)
        );
    }
}
