<?php

namespace Ruler\Test\Operator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ruler\Context;
use Ruler\Operator;
use Ruler\Variable;

class DivisionTest extends TestCase
{
    public function testInterface()
    {
        $varA = new Variable('a', 1);
        $varB = new Variable('b', [2]);

        $op = new Operator\Division($varA, $varB);
        $this->assertInstanceOf(\Ruler\VariableOperand::class, $op);
    }

    public function testInvalidData()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Arithmetic: values must be numeric');
        $varA    = new Variable('a', 'string');
        $varB    = new Variable('b', 'blah');
        $context = new Context();

        $op = new Operator\Division($varA, $varB);
        $op->prepareValue($context);
    }

    public function testDivideByZero()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Division by zero');
        $varA    = new Variable('a', random_int(1, 100));
        $varB    = new Variable('b', 0);
        $context = new Context();

        $op = new Operator\Division($varA, $varB);
        $op->prepareValue($context);
    }

    #[DataProvider('divisionData')]
    public function testDivision($a, $b, $result)
    {
        $varA    = new Variable('a', $a);
        $varB    = new Variable('b', $b);
        $context = new Context();

        $op = new Operator\Division($varA, $varB);
        $this->assertEquals($op->prepareValue($context)->getValue(), $result);
    }

    public static function divisionData()
    {
        return [
            [6, 2, 3],
            [7.5, 2.5, 3.0],
        ];
    }
}
