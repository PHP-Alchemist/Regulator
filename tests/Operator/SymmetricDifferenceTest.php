<?php

namespace Regulator\Test\Operator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Regulator\Context;
use Regulator\Operator;
use Regulator\Variable;

class SymmetricDifferenceTest extends TestCase
{
    public function testInterface()
    {
        $varA = new Variable('a', 1);
        $varB = new Variable('b', [2]);

        $op = new Operator\SymmetricDifference($varA, $varB);
        $this->assertInstanceOf(\Regulator\VariableOperand::class, $op);
    }

    public function testInvalidData()
    {
        $varA    = new Variable('a', 'string');
        $varB    = new Variable('b', 'blah');
        $context = new Context();

        $op = new Operator\SymmetricDifference($varA, $varB);
        $this->assertEquals(
            ['string', 'blah'],
            $op->prepareValue($context)->getValue()
        );
    }

    #[DataProvider('symmetricDifferenceData')]
    public function testSymmetricDifference($a, $b, $result)
    {
        $varA    = new Variable('a', $a);
        $varB    = new Variable('b', $b);
        $context = new Context();

        $op = new Operator\SymmetricDifference($varA, $varB);
        $this->assertEquals(
            $result,
            $op->prepareValue($context)->getValue()
        );
    }

    public static function symmetricDifferenceData()
    {
        return [
            [6, 2, [6, 2]],
            [
                ['a', 'b', 'c'],
                'a',
                ['b', 'c'],
            ],
            [
                'a',
                ['a', 'b', 'c'],
                ['b', 'c'],
            ],
            [
                'a',
                ['b', 'c'],
                ['a', 'b', 'c'],
            ],
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
}
