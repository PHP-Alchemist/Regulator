<?php

namespace Ruler\Test\Operator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ruler\Context;
use Ruler\Operator;
use Ruler\Variable;

class ComplementTest extends TestCase
{
    public function testInterface()
    {
        $varA = new Variable('a', 1);
        $varB = new Variable('b', [2]);

        $op = new Operator\Complement($varA, $varB);
        $this->assertInstanceOf(\Ruler\VariableOperand::class, $op);
    }

    public function testInvalidData()
    {
        $varA    = new Variable('a', 'string');
        $varB    = new Variable('b', 'blah');
        $context = new Context();

        $op = new Operator\Complement($varA, $varB);
        $this->assertEquals(
            ['string'],
            $op->prepareValue($context)->getValue()
        );
    }

    #[DataProvider('complementData')]
    public function testComplement($a, $b, $result)
    {
        $varA    = new Variable('a', $a);
        $varB    = new Variable('b', $b);
        $context = new Context();

        $op = new Operator\Complement($varA, $varB);
        $this->assertEquals(
            $result,
            $op->prepareValue($context)->getValue()
        );
    }

    public static function complementData()
    {
        return [
            [6, 2, [6]],
            [
                ['a', 'b', 'c'],
                'a',
                ['b', 'c'],
            ],
            [
                'a',
                ['a', 'b', 'c'],
                [],
            ],
            [
                'a',
                ['b', 'c'],
                ['a'],
            ],
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
}
